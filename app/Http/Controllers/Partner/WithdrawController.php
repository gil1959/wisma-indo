<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\PartnerWithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class WithdrawController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('partner.withdraw.index', [
            'user' => $user,
            'available' => (int) $user->partner_balance_available,
            'pending' => (int) $user->partner_balance_pending,
            'withdrawn' => (int) $user->partner_balance_withdrawn,
            'taxPercent' => (float) ($user->partner_tax_percent ?? 0),
            'recent' => PartnerWithdrawalRequest::query()
                ->where('partner_id', $user->id)
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'amount' => ['required', 'integer', 'min:10000'],
            'email' => ['required', 'email', 'max:190'],
            'bank_name' => ['required', 'string', 'max:120'],
            'account_number' => ['required', 'string', 'max:80'],
            'account_holder' => ['required', 'string', 'max:120'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // password check
        if (!Hash::check($data['password'], $user->password)) {
            return back()->withInput()->with('error', 'Password salah. Request penarikan gagal.');
        }

        // saldo check + pindah saldo dilakukan dalam transaksi
        try {
    DB::transaction(function () use ($user, $data) {

        $locked = $user->newQuery()
            ->whereKey($user->id)
            ->lockForUpdate()
            ->first();

        $amount = (int) $data['amount'];

        if ($amount > (int) $locked->partner_balance_available) {
            // lempar exception biar transaction rollback tanpa nampilin debug page
            throw new \RuntimeException('Saldo tidak cukup.');
        }

        // move: available -> pending
        $locked->partner_balance_available = (int) $locked->partner_balance_available - $amount;
        $locked->partner_balance_pending = (int) $locked->partner_balance_pending + $amount;
        $locked->save();

        PartnerWithdrawalRequest::create([
            'partner_id' => $locked->id,
            'amount' => $amount,
            'email' => $data['email'],
            'bank_name' => $data['bank_name'],
            'account_number' => $data['account_number'],
            'account_holder' => $data['account_holder'],
            'status' => 'pending',
        ]);
    });
} catch (\RuntimeException $e) {
    // saldo tidak cukup
    return back()->withInput()->with('error', $e->getMessage());
}


        // optional: update profil partner dari input (biar konsisten)
        $user->partner_bank_name = $data['bank_name'];
        $user->partner_bank_account_number = $data['account_number'];
        $user->partner_bank_account_holder = $data['account_holder'];
        $user->save();

        return redirect()->route('partner.withdraw.index')->with('success', 'Request penarikan berhasil dibuat.');
    }

    public function requests(Request $request)
    {
        $user = auth()->user();
        $status = trim((string) $request->get('status', 'all'));

        $q = PartnerWithdrawalRequest::query()
            ->where('partner_id', $user->id)
            ->latest();

        if (in_array($status, ['pending','approved','rejected'], true)) {
            $q->where('status', $status);
        }

        return view('partner.withdraw.requests', [
            'user' => $user,
            'status' => $status,
            'items' => $q->paginate(20)->withQueryString(),
        ]);
    }

    public function show(PartnerWithdrawalRequest $withdrawal)
    {
        $user = auth()->user();
        abort_unless($withdrawal->partner_id === $user->id, 403);

        return view('partner.withdraw.show', [
            'user' => $user,
            'w' => $withdrawal->load('reviewer'),
        ]);
    }

    public function destroy(PartnerWithdrawalRequest $withdrawal)
    {
        $user = auth()->user();
        abort_unless($withdrawal->partner_id === $user->id, 403);

        DB::transaction(function () use ($user, $withdrawal) {

            $lockedUser = $user->newQuery()->whereKey($user->id)->lockForUpdate()->first();
            $lockedW = PartnerWithdrawalRequest::query()->whereKey($withdrawal->id)->lockForUpdate()->first();

            if (!$lockedW || $lockedW->deleted_at) {
                return;
            }

            // kalau masih pending: balikin saldo pending -> available
            if ($lockedW->status === 'pending') {
                $amt = (int) $lockedW->amount;
                $lockedUser->partner_balance_pending = (int) $lockedUser->partner_balance_pending - $amt;
                $lockedUser->partner_balance_available = (int) $lockedUser->partner_balance_available + $amt;
                $lockedUser->save();
            }

            $lockedW->delete();
        });

        return back()->with('success', 'Request penarikan dihapus.');
    }
}
