<?php
namespace Tests\Feature;

use App\Models\User;
use App\Notifications\ConfirmAccountDeletion;
use App\Services\AccountDeletionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema, Notification, Storage, URL};
use Laravel\Sanctum\Sanctum;

class AccountDeletionTest extends MobileAuthApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::create('visitors', function (Blueprint $t) { $t->id(); $t->string('ip_address'); $t->date('date'); $t->integer('hits')->default(0); $t->timestamps(); });
        Schema::table('users', function (Blueprint $t) { $t->string('avatar')->nullable(); });
        Schema::table('listings', function (Blueprint $t) { $t->string('cover_image')->nullable(); $t->string('slug')->nullable(); });
        Schema::table('topup_transactions', function (Blueprint $t) { $t->string('payment_proof')->nullable(); });
        foreach (['listing_images','favorite_listings','listing_transactions','survey_schedules','scheduled_autoposts','buyer_leads'] as $name) {
            Schema::create($name, function (Blueprint $t) {
                $t->id(); $t->unsignedBigInteger('listing_id')->nullable(); $t->unsignedBigInteger('user_id')->nullable(); $t->unsignedBigInteger('partner_id')->nullable();
                $t->string('image_path')->nullable(); $t->string('email')->nullable();
            });
        }
        Schema::create('lead_activities', function (Blueprint $t) { $t->id(); $t->unsignedBigInteger('buyer_lead_id'); });
        foreach (['bulk_uploads','social_accounts','partner_subscriptions','sessions'] as $name) Schema::create($name, function (Blueprint $t) { $t->id(); $t->unsignedBigInteger('user_id'); $t->string('file_path')->nullable(); });
    }

    private function account(string $role = 'user'): User
    {
        $user = User::create(['name'=>'Akun Pengujian','email'=>uniqid().'@example.test','password'=>bcrypt('Password123'),'email_verified_at'=>now()]);
        $user->assignRole($role);
        return $user;
    }
    private function link(User $user): string
    {
        return URL::temporarySignedRoute('account-deletion.confirm', now()->addMinutes(30), ['user'=>$user->id,'fingerprint'=>app(AccountDeletionService::class)->fingerprint($user)]);
    }

    public function test_public_page_and_email_link_are_read_only()
    {
        $user = $this->account();
        $index = $this->get('/hapus-akun')->assertOk()->assertSee('Hapus Akun WismaIndo');
        $confirm = $this->get($this->link($user))->assertOk()->assertSee('Konfirmasi penghapusan akun');
        if ($directory = getenv('WISMA_PREVIEW_DIR')) {
            file_put_contents($directory.'/index.html', $index->getContent());
            file_put_contents($directory.'/confirm.html', $confirm->getContent());
        }
        $this->assertNotNull($user->fresh());
    }

    public function test_request_is_generic_and_does_not_delete_until_confirmation()
    {
        $user = $this->account();
        $found = $this->postJson('/hapus-akun', ['email'=>$user->email,'consent'=>true])->assertOk();
        $missing = $this->postJson('/hapus-akun', ['email'=>'missing@example.test','consent'=>true])->assertOk();
        $this->assertSame($found->json(), $missing->json());
        $this->assertNotNull($user->fresh());
        Notification::assertSentTo($user, ConfirmAccountDeletion::class, fn($n) => str_contains($n->url, 'signature='));
        $this->postJson('/hapus-akun', ['email'=>$user->email,'consent'=>true])->assertOk();
        Notification::assertSentToTimes($user, ConfirmAccountDeletion::class, 1);
    }

    public function test_confirmation_requires_valid_unexpired_link_and_explicit_consent()
    {
        $user = $this->account(); $url = $this->link($user);
        $this->postJson($url.'&altered=1', ['confirmation'=>'HAPUS','consent'=>true])->assertForbidden();
        $this->postJson($url, ['confirmation'=>'hapus','consent'=>true])->assertUnprocessable();
        $this->postJson($url, ['confirmation'=>'HAPUS'])->assertUnprocessable();
        $this->travel(31)->minutes();
        $this->postJson($url, ['confirmation'=>'HAPUS','consent'=>true])->assertForbidden();
        $this->assertNotNull($user->fresh());
    }

    public function test_deletes_owned_data_files_and_tokens_without_touching_another_account()
    {
        $user = $this->account('partner'); $other = $this->account();
        $user->update(['avatar'=>'/storage/avatars/owned.jpg']);
        foreach (['avatars/owned.jpg','listings/owned.jpg','partners/1/ktp.pdf','payments/proof.jpg','bulk_uploads/owned.csv','avatars/other.jpg'] as $path) Storage::disk('public')->put($path, 'fixture');
        $owned = DB::table('listings')->insertGetId(['user_id'=>$user->id,'cover_image'=>'/storage/listings/owned.jpg']);
        DB::table('listings')->insert(['user_id'=>$other->id]);
        DB::table('listing_images')->insert(['listing_id'=>$owned,'image_path'=>'/storage/listings/owned.jpg']);
        DB::table('favorite_listings')->insert(['listing_id'=>$owned,'user_id'=>$other->id]);
        DB::table('partner_registrations')->insert(['user_id'=>$user->id,'ktp_file'=>'storage/partners/1/ktp.pdf']);
        DB::table('topup_transactions')->insert(['user_id'=>$user->id,'amount'=>10,'status'=>'success','payment_proof'=>'/storage/payments/proof.jpg']);
        DB::table('bulk_uploads')->insert(['user_id'=>$user->id,'file_path'=>'bulk_uploads/owned.csv']);
        DB::table('social_accounts')->insert(['user_id'=>$user->id]);
        $lead = DB::table('buyer_leads')->insertGetId(['partner_id'=>$user->id,'listing_id'=>$owned]);
        DB::table('lead_activities')->insert(['buyer_lead_id'=>$lead]);
        DB::table('survey_schedules')->insert(['partner_id'=>$user->id,'listing_id'=>$owned]);
        DB::table('scheduled_autoposts')->insert(['user_id'=>$user->id,'listing_id'=>$owned]);
        DB::table('partner_subscriptions')->insert(['user_id'=>$user->id]);
        DB::table('sessions')->insert(['user_id'=>$user->id]);
        $user->createToken('test');
        $url = $this->link($user);
        $this->post($url, ['confirmation'=>'HAPUS','consent'=>true])->assertRedirect(route('account-deletion.index'));
        $this->assertNull($user->fresh()); $this->assertNotNull($other->fresh());
        foreach (['avatars/owned.jpg','listings/owned.jpg','partners/1/ktp.pdf','payments/proof.jpg','bulk_uploads/owned.csv'] as $path) Storage::disk('public')->assertMissing($path);
        Storage::disk('public')->assertExists('avatars/other.jpg');
        foreach (['listing_images','favorite_listings','partner_registrations','topup_transactions','bulk_uploads','social_accounts','sessions','personal_access_tokens','buyer_leads','lead_activities','survey_schedules','scheduled_autoposts','partner_subscriptions'] as $table) $this->assertSame(0, DB::table($table)->count(), $table);
        $this->assertSame(1, DB::table('listings')->count());
        $this->postJson($url, ['confirmation'=>'HAPUS','consent'=>true])->assertNotFound();
    }

    public function test_app_uses_authenticated_email_and_admin_cannot_self_delete()
    {
        $user = $this->account(); $other = $this->account(); Sanctum::actingAs($user);
        $this->postJson('/api/v1/account/deletion-request', ['email'=>$other->email,'consent'=>true])->assertOk();
        Notification::assertSentTo($user, ConfirmAccountDeletion::class); Notification::assertNotSentTo($other, ConfirmAccountDeletion::class);
        $admin = $this->account('admin'); Sanctum::actingAs($admin);
        $this->postJson('/api/v1/account/deletion-request', ['consent'=>true])->assertForbidden();
        $this->post($this->link($admin), ['confirmation'=>'HAPUS','consent'=>true])->assertForbidden();
        $this->assertNotNull($admin->fresh());
    }

    public function test_changed_email_invalidates_previous_deletion_link()
    {
        $user = $this->account(); $url = $this->link($user); $user->update(['email'=>'changed@example.test']);
        $this->post($url, ['confirmation'=>'HAPUS','consent'=>true])->assertForbidden();
        $this->assertNotNull($user->fresh());
    }
}
