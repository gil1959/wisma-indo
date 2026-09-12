<?php
namespace App\Services\Mobile;

class PanelFields
{
    public static function forAction(string $class, string $method): array
    {
        $reflection = new \ReflectionMethod($class, $method);
        $lines = file($reflection->getFileName());
        $source = implode('', array_slice($lines, $reflection->getStartLine()-1, $reflection->getEndLine()-$reflection->getStartLine()+1));
        $position = strpos($source, '->validate(');
        $fields = [];
        if ($position !== false) {
            $rules = substr($source, $position);
            $end = strpos($rules, ']);');
            // Listing update has separate moderation and full editor validation blocks.
            if ($end !== false && !($method==='update' && str_ends_with($class,'Admin\\ListingController'))) $rules=substr($rules,0,$end);
            preg_match_all("/'([^']+)'\\s*=>\\s*(?:'([^']*)'|\\[([^\\]]*)\\])/", $rules, $matches, PREG_SET_ORDER);
            foreach($matches as $match) {
                $key=$match[1]; if(strpos($key,'.')!==false || in_array($key,['latitude','longitude'])) continue;
                preg_match_all("/'([^']*)'/", $match[3]??'', $arrayRules);
                $rule=$match[2] ?: implode('|', $arrayRules[1]);
                if(!preg_match('/^(required|nullable|boolean|string|integer|numeric)/',$rule)) continue;
                $type = strpos($rule,'boolean')!==false || in_array($key,['is_enabled','show_on_mobile','show_on_desktop']) ? 'boolean' : (strpos($rule,'image')!==false || strpos($rule,'file')!==false ? 'file' : (strpos($rule,'array')!==false ? 'array' : (strpos($rule,'integer')!==false || strpos($rule,'numeric')!==false ? 'number' : 'text')));
                if (preg_match('/password|secret|api_key|private_key|callback_token/', $key)) $type='password';
                if (preg_match('/description|content|body_html|body_text|message|note|template|address|benefits|_desc|_text|script|urls/', $key) && $type==='text') $type='multiline';
                if(in_array($key,['content','description','body_html'])) $type='richtext';
                if($key==='maps_url') $type='map';
                $options=[];
                if(preg_match('/(?:^|[|])in:([^|]+)/',$rule,$optionMatch)) { $options=explode(',',$optionMatch[1]); $type='select'; }
                $fields[$key]=['key'=>$key,'label'=>self::label($key),'type'=>$type,'required'=>in_array('required',explode('|',$rule)),'options'=>$options];
                if(strpos($rule,'confirmed')!==false) $fields[$key.'_confirmation']=['key'=>$key.'_confirmation','label'=>'Konfirmasi password','type'=>'password','required'=>true,'options'=>[]];
            }
        }
        if ($method==='reject' && !$fields) $fields['reason']=['key'=>'reason','label'=>'Alasan penolakan','type'=>'multiline','required'=>true,'options'=>[]];
        if ($method==='addLeadActivity') { $fields['status']['type']='select'; $fields['status']['options']=['Lead Baru','Follow Up','Survey','Negosiasi','Booking Fee','Approved','Akad','Closing']; }
        if(isset($fields['target'])) { $fields['target']['type']='select'; $fields['target']['options']=['all','specific']; }
        if(str_ends_with($class,'Admin\\ListingController') && in_array($method,['store','update'])) {
            $fields['images']=['key'=>'images','label'=>'Foto tambahan','type'=>'files','required'=>false,'options'=>[]];
            if($method==='update') $fields['delete_images']=['key'=>'delete_images','label'=>'Hapus foto yang dipilih','type'=>'multiselect','required'=>false,'options'=>[]];
        }
        foreach($fields as $key=>&$field) {
            $model = ['listing_category_id'=>'ListingCategory','category_id'=>'ArticleCategory','listing_id'=>'Listing','partner_id'=>'User','user_ids'=>'User','package_id'=>'PartnerPackage'][$key]??null;
            if($model) {
                $query=('App\\Models\\'.$model)::query();
                if($key==='partner_id') $query->role('partner');
                if($key==='package_id') $query->where('is_free',false)->where('is_active',true);
                $field['options']=$query->limit(500)->get()->map(function($row)use($key){
                    $label=$row->name??$row->title??$row->email;
                    if($key==='package_id') $label.=' | Rp '.number_format($row->price,0,',','.').' | '.$row->duration_days.' hari | '.((int)$row->listing_quota===-1?'Iklan tanpa batas':$row->listing_quota.' iklan');
                    return ['value'=>(string)$row->id,'label'=>$label];
                })->all();
                $field['type']=$key==='user_ids'?'multiselect':'select';
            }
            if($key==='permissions') { $field['type']='multiselect'; $field['options']=collect(config('admin_permissions',[]))->map(function($v,$k){return ['value'=>$k,'label'=>$v['label']??$k];})->values()->all(); }
            if($key==='payment_method' && $method==='purchase') { $field['type']='select'; $field['options']=\App\Http\Controllers\Api\V1\NativePartnerBillingController::paymentOptions(); }
        }
        unset($field);
        return array_values($fields);
    }
    public static function label(string $key): string
    {
        return [
            'name'=>'Nama','title'=>'Judul','email'=>'Email','phone'=>'Nomor HP','password'=>'Password','current_password'=>'Password sekarang','password_confirmation'=>'Konfirmasi password','package_id'=>'Paket partner','payment_method'=>'Metode pembayaran','payment_proof'=>'Bukti transfer','duration'=>'Durasi penangguhan dalam hari',
            'description'=>'Deskripsi','content'=>'Konten','price'=>'Harga','amount'=>'Jumlah','bonus'=>'Bonus','is_active'=>'Aktif','is_verified'=>'Email terverifikasi','role'=>'Peran','permissions'=>'Hak akses',
            'address'=>'Alamat','full_address'=>'Alamat lengkap','sub_district'=>'Kecamatan','listing_quota'=>'Kuota iklan','duration_days'=>'Durasi dalam hari','discount_label'=>'Label diskon','original_price'=>'Harga awal','valid_until'=>'Berlaku sampai','is_voucher'=>'Voucher','button_text'=>'Teks tombol','benefits'=>'Manfaat',
            'status'=>'Status','note'=>'Catatan','reason'=>'Alasan','rejection_note'=>'Alasan penolakan','image'=>'Gambar','photo'=>'Foto','avatar'=>'Foto profil','cover_image'=>'Foto utama','seo_image'=>'Gambar SEO','type'=>'Tipe','order'=>'Urutan','url'=>'Tautan','label'=>'Label','subtitle'=>'Subjudul','rating'=>'Penilaian',
            'listing_category_id'=>'Kategori iklan','category_id'=>'Kategori artikel','partner_id'=>'Partner penerima','listing_id'=>'Iklan terkait','user_ids'=>'Penerima','target'=>'Tujuan','message'=>'Pesan','is_published'=>'Terbitkan','whatsapp_template'=>'Template WhatsApp','body_format'=>'Format isi','body_html'=>'Isi HTML','body_text'=>'Isi teks','include_paths_text'=>'Halaman yang ditampilkan','exclude_paths_text'=>'Halaman yang dikecualikan','delay_seconds'=>'Jeda dalam detik','frequency'=>'Frekuensi','start_at'=>'Mulai','end_at'=>'Selesai','is_enabled'=>'Aktifkan popup','show_on_mobile'=>'Tampilkan di ponsel','show_on_desktop'=>'Tampilkan di desktop',
        ][$key]??ucwords(str_replace('_',' ',$key));
    }
}
