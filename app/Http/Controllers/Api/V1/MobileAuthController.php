<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\PartnerRegistration;
use App\Models\Setting;
use App\Notifications\MobileVerifyEmail;
use App\Notifications\MobileResetPassword;
use App\Services\PartnerRegistrationService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class MobileAuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            $user = User::create(['name' => $request->name, 'email' => $request->email, 'phone' => $request->phone, 'password' => Hash::make($request->password)]);
            $user->assignRole('user');
            $free = Setting::getValue('free_quota_register_enabled', '1') === '1';
            $user->quota()->create(['listing_quota' => $free ? 1 : 0, 'has_free_quota' => $free]);
            return $user;
        });
        $sent = $this->sendVerification($user);
        return $this->session($user, 201, $sent ? 'Akun berhasil dibuat. Periksa email untuk verifikasi.' : 'Akun berhasil dibuat. Pengiriman email belum berhasil, kirim ulang dari halaman verifikasi.');
    }

    public function registerPartner(Request $request, PartnerRegistrationService $service)
    {
        $user = $service->register($request);
        $admin = Setting::getValue('admin_notification_email');
        if ($admin) {
            try { \Illuminate\Support\Facades\Mail::raw('Pendaftaran partner baru: ' . $user->name . ' (' . $user->email . ')', function ($mail) use ($admin) { $mail->to($admin)->subject('Pendaftaran Partner WismaIndo'); }); }
            catch (\Throwable $e) { report($e); }
        }
        return response()->json(['success' => true, 'message' => 'Pendaftaran partner diterima. Tunggu pemeriksaan dokumen dan persetujuan admin sebelum masuk.', 'data' => ['status' => 'pending']], 201);
    }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required|string']);
        $key = 'mobile-login:' . Str::lower($request->email) . '|' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) return response()->json(['success' => false, 'message' => 'Terlalu banyak percobaan. Coba kembali sebentar lagi.'], 429);
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($key, 60);
            return response()->json(['success' => false, 'message' => 'Email atau password tidak sesuai.'], 401);
        }
        RateLimiter::clear($key);
        if (($user->is_suspended && !$user->suspended_until) || ($user->suspended_until && $user->suspended_until->isFuture())) {
            return response()->json(['success' => false, 'message' => 'Akun sedang ditangguhkan. Hubungi admin.'], 403);
        }
        if (!$user->hasAnyRole(['admin', 'site_moderator'])) {
            $registration = PartnerRegistration::where('user_id', $user->id)->first();
            if ($registration && $registration->status !== 'approved') return response()->json(['success' => false,
                'message' => $registration->status === 'pending' ? 'Pendaftaran partner masih menunggu persetujuan admin.' : 'Pendaftaran partner belum disetujui. ' . $registration->rejection_note,
                'code' => 'partner_' . $registration->status], 403);
        }
        return $this->session($user);
    }

    private function session(User $user, int $status = 200, string $message = 'Berhasil masuk.')
    {
        return response()->json(['success' => true, 'message' => $message, 'data' => [
            'user' => new UserResource($user), 'access_token' => $user->createToken('android')->plainTextToken, 'token_type' => 'Bearer',
        ]], $status);
    }

    private function sendVerification(User $user): bool
    {
        try { $user->notify(new MobileVerifyEmail()); return true; }
        catch (\Throwable $e) { report($e); return false; }
    }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) return response()->json(['success' => true, 'message' => 'Email sudah terverifikasi.']);
        $sent = $this->sendVerification($request->user());
        return response()->json(['success' => $sent, 'message' => $sent ? 'Email verifikasi sudah dikirim.' : 'Email belum dapat dikirim. Coba lagi nanti.'], $sent ? 200 : 503);
    }

    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        abort_unless(hash_equals(sha1($user->getEmailForVerification()), $hash), 403);
        if (!$user->hasVerifiedEmail() && $user->markEmailAsVerified()) event(new Verified($user));
        if ($request->expectsJson()) return response()->json(['success' => true, 'message' => 'Email berhasil diverifikasi.']);
        return $this->landing('Email berhasil diverifikasi', 'wismaindo://verified', 'Kembali ke aplikasi');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        try {
            Password::sendResetLink($request->only('email'), function ($user, $token) { $user->notify(new MobileResetPassword($token)); });
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'message' => 'Permintaan belum dapat diproses. Coba lagi nanti.'], 503);
        }
        return response()->json(['success' => true, 'message' => 'Jika email terdaftar, tautan reset password akan dikirim. Periksa kotak masuk dan spam.']);
    }

    public function passwordLink(Request $request)
    {
        $request->validate(['email' => 'required|email', 'token' => 'required|string']);
        return $this->landing('Atur ulang password', 'wismaindo://reset-password?' . http_build_query($request->only('email', 'token')), 'Buka aplikasi WismaIndo');
    }

    private function landing($title, $link, $label)
    {
        return response('<!doctype html><html lang="id"><meta name="viewport" content="width=device-width,initial-scale=1"><title>' . e($title) . '</title><body style="font-family:sans-serif;background:#FCF9F8;padding:32px"><h1>' . e($title) . '</h1><p>Lanjutkan melalui aplikasi WismaIndo.</p><a style="display:inline-block;background:#0194F3;color:white;padding:16px;border-radius:16px;text-decoration:none" href="' . e($link) . '">' . e($label) . '</a></body></html>')->header('Cache-Control', 'no-store')->header('Referrer-Policy', 'no-referrer');
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email', 'token' => 'required|string', 'password' => 'required|string|min:8|confirmed']);
        $status = Password::reset($request->only('email', 'token', 'password', 'password_confirmation'), function ($user) use ($request) {
            $user->forceFill(['password' => Hash::make($request->password), 'remember_token' => Str::random(60)])->save();
            $user->tokens()->delete();
            event(new PasswordReset($user));
        });
        return response()->json(['success' => $status === Password::PASSWORD_RESET,
            'message' => $status === Password::PASSWORD_RESET ? 'Password berhasil diperbarui. Silakan masuk kembali.' : 'Tautan tidak valid atau kedaluwarsa. Minta tautan reset baru.'], $status === Password::PASSWORD_RESET ? 200 : 422);
    }
}
