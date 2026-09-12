<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;

class NativePanelApiTest extends MobileAuthApiTest
{

    public function test_google_switch_exchange_binding_single_use_and_roles()
    {
        $this->getJson('/api/v1/auth/options')->assertOk()->assertJsonPath('data.google_enabled',false);
        $this->postJson('/api/v1/auth/google/start',['challenge'=>str_repeat('a',64)])->assertForbidden();
        \App\Models\Setting::create(['key'=>'google_login_active','value'=>'1']);
        $this->getJson('/api/v1/auth/options')->assertOk()->assertJsonPath('data.google_enabled',true);
        $user=User::create(['name'=>'Google Tester','email'=>'google@example.test','password'=>bcrypt('test'),'email_verified_at'=>now()]);$user->assignRole('partner');
        $code=str_repeat('c',64);$verifier=str_repeat('v',64);
        \Illuminate\Support\Facades\Cache::put('google-exchange:'.hash('sha256',$code),['id'=>$user->id,'challenge'=>hash('sha256',$verifier)],120);
        $this->postJson('/api/v1/auth/google/exchange',['code'=>$code,'verifier'=>str_repeat('x',64)])->assertForbidden();
        $this->postJson('/api/v1/auth/google/exchange',['code'=>$code,'verifier'=>$verifier])->assertOk()->assertJsonPath('data.user.roles.0','partner');
        $this->postJson('/api/v1/auth/google/exchange',['code'=>$code,'verifier'=>$verifier])->assertForbidden();
    }
    public function test_bulk_upload_owns_history_and_reserves_quota_without_running_jobs()
    {
        Schema::create('bulk_uploads',function(Blueprint $t){$t->id();$t->unsignedBigInteger('user_id');$t->string('type');$t->string('file_path');$t->string('status');$t->integer('total_rows');$t->integer('processed_rows')->default(0);$t->integer('failed_rows')->default(0);$t->text('error_log')->nullable();$t->timestamps();});
        \Illuminate\Support\Facades\Queue::fake();
        $user=User::create(['name'=>'Bulk Tester','email'=>'bulk@example.test','password'=>bcrypt('test'),'email_verified_at'=>now()]);$user->assignRole('user');Sanctum::actingAs($user);
        $user->quota()->create(['listing_quota'=>2]);
        $file=\Illuminate\Http\UploadedFile::fake()->createWithContent('iklan.csv',"Judul Iklan,Kategori\nRumah pertama,Rumah\nRumah kedua,Rumah\n");
        $this->postJson('/api/v1/user/bulk-uploads',['type'=>'property','file'=>$file])->assertOk();
        $this->assertSame(0,(int)$user->quota()->first()->listing_quota);
        $this->getJson('/api/v1/user/bulk-uploads')->assertOk()->assertJsonPath('data.uploads.total',1);
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\ProcessBulkUploadJob::class,1);
        $other=User::create(['name'=>'Other','email'=>'bulk-other@example.test','password'=>bcrypt('test'),'email_verified_at'=>now()]);$other->assignRole('partner');Sanctum::actingAs($other);
        $this->getJson('/api/v1/user/bulk-uploads')->assertOk()->assertJsonPath('data.uploads.total',0);
        $this->postJson('/api/v1/user/bulk-uploads',['type'=>'property','file'=>\Illuminate\Http\UploadedFile::fake()->createWithContent('iklan.csv',"Judul Iklan,Kategori\nRumah,Rumah\n")])->assertUnprocessable();
        $other->quota()->create(['listing_quota'=>-1]);
        $this->postJson('/api/v1/user/bulk-uploads',['type'=>'goods','file'=>\Illuminate\Http\UploadedFile::fake()->createWithContent('iklan.csv',"Judul Iklan,Kategori\nBarang,Elektronik\n")])->assertOk();
        $this->assertSame(-1,(int)$other->quota()->first()->listing_quota);
        $other->syncRoles(['admin']);$this->getJson('/api/v1/user/bulk-uploads')->assertForbidden();
    }
    public function test_ai_description_accepts_all_listing_categories_without_live_provider_calls()
    {
        $user=User::create(['name'=>'AI Tester','email'=>'ai@example.test','password'=>bcrypt('test'),'email_verified_at'=>now()]);
        $user->assignRole('user'); Sanctum::actingAs($user);
        \App\Models\Setting::create(['key'=>'gemini_api_key','value'=>'fake-test-key']);
        \Illuminate\Support\Facades\Http::fake(['generativelanguage.googleapis.com/*'=>\Illuminate\Support\Facades\Http::response(['candidates'=>[['content'=>['parts'=>[['text'=>'```html <p>Deskripsi pengujian</p> ```']]]]]])]);
        foreach (['properti','barang','jasa'] as $category) {
            $this->postJson('/api/v1/user/generate-ai',['title'=>'Iklan pengujian','type'=>'listing','category'=>$category,'facilities'=>['Parkir']])->assertOk()->assertJsonPath('data','<p>Deskripsi pengujian</p>');
        }
        \Illuminate\Support\Facades\Http::assertSentCount(3);
        $this->postJson('/api/v1/user/generate-ai',['title'=>''])->assertUnprocessable();
    }
    public function test_native_impersonation_is_admin_only_and_suspended_sessions_are_blocked()
    {
        $admin=User::create(['name'=>'Admin','email'=>'admin@example.test','password'=>bcrypt('test'),'email_verified_at'=>now()]);
        $admin->assignRole('admin');
        $user=User::create(['name'=>'User','email'=>'user@example.test','password'=>bcrypt('test'),'email_verified_at'=>now()]);
        $user->assignRole('user'); Sanctum::actingAs($user);
        $this->postJson('/api/v1/mobile/admin/users/impersonate',['record'=>$admin->id])->assertForbidden();
        Sanctum::actingAs($admin);
        $this->postJson('/api/v1/mobile/admin/users/impersonate',['record'=>$admin->id])->assertForbidden();
        $this->postJson('/api/v1/mobile/admin/users/impersonate',['record'=>$user->id])->assertOk()->assertJsonStructure(['data'=>['access_token']]);
        $user->update(['is_suspended'=>true]); Sanctum::actingAs($user);
        $this->getJson('/api/v1/mobile/partner/modules')->assertForbidden();
    }
    public function test_native_panel_enforces_roles_and_uses_web_page_validation()
    {
        Schema::create('pages',function(Blueprint $t){$t->id();$t->string('title');$t->string('slug')->unique();$t->text('content');$t->boolean('is_active')->default(false);$t->timestamps();});
        $user=User::create(['name'=>'Panel Tester','email'=>'panel@example.test','password'=>bcrypt('test'),'email_verified_at'=>now()]);
        $user->assignRole('user'); Sanctum::actingAs($user);
        $this->getJson('/api/v1/mobile/admin/modules')->assertForbidden();
        $this->postJson('/api/v1/mobile/admin/pages/create',['title'=>'Blocked'])->assertForbidden();
        $user->syncRoles(['admin']);
        $this->getJson('/api/v1/mobile/admin/modules')->assertOk()->assertJsonFragment(['key'=>'pages','title'=>'Halaman CMS']);
        $this->getJson('/api/v1/mobile/admin/pages')->assertOk()->assertJsonFragment(['key'=>'title','label'=>'Judul','type'=>'text','required'=>true,'options'=>[]]);
        $this->postJson('/api/v1/mobile/admin/pages/create',['payload'=>json_encode(['title'=>'Test'])])->assertUnprocessable();
        $payload=['title'=>'Halaman Pengujian','slug'=>'halaman-pengujian','content'=>'<p>Konten nyata</p>','is_active'=>true];
        $this->postJson('/api/v1/mobile/admin/pages/create',['payload'=>json_encode($payload)])->assertOk()->assertJsonPath('success',true);
        $page=\App\Models\Page::first(); $this->assertNotNull($page);
        $this->getJson('/api/v1/mobile/admin/pages?record='.$page->id)->assertOk()->assertJsonPath('data.values.title','Halaman Pengujian');
        $payload['title']='Diubah';$payload['is_active']=false;
        $this->postJson('/api/v1/mobile/admin/pages/edit',['record'=>$page->id,'payload'=>json_encode($payload)])->assertOk();
        $this->assertSame('Diubah',$page->fresh()->title);$this->assertFalse((bool)$page->fresh()->is_active);
        $this->postJson('/api/v1/mobile/admin/pages/not-allowed',['record'=>$page->id])->assertNotFound();
        $this->postJson('/api/v1/mobile/admin/pages/delete',['record'=>$page->id])->assertOk(); $this->assertSame(0,\App\Models\Page::count());
    }
    public function test_partner_password_requires_confirmation_and_current_password()
    {
        $user=User::create(['name'=>'Partner Tester','email'=>'partner-test@example.test','password'=>bcrypt('Original123'),'email_verified_at'=>now()]);
        $user->assignRole('partner'); Sanctum::actingAs($user);
        $this->getJson('/api/v1/mobile/partner/profile')->assertOk()->assertJsonFragment(['key'=>'password_confirmation','label'=>'Konfirmasi password','type'=>'password','required'=>true,'options'=>[]]);
        $payload=['current_password'=>'wrong','password'=>'Changed123','password_confirmation'=>'Changed123'];
        $this->postJson('/api/v1/mobile/partner/profile/password',['payload'=>json_encode($payload)])->assertUnprocessable();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('Original123',$user->fresh()->password));
        $payload['current_password']='Original123';
        $this->postJson('/api/v1/mobile/partner/profile/password',['payload'=>json_encode($payload)])->assertOk();
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('Changed123',$user->fresh()->password));
    }
}
