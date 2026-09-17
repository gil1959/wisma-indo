<?php
namespace Tests\Feature;

use App\Models\{User, Setting, TopupTransaction, PartnerSubscription};
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB, Schema, Http};

class PaymentFlowApiTest extends MobileAuthApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        Schema::table('topup_transactions', function (Blueprint $t) {
            $t->unsignedBigInteger('topup_package_id')->nullable();
            foreach (['payment_reference', 'payment_url', 'payment_method'] as $field) $t->string($field)->nullable();
            $t->integer('unique_code')->nullable(); $t->integer('price')->default(10000); $t->integer('total_amount')->default(10000); $t->timestamps();
        });
        Schema::create('topup_packages', function (Blueprint $t) {
            $t->id(); $t->integer('price')->default(10000); $t->boolean('is_active')->default(true); $t->string('name'); $t->integer('amount'); $t->integer('bonus'); $t->timestamps();
        });
        Schema::create('partner_packages', function (Blueprint $t) {
            $t->id(); $t->string('name')->default('Partner'); $t->integer('price')->default(10000); $t->boolean('is_free')->default(false); $t->boolean('is_active')->default(true); $t->integer('listing_quota'); $t->integer('duration_days'); $t->timestamps();
        });
        Schema::create('partner_subscriptions', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('user_id'); $t->unsignedBigInteger('partner_package_id');
            $t->string('status'); $t->string('payment_method'); $t->string('payment_reference')->nullable();
            $t->string('payment_url')->nullable(); $t->integer('amount'); $t->timestamp('starts_at')->nullable(); $t->timestamp('ends_at')->nullable(); $t->timestamps();
        });
        Setting::setValue('tripay_private_key', 'test-secret');
        Setting::setValue('xendit_callback_token', 'test-token');
        Setting::setValue('xendit_api_key', 'test-api');
    }

    private function transaction(bool $partner = false, int $quota = 2)
    {
        $id = DB::table('users')->insertGetId(['name'=>'Payment test','slug'=>'payment','email'=>uniqid().'@example.test','password'=>'unused']);
        DB::table('user_quotas')->insert(['user_id'=>$id,'listing_quota'=>$quota]);
        if ($partner) {
            $package = DB::table('partner_packages')->insertGetId(['listing_quota'=>10,'duration_days'=>30]);
            return PartnerSubscription::create(['user_id'=>$id,'partner_package_id'=>$package,'status'=>'pending','payment_method'=>'BRIVA','payment_reference'=>'PARTNER-'.$id.'-123','amount'=>10000]);
        }
        $package = DB::table('topup_packages')->insertGetId(['name'=>'Test','amount'=>10,'bonus'=>3]);
        return TopupTransaction::create(['user_id'=>$id,'topup_package_id'=>$package,'status'=>'pending','amount'=>10,'payment_method'=>'BRIVA','payment_reference'=>'TOPUP-'.$id.'-123','payment_url'=>'https://tripay.co.id/checkout/example']);
    }

    private function sendPaymentCallback($transaction, string $status = 'PAID', string $route = '/api/webhooks/topup/tripay', bool $valid = true)
    {
        $body = json_encode(['merchant_ref'=>$transaction->payment_reference,'status'=>$status]);
        return $this->call('POST', $route, [], [], [], ['CONTENT_TYPE'=>'application/json','HTTP_ACCEPT'=>'application/json','HTTP_X_CALLBACK_EVENT'=>'payment_status','HTTP_X_CALLBACK_SIGNATURE'=>$valid ? hash_hmac('sha256',$body,'test-secret') : 'invalid'], $body);
    }

    public function test_topup_callback_grants_bonus_once_and_late_expiry_cannot_revoke_success()
    {
        $tx = $this->transaction();
        $this->sendPaymentCallback($tx, 'PAID', '/api/webhooks/tripay')->assertOk();
        $this->sendPaymentCallback($tx)->assertOk();
        $this->sendPaymentCallback($tx, 'EXPIRED')->assertOk();
        $this->assertSame('success', $tx->fresh()->status);
        $this->assertEquals(15, $tx->user->quota->listing_quota);
    }

    public function test_partner_payment_activates_package_once_and_preserves_unlimited_quota()
    {
        foreach ([2, -1] as $quota) {
            $tx = $this->transaction(true, $quota);
            $this->sendPaymentCallback($tx)->assertOk(); $this->sendPaymentCallback($tx)->assertOk();
            $this->assertSame('active', $tx->fresh()->status);
            $this->assertNotNull($tx->fresh()->ends_at);
            $this->assertEquals($quota == -1 ? -1 : 12, $tx->user->quota->listing_quota);
        }
    }

    public function test_invalid_or_failed_callback_does_not_credit_quota()
    {
        $tx = $this->transaction();
        $this->sendPaymentCallback($tx, valid: false)->assertForbidden();
        $this->sendPaymentCallback($tx, 'EXPIRED')->assertOk();
        $this->assertSame('failed', $tx->fresh()->status);
        $this->assertEquals(2, $tx->user->quota->listing_quota);
    }

    public function test_topup_response_contains_gateway_url_and_return_does_not_credit_quota()
    {
        $tx = $this->transaction();
        $data = (new \App\Http\Resources\TopupTransactionResource($tx))->resolve();
        $this->assertSame($tx->payment_url, $data['payment_url']);
        $this->get('/api/v1/payments/return?status=PAID')->assertOk()->assertSee('Periksa status pembayaran');
        $this->assertSame('pending', $tx->fresh()->status);
        $this->assertEquals(2, $tx->user->quota->listing_quota);
    }

    public function test_xendit_verifies_invoice_reference_before_crediting()
    {
        $tx = $this->transaction();
        $body = ['external_id'=>$tx->payment_reference,'id'=>'invoice123','status'=>'PAID'];
        Http::fake(['api.xendit.co/*'=>Http::sequence()->push(['external_id'=>'WRONG','status'=>'PAID','amount'=>10000])->push(['external_id'=>$tx->payment_reference,'status'=>'PAID','amount'=>10000])]);
        $this->postJson('/api/webhooks/topup/xendit', $body, ['X-Callback-Token'=>'test-token'])->assertUnprocessable();
        $this->assertSame('pending', $tx->fresh()->status);
        $this->postJson('/api/webhooks/xendit', $body, ['X-Callback-Token'=>'test-token'])->assertOk();
        $this->assertSame('success', $tx->fresh()->status);
        $this->assertEquals(15, $tx->user->quota->listing_quota);
    }

    public function test_native_gateway_checkout_returns_url_and_does_not_credit_before_payment()
    {
        Schema::create('offline_payment_methods', function (Blueprint $t) { $t->id(); $t->boolean('is_active')->default(true); });
        \Illuminate\Support\Facades\Cache::put('native-partner-payment-channels', [['code'=>'BRIVA','name'=>'BRI']], 60);
        Http::fake(['tripay.co.id/*'=>Http::response(['success'=>true,'data'=>['checkout_url'=>'https://tripay.co.id/checkout/example']])]);
        foreach ([false, true] as $partner) {
            $existing = $this->transaction($partner);
            $user = $existing->user; $user->update(['email_verified_at'=>now()]); $user->assignRole($partner ? 'partner' : 'user');
            \Laravel\Sanctum\Sanctum::actingAs($user);
            if ($partner) {
                $response = $this->postJson('/api/v1/mobile/partner/billing/purchase', ['payload'=>json_encode(['package_id'=>$existing->partner_package_id,'payment_method'=>'pg|BRIVA'])]);
            } else {
                $response = $this->postJson('/api/v1/user/topup/checkout/'.$existing->topup_package_id, ['payment_method'=>'pg|BRIVA']);
            }
            $response->assertSuccessful()->assertJsonPath('success', true)->assertJsonPath('data.payment_url', 'https://tripay.co.id/checkout/example')->assertJsonPath('data.status','pending');
            $this->assertEquals(2, $user->quota->listing_quota);
        }
        Http::assertSent(function ($request) { return str_ends_with($request['return_url'], '/api/v1/payments/return') && str_ends_with($request['callback_url'], '/api/webhooks/topup/tripay'); });
    }
}
