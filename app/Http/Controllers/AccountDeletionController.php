<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\ConfirmAccountDeletion;
use App\Services\AccountDeletionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{RateLimiter, URL};

class AccountDeletionController extends Controller
{
    public function index() { return response()->view('account-deletion.index')->header('Referrer-Policy', 'no-referrer'); }

    public function request(Request $request, AccountDeletionService $service)
    {
        $data = $request->validate(['email'=>'required|email|max:255','consent'=>'accepted']);
        $key = 'account-deletion:'.hash('sha256', strtolower($data['email']));
        if (!RateLimiter::tooManyAttempts($key, 1)) {
            RateLimiter::hit($key, 300);
            $user = User::where('email', $data['email'])->first();
            if ($user && $service->eligible($user)) {
                $url = URL::temporarySignedRoute('account-deletion.confirm', now()->addMinutes(30), ['user'=>$user->id, 'fingerprint'=>$service->fingerprint($user)]);
                try { $user->notify(new ConfirmAccountDeletion($url)); }
                catch (\Throwable $e) {
                    RateLimiter::clear($key);
                    // Do not log the signed URL or disclose whether an address has an account.
                    \Illuminate\Support\Facades\Log::error('Email konfirmasi penghapusan gagal dikirim.');
                }
            }
        }
        $message = 'Jika email terdaftar sebagai akun pengguna atau partner, tautan konfirmasi akan dikirim. Periksa kotak masuk dan spam. Jika belum diterima, coba lagi setelah 5 menit.';
        return $request->expectsJson() ? response()->json(['success'=>true,'message'=>$message]) : back()->with('deletion_status', $message);
    }

    public function requestFromApp(Request $request, AccountDeletionService $service)
    {
        abort_unless($service->eligible($request->user()), 403);
        $request->merge(['email'=>$request->user()->email]);
        return $this->request($request, $service);
    }

    private function validateLink(Request $request, AccountDeletionService $service): User
    {
        abort_unless($request->hasValidSignature(), 403, 'Tautan tidak valid atau kedaluwarsa. Ajukan ulang dari halaman Hapus akun.');
        $user = User::findOrFail($request->route('user'));
        abort_unless($service->eligible($user) && hash_equals($service->fingerprint($user), (string) $request->query('fingerprint')), 403);
        return $user;
    }

    public function confirm(Request $request, AccountDeletionService $service)
    {
        $user = $this->validateLink($request, $service);
        return response()->view('account-deletion.confirm', ['accountName'=>$user->name, 'action'=>$request->fullUrl()])
            ->header('Cache-Control', 'no-store')->header('Referrer-Policy', 'no-referrer');
    }

    public function destroy(Request $request, AccountDeletionService $service)
    {
        $request->validate(['confirmation'=>'required|in:HAPUS','consent'=>'accepted']);
        $user = $this->validateLink($request, $service);
        try { $service->delete($user->id, (string) $request->query('fingerprint')); }
        catch (\RuntimeException $e) {
            \Illuminate\Support\Facades\Log::error('Penghapusan akun belum selesai. Periksa akses penyimpanan dan database.');
            return back()->withErrors(['confirmation'=>'Penghapusan belum selesai. Silakan coba kembali. Jika tautan kedaluwarsa, ajukan tautan baru.']);
        }
        if (auth()->id() === $user->id) { auth()->logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); }
        return redirect()->route('account-deletion.index')->with('deletion_status', 'Akun dan data terkait di sistem aktif WismaIndo sudah dihapus. Sesi login tidak dapat digunakan lagi.');
    }
}
