<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\PartnerRegistration;
use App\Notifications\MobileVerifyEmail;
use App\Notifications\MobileResetPassword;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema, Notification, Storage, URL, Hash};
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;

class MobileAuthApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:','cache.default'=>'array','mail.default'=>'array']);
        DB::purge('sqlite');
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        Schema::create('users', function(Blueprint $t) {
            $t->id(); $t->string('name'); $t->string('slug'); $t->string('email')->unique(); $t->string('phone')->nullable();
            $t->string('password'); $t->rememberToken(); $t->timestamp('email_verified_at')->nullable();
            $t->boolean('is_suspended')->default(false); $t->timestamp('suspended_until')->nullable(); $t->timestamps();
        });
        Schema::create('roles', function(Blueprint $t) { $t->id(); $t->string('name'); $t->string('guard_name'); $t->timestamps(); });
        Schema::create('model_has_roles', function(Blueprint $t) { $t->unsignedBigInteger('role_id'); $t->string('model_type'); $t->unsignedBigInteger('model_id'); });
        Schema::create('permissions', function(Blueprint $t) { $t->id(); $t->string('name'); $t->string('guard_name'); $t->timestamps(); });
        Schema::create('role_has_permissions', function(Blueprint $t) { $t->unsignedBigInteger('permission_id'); $t->unsignedBigInteger('role_id'); });
        Schema::create('model_has_permissions', function(Blueprint $t) { $t->unsignedBigInteger('permission_id'); $t->string('model_type'); $t->unsignedBigInteger('model_id'); });
        Schema::create('user_quotas', function(Blueprint $t) { $t->id(); $t->unsignedBigInteger('user_id'); $t->integer('listing_quota')->default(0); $t->boolean('has_free_quota')->default(false); $t->timestamps(); });
        Schema::create('settings', function(Blueprint $t) { $t->id(); $t->string('key'); $t->text('value')->nullable(); $t->timestamps(); });
        Schema::create('topup_transactions', function(Blueprint $t) { $t->id(); $t->unsignedBigInteger('user_id'); $t->string('status'); $t->integer('amount'); });
        Schema::create('listings', function(Blueprint $t) { $t->id(); $t->unsignedBigInteger('user_id'); });
        Schema::create('personal_access_tokens', function(Blueprint $t) { $t->id(); $t->morphs('tokenable'); $t->string('name'); $t->string('token',64)->unique(); $t->text('abilities')->nullable(); $t->timestamp('last_used_at')->nullable(); $t->timestamps(); });
        Schema::create('password_resets', function(Blueprint $t) { $t->string('email')->index(); $t->string('token'); $t->timestamp('created_at')->nullable(); });
        Schema::create('partner_registrations', function(Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id'); $t->string('status')->default('pending');
            foreach(['ktp_file','nib_file','foto_file','npwp_file','lisensi_file','rejection_note'] as $name) $t->string($name)->nullable();
            $t->timestamps();
        });
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        foreach(['user','partner','admin','site_moderator'] as $role) Role::create(['name'=>$role,'guard_name'=>'web']);
        Notification::fake(); Storage::fake('public');
    }
    private function data(): array {
        return ['name'=>'Pengujian Akun','email'=>'account@example.test','phone'=>'08123456789','password'=>'Password123','password_confirmation'=>'Password123'];
    }
    public function test_register_sends_verification_and_obeys_free_quota_setting() {
        $r = $this->postJson('/api/v1/register',$this->data())->assertCreated()->assertJsonPath('data.user.is_verified',false)->assertJsonPath('data.user.quota.remaining',1)->assertJsonPath('data.user.roles.0','user');
        $user = User::first(); Notification::assertSentTo($user, MobileVerifyEmail::class);
        $this->assertNotEmpty($r->json('data.access_token'));
        $this->postJson('/api/v1/register',$this->data())->assertUnprocessable();
        \App\Models\Setting::setValue('free_quota_register_enabled','0');
        $data = $this->data(); $data['email']='second@example.test';
        $this->postJson('/api/v1/register',$data)->assertCreated()->assertJsonPath('data.user.quota.remaining',0);
    }
    public function test_verification_requires_valid_signature_and_matching_hash() {
        $this->postJson('/api/v1/register',$this->data())->assertCreated(); $user=User::first();
        $url=URL::temporarySignedRoute('mobile.verify',now()->addMinutes(10),['id'=>$user->id,'hash'=>sha1($user->email)],false);
        $this->getJson($url.'&changed=1')->assertForbidden();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $this->getJson($url)->assertOk(); $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
    public function test_password_reset_revokes_tokens_and_cannot_be_replayed() {
        $this->postJson('/api/v1/register',$this->data())->assertCreated(); $user=User::first();
        $this->postJson('/api/v1/auth/forgot-password',['email'=>$user->email])->assertOk();
        $token=null; Notification::assertSentTo($user,MobileResetPassword::class,function($notification) use (&$token) { $token=$notification->token; return true; });
        $data=['email'=>$user->email,'token'=>$token,'password'=>'NewPassword123','password_confirmation'=>'NewPassword123'];
        $bad=$data; $bad['token']='invalid'; $this->postJson('/api/v1/auth/reset-password',$bad)->assertUnprocessable();
        $this->postJson('/api/v1/auth/reset-password',$data)->assertOk();
        $this->assertTrue(Hash::check('NewPassword123',$user->fresh()->password)); $this->assertSame(0,$user->tokens()->count());
        $this->postJson('/api/v1/auth/reset-password',$data)->assertUnprocessable();
    }
    public function test_partner_requires_nib_saves_documents_and_cannot_login_before_approval() {
        $data=$this->data(); $data['ktp_file']=UploadedFile::fake()->create('ktp.pdf',10,'application/pdf');
        $this->post('/api/v1/auth/register-partner',$data,['Accept'=>'application/json'])->assertUnprocessable()->assertJsonValidationErrors('nib_file');
        $this->assertSame(0,User::count());
        $data['nib_file']=UploadedFile::fake()->create('nib.pdf',10,'application/pdf');
        $this->post('/api/v1/auth/register-partner',$data,['Accept'=>'application/json'])->assertCreated()->assertJsonPath('data.status','pending');
        $registration=PartnerRegistration::first();
        Storage::disk('public')->assertExists(substr($registration->nib_file,8));
        $login=['email'=>$data['email'],'password'=>$data['password']];
        $this->postJson('/api/v1/login',$login)->assertForbidden()->assertJsonPath('code','partner_pending');
        $registration->update(['status'=>'approved']); $user=User::first(); $user->syncRoles(['partner']); $user->markEmailAsVerified();
        $this->postJson('/api/v1/login',$login)->assertOk()->assertJsonPath('data.user.roles.0','partner');
        $user->update(['is_suspended'=>true]); $this->postJson('/api/v1/login',$login)->assertForbidden();
    }
}
