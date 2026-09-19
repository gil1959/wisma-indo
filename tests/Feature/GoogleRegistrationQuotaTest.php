<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as GoogleUser;

class GoogleRegistrationQuotaTest extends MobileAuthApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(\App\Http\Middleware\TrackVisitor::class);
        Schema::table('users', function (Blueprint $table) {
            $table->string('google_id')->nullable();
            $table->string('avatar')->nullable();
        });
    }

    public function test_google_registration_obeys_global_toggle_and_repeat_login_keeps_quota(): void
    {
        foreach ([null, '0', '1'] as $index => $enabled) {
            if ($enabled !== null) {
                Setting::setValue('free_quota_register_enabled', $enabled);
            }
            $email = "google{$index}@example.test";
            $google = (new GoogleUser())->map([
                'id' => "google{$index}", 'name' => 'Google User',
                'email' => $email, 'avatar' => null,
            ]);
            $socialite = \Mockery::mock();
            $socialite->shouldReceive('driver')->with('google')->andReturnSelf();
            $socialite->shouldReceive('user')->andReturn($google);
            Socialite::swap($socialite);

            $this->get('/auth/google/callback')->assertRedirect('/akun');
            $user = User::where('email', $email)->firstOrFail();
            $this->assertAuthenticatedAs($user);
            $this->assertTrue($user->hasRole('user'));
            $expected = $enabled === '0' ? 0 : 1;
            $this->assertSame($expected, (int) $user->quota->listing_quota);
            $this->assertSame((bool) $expected, (bool) $user->quota->has_free_quota);

            $user->quota->update(['listing_quota' => 7]);
            Setting::setValue('free_quota_register_enabled', $enabled === '0' ? '1' : '0');
            Auth::logout();
            $this->get('/auth/google/callback')->assertRedirect('/akun');
            $this->assertSame(7, (int) $user->quota->fresh()->listing_quota);
            $this->assertSame(1, $user->quota()->count());
            Auth::logout();
        }
    }
}
