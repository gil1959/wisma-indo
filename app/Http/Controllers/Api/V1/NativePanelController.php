<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Mobile\{PanelRegistry,PanelFields};
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class NativePanelController extends Controller
{
    private function authorizeArea(Request $request, string $area): void
    {
        $user=$request->user();
        abort_if(($user->is_suspended && !$user->suspended_until) || ($user->suspended_until && $user->suspended_until->isFuture()),403,'Akun sedang ditangguhkan.');
        abort_unless(in_array($area,['admin','partner']),404);
        abort_unless($area==='admin' ? $request->user()->hasAnyRole(['admin','site_moderator']) : $request->user()->hasRole('partner'),403);
    }
    private function module(Request $request,string $area,string $key): array
    {
        $this->authorizeArea($request,$area);
        $module=PanelRegistry::modules($area)[$key]??null; abort_unless($module,404);
        if($area==='partner' && ($module['premium']??false)) {
            $active=\App\Models\PartnerSubscription::where('user_id',$request->user()->id)->where('status','active')->where('ends_at','>',now())->whereHas('package',function($q){$q->where('is_free',false);})->exists();
            abort_unless($active,403,'Silakan berlangganan Paket Partner untuk membuka fitur ini.');
        }
        return $module;
    }
    public function modules(Request $request,string $area)
    {
        $this->authorizeArea($request,$area);
        return response()->json(['success'=>true,'data'=>collect(PanelRegistry::modules($area))->map(function($v,$k){return ['key'=>$k,'title'=>$v['title']];})->values()]);
    }
    public function document(Request $request, $id, string $field)
    {
        $this->authorizeArea($request,'admin');
        abort_unless(in_array($field,['ktp_file','nib_file','npwp_file','lisensi_file','foto_file']),404);
        $registration=\App\Models\PartnerRegistration::findOrFail($id);
        $relative=ltrim((string)$registration->$field,'/');
        abort_unless(str_starts_with($relative,'storage/partners/'),404);
        $root=realpath(storage_path('app/public/partners'));
        $path=realpath(storage_path('app/public/'.substr($relative,8)));
        abort_unless($root && $path && str_starts_with($path,$root.DIRECTORY_SEPARATOR) && is_file($path),404);
        return response()->file($path,['Cache-Control'=>'private, no-store']);
    }
    private function invoke(Request $request,array $module,string $method,$id=null)
    {
        $class='App\\Http\\Controllers\\'.$module['controller'];
        $reflection=new \ReflectionMethod($class,$method); $args=[];
        foreach($reflection->getParameters() as $parameter) {
            $type=$parameter->getType(); $typeName=$type instanceof \ReflectionNamedType ? $type->getName() : null;
            if($typeName && is_a($typeName,Request::class,true)) $args[]=$request;
            elseif($typeName && is_a($typeName,Model::class,true)) { abort_unless($id,422); $args[]=$typeName::query()->lockForUpdate()->findOrFail($id); }
            elseif($id!==null) $args[]=$id;
            elseif($parameter->isDefaultValueAvailable()) $args[]=$parameter->getDefaultValue();
            else abort(422,'Pilih data terlebih dahulu.');
        }
        return app($class)->$method(...$args);
    }
    private function record(Request $request,string $area,array $module,$id)
    {
        abort_unless(isset($module['model']),404);
        $query=('App\\Models\\'.$module['model'])::query();
        if($area==='partner') $query->where($module['owner_key']??'partner_id',$request->user()->id);
        $record=$query->findOrFail($id);
        if($module['model']==='User') { $record->load('roles'); $record->setAttribute('role',$record->roles->first()->name??'user'); $record->setAttribute('is_verified',$record->hasVerifiedEmail()); $record->setAttribute('permissions',$record->getDirectPermissions()->pluck('name')); }
        if($module['model']==='Listing') $record->load('images');
        if($module['model']==='BuyerLead') $record->load(['listing','activities']);
        if(in_array($module['model'],['PartnerRegistration','PartnerSubscription','TopupTransaction','ListingTransaction'])) $record->load('user');
        if($module['model']==='PartnerSubscription') $record->load('package');
        return $record;
    }
    public function screen(Request $request,string $area,string $key)
    {
        $request->validate(['q'=>'nullable|string|max:255','status'=>'nullable|string|max:50','record'=>'nullable|integer|min:1','start_date'=>'nullable|date_format:Y-m-d','end_date'=>'nullable|date_format:Y-m-d|after_or_equal:start_date']);
        $module=$this->module($request,$area,$key);
        if($key==='kpr') return response()->json(['success'=>true,'data'=>['title'=>$module['title'],'content'=>['Informasi'=>'Segera Hadir. Fitur Pengajuan KPR sedang dalam tahap pengembangan.'],'actions'=>[]]]);
        $data=[]; $id=$request->query('record');
        if($id) {
            $data=['record'=>$this->record($request,$area,$module,$id)];
            if($area==='partner' && $key==='billing' && $data['record']->payment_method==='offline') $data['Rekening tujuan']=\App\Models\OfflinePaymentMethod::where('is_active',true)->get();
        }
        elseif(isset($module['read'])) {
            $view=$this->invoke($request,$module,$module['read']);
            if($view instanceof \Illuminate\View\View) $data=$view->getData();
        } elseif(isset($module['model'])) $data=['records'=>('App\\Models\\'.$module['model'])::latest()->paginate(20)];
        if(!$id && $request->filled('q') && isset($module['model'])) {
            $model=new ('App\\Models\\'.$module['model']);
            $columns=array_values(array_filter(['name','title','email','phone'],function($column)use($model){return \Illuminate\Support\Facades\Schema::hasColumn($model->getTable(),$column);}));
            if($columns) {
                $query=$model->newQuery()->where(function($q)use($columns,$request){foreach($columns as $column)$q->orWhere($column,'like','%'.$request->q.'%');});
                if($area==='partner') $query->where($module['owner_key']??'partner_id',$request->user()->id);
                if($key==='users') $query->whereDoesntHave('roles',function($q){$q->where('name','admin');});
                if($request->filled('status') && \Illuminate\Support\Facades\Schema::hasColumn($model->getTable(),'status')) $query->where('status',$request->status);
                foreach($data as $section=>$value) if($value instanceof \Illuminate\Contracts\Pagination\Paginator || $value instanceof \Illuminate\Support\Collection) { $data[$section]=$query->latest()->paginate(20); break; }
            }
        }
        $actions=[];
        foreach($module['actions']??[] as $action=>$definition) {
            if(($definition['record']??false) && !$id) continue;
            if(!($definition['record']??false) && $id) continue;
            $class='App\\Http\\Controllers\\'.$module['controller'];
            if(!method_exists($class,$definition['method'])) continue;
            $fields=PanelFields::forAction($class,$definition['method']);
            if($key==='listings' && $id) foreach($fields as &$field) if($field['key']==='delete_images') $field['options']=$data['record']->images->map(function($image,$index){return ['value'=>(string)$image->id,'label'=>'Foto '.($index+1)];})->all();
            unset($field);
            if($key==='listings' && $action==='status') $fields=[['key'=>'status','label'=>'Status','type'=>'select','required'=>true,'options'=>['pending','tersedia','terjual','tersewa','nonaktif','rejected']],['key'=>'rejection_note','label'=>'Alasan penolakan','type'=>'multiline','required'=>false,'options'=>[]]];
            $actions[]=['key'=>$action,'title'=>$definition['title'],'confirm'=>$definition['confirm']??false,'fields'=>$fields];
        }
        $values=[];
        if($id) $values=$data['record']->toArray();
        elseif($module['form']??false) {
            foreach($data as $v) if($v instanceof Model) { $values=$v->toArray(); break; }
            foreach($data as $k=>$v) if(is_scalar($v)||$v===null) $values[$k]=$v;
            if($module['settings']??false) $values=\App\Models\Setting::pluck('value','key')->all();
            if(isset($values['include_paths'])) $values['include_paths_text']=implode("\n",$values['include_paths']);
            if(isset($values['exclude_paths'])) $values['exclude_paths_text']=implode("\n",$values['exclude_paths']);
        }
        // Configuration screens expose only editable fields, never the full settings table or credentials in read cards.
        if($module['form']??false) {
            $keys=collect($actions)->flatMap(function($a){return array_column($a['fields'],'key');})->all();
            $values=array_intersect_key($values,array_flip($keys)); $data=[];
        }
        unset($data['settings'],$data['permissions'],$data['roles']);
        foreach($data as $section=>$value) {
            if($value instanceof \Illuminate\Pagination\LengthAwarePaginator) $value->setCollection($value->getCollection()->values());
            elseif($value instanceof \Illuminate\Support\Collection) $data[$section]=$value->values();
        }
        if($key==='google-indexing') {
            $overview=app(\App\Http\Controllers\Admin\SettingController::class)->general()->getData();
            $data=['Status koneksi'=>\App\Models\Setting::getValue('g_index_auth_mode','Belum terhubung'),'Statistik indexing'=>$overview['googleIndexingStats'],'Redirect URI untuk Google Cloud'=>url('/api/v1/auth/indexing-callback'),'Riwayat indexing'=>\App\Models\GoogleIndexingLog::latest()->paginate(20)];
        }
        $recordSections=[];
        if (!$id && isset($module['model'])) {
            $modelClass='App\\Models\\'.$module['model'];
            foreach ($data as $section=>$value) {
                $items=$value instanceof \Illuminate\Pagination\LengthAwarePaginator ? $value->getCollection() : $value;
                if ($items instanceof \Illuminate\Support\Collection && $items->first() instanceof $modelClass) $recordSections[]=$section;
            }
        }
        return response()->json(['success'=>true,'data'=>['title'=>$module['title'],'content'=>$data,'values'=>$values,'actions'=>$actions,'record_module'=>isset($module['model'])?$key:null,'record_sections'=>$recordSections,'form'=>$module['form']??false]]);
    }
    public function action(Request $request,string $area,string $key,string $action)
    {
        $module=$this->module($request,$area,$key); $definition=$module['actions'][$action]??null; abort_unless($definition,404);
        $id=$request->input('record');
        if($area==='admin' && $key==='google-indexing' && $action==='connect') return app(NativeGoogleIndexingController::class)->connect($request);
        if($definition['record']??false) $this->record($request,$area,$module,$id);
        if($area==='admin' && $key==='users' && $action==='impersonate') {
            $user=\App\Models\User::findOrFail($id);
            abort_if($user->hasRole('admin'),403,'Tidak bisa masuk sebagai sesama admin.');
            $token=$user->createToken('native-impersonation:'.$request->user()->id)->plainTextToken;
            return response()->json(['success'=>true,'message'=>'Sesi pengguna dimulai.','data'=>['access_token'=>$token]]);
        }
        if($request->filled('payload')) { $payload=json_decode($request->input('payload'),true); abort_unless(is_array($payload),422); unset($payload['record'],$payload['_method']); $request->merge($payload); }
        if($area==='admin' && $key==='listings' && $action==='status') $request->merge(['only_status'=>true]);
        if($area==='admin' && $key==='listings' && $request->filled('maps_url')) {
            parse_str(parse_url($request->maps_url,PHP_URL_QUERY)??'', $mapQuery);
            $point=explode(',',$mapQuery['q']??'');
            if(count($point)===2 && is_numeric($point[0]) && is_numeric($point[1])) $request->merge(['latitude'=>$point[0],'longitude'=>$point[1]]);
        }
        foreach(PanelFields::forAction('App\\Http\\Controllers\\'.$module['controller'],$definition['method']) as $field) {
            if($field['type']==='boolean' && !$request->boolean($field['key'])) $request->request->remove($field['key']);
        }
        $session=new \Illuminate\Session\Store('native-panel',new \Illuminate\Session\ArraySessionHandler(120));
        $session->start(); $request->setLaravelSession($session); app('redirect')->setSession($session);
        $response=DB::transaction(function() use($request,$module,$definition,$id) {
            if($id && isset($module['model'])) ('App\\Models\\'.$module['model'])::query()->lockForUpdate()->findOrFail($id);
            return $this->invoke($request,$module,$definition['method'],$id);
        });
        if($response instanceof \Illuminate\Http\JsonResponse) return $response;
        $error=$session->get('error');
        if(!$error && $session->has('errors')) $error=implode("\n",$session->get('errors')->all());
        return response()->json(['success'=>!$error,'message'=>$error?:$session->get('success','Perubahan berhasil disimpan.')],$error?422:200);
    }
}
