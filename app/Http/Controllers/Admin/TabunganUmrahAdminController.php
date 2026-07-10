<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TabunganUmrahAccount;
use App\Models\TabunganUmrahDeposit;
use App\Mail\TabunganUmrahAccountVerifiedUserMail;
use App\Mail\TabunganUmrahAccountRejectedUserMail;
use App\Mail\TabunganUmrahDepositApprovedUserMail;
use App\Mail\TabunganUmrahDepositRejectedUserMail;
use App\Mail\TabunganUmrahDepositStatusAdminMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class TabunganUmrahAdminController extends Controller
{
    public function pendingAccounts()
    {
        $accounts = TabunganUmrahAccount::where('status', 'pending')
            ->latest()
            ->paginate(15);

        return view('admin.tabungan-umrah.accounts-pending', compact('accounts'));
    }

    public function verifiedAccounts()
    {
        $accounts = TabunganUmrahAccount::where('status', 'verified')
            ->latest()
            ->paginate(15);

        return view('admin.tabungan-umrah.accounts-verified', compact('accounts'));
    }

    public function showAccount(TabunganUmrahAccount $account)
    {
        $account->load(['user','deposits' => function($q){
            $q->latest();
        }]);

        $approvedTotal = $account->approved_total;

        return view('admin.tabungan-umrah.account-show', compact('account','approvedTotal'));
    }

    public function verifyAccount(Request $request, TabunganUmrahAccount $account)
    {
        $request->validate([
            'target_amount' => ['required','integer','min:1000'],
            'target_departure_date' => ['required','date'],
        ]);

        $account->update([
            'status' => 'verified',
            'target_amount' => $request->target_amount,
            'target_departure_date' => $request->target_departure_date,
            'approved_at' => now(),
            'rejected_at' => null,
            'rejected_reason' => null,
            'suspended_at' => null,
        ]);

        Mail::to($account->user->email)->send(new TabunganUmrahAccountVerifiedUserMail($account));

        return redirect()->route('admin.tabungan-umrah.accounts.show', $account->id)
            ->with('success', 'Akun berhasil diverifikasi.');
    }

    public function rejectAccount(Request $request, TabunganUmrahAccount $account)
    {
        $request->validate([
            'rejected_reason' => ['required','string','max:2000'],
        ]);

        $account->update([
            'status' => 'rejected',
            'rejected_at' => now(),
            'rejected_reason' => $request->rejected_reason,
        ]);

        Mail::to($account->user->email)->send(new TabunganUmrahAccountRejectedUserMail($account));

        return redirect()->route('admin.tabungan-umrah.accounts.show', $account->id)
            ->with('success', 'Akun ditolak dan email terkirim.');
    }

    public function suspendAccount(TabunganUmrahAccount $account)
    {
        $account->update([
            'status' => 'suspended',
            'suspended_at' => now(),
        ]);

        return back()->with('success', 'Akun disuspend.');
    }

    public function unsuspendAccount(TabunganUmrahAccount $account)
    {
        $account->update([
            'status' => 'verified',
            'suspended_at' => null,
        ]);

        return back()->with('success', 'Suspend dicabut.');
    }

    public function depositsIndex(Request $request)
{
    $status = $request->query('status', 'all');

    if (!in_array($status, ['all', 'waiting_verification', 'approved', 'rejected'], true)) {
        $status = 'all';
    }

    $query = TabunganUmrahDeposit::with(['account.user', 'paymentMethod'])
        ->latest();

    if ($status !== 'all') {
        $query->where('status', $status);
    }

    $deposits = $query->paginate(20)->withQueryString();

    return view('admin.tabungan-umrah.deposits-index', compact('deposits', 'status'));
}

    public function showDeposit(TabunganUmrahDeposit $deposit)
    {
        $deposit->load(['account.user','paymentMethod','verifier']);

        return view('admin.tabungan-umrah.deposit-show', compact('deposit'));
    }

    public function approveDeposit(TabunganUmrahDeposit $deposit)
    {
        $deposit->update([
            'status' => 'approved',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'note' => null,
        ]);

        Mail::to($deposit->user->email)->send(new TabunganUmrahDepositApprovedUserMail($deposit));
        Mail::to(config('mail.from.address'))->send(new TabunganUmrahDepositStatusAdminMail($deposit)); // notifikasi admin juga

        return back()->with('success', 'Setoran approved.');
    }

    public function rejectDeposit(Request $request, TabunganUmrahDeposit $deposit)
    {
        $request->validate([
            'note' => ['required','string','max:2000'],
        ]);

        $deposit->update([
            'status' => 'rejected',
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'note' => $request->note,
        ]);

        Mail::to($deposit->user->email)->send(new TabunganUmrahDepositRejectedUserMail($deposit));
        Mail::to(config('mail.from.address'))->send(new TabunganUmrahDepositStatusAdminMail($deposit));

        return back()->with('success', 'Setoran rejected.');
    }

    public function editAccount(TabunganUmrahAccount $account)
{
    $account->load('user');

    return view('admin.tabungan-umrah.account-edit', compact('account'));
}
public function printStatement(TabunganUmrahAccount $account, Request $request)
{
    $from = Carbon::parse($request->query('from', now()->startOfMonth()->toDateString()))->startOfDay();
    $to   = Carbon::parse($request->query('to', now()->endOfMonth()->toDateString()))->endOfDay();

    if ($from->gt($to)) {
        abort(400, 'Range tanggal tidak valid.');
    }

    $openingBalance = (int) TabunganUmrahDeposit::where('account_id', $account->id)
        ->where('status', 'approved')
        ->whereNotNull('verified_at')
        ->where('verified_at', '<', $from)
        ->sum('amount');

    $deposits = TabunganUmrahDeposit::with(['paymentMethod', 'verifier', 'user'])
        ->where('account_id', $account->id)
        ->whereBetween('submitted_at', [$from, $to])
        ->orderBy('submitted_at', 'asc')
        ->get();

    $running = $openingBalance;

    $rows = $deposits->map(function ($d) use (&$running) {
        $credit = 0;
        $debit = 0;

        if ($d->status === 'approved') {
            $credit = (int) $d->amount;
            $running += $credit;
        }

        $method = optional($d->paymentMethod)->name ?: '—';
        $desc = 'Setoran Tabungan Umrah ('.$method.')';
        if (!empty($d->note)) {
            $desc .= ' - '.$d->note;
        }

        return [
            'date' => optional($d->submitted_at)->format('d/m/Y H:i'),
            'desc' => $desc,
            'status' => $d->status,
            'debit' => $debit,
            'credit' => $credit,
            'balance' => $running,
            'ref' => 'DEP-'.$d->id,
        ];
    });

    $totals = [
        'total_credit_approved' => (int) $deposits->where('status', 'approved')->sum('amount'),
        'closing_balance' => (int) $running,
    ];

    $statementNo = 'STMT-UMR-'.$account->id.'-'.now()->format('YmdHis');

    return view('shared.tabungan-umrah.statement-print', [
        'account' => $account,
        'from' => $from,
        'to' => $to,
        'openingBalance' => $openingBalance,
        'rows' => $rows,
        'totals' => $totals,
        'statementNo' => $statementNo,
        'printedBy' => auth()->user(),
        'contextLabel' => 'Admin',
    ]);
}

public function updateAccount(Request $request, TabunganUmrahAccount $account)
{
    $account->load('user');

    $request->validate([
        // USER FIELDS
        'user_name' => ['required','string','max:191'],
        'user_email' => [
            'required','email','max:191',
            Rule::unique('users', 'email')->ignore($account->user_id),
        ],
        'user_password' => ['nullable','string','min:8','confirmed'], // butuh user_password_confirmation
        'user_phone' => ['nullable','string','max:50'],
        'user_address' => ['nullable','string','max:255'],
        'user_full_address' => ['nullable','string','max:255'],
        'user_sub_district' => ['nullable','string','max:255'],

        // TABUNGAN UMRAH ACCOUNT FIELDS
        'account_full_name' => ['required','string','max:191'],
        'account_whatsapp' => ['required','string','max:50'],
        'account_saving_type' => ['required', Rule::in(['umroh_reguler','haji_furoda'])],
        'account_status' => ['required', Rule::in(['pending','verified','rejected','suspended'])],
        'target_amount' => ['nullable','integer','min:1000'],
        'target_departure_date' => ['nullable','date'],
        'rejected_reason' => ['nullable','string','max:2000'],
    ]);

    DB::transaction(function () use ($request, $account) {

        // Update USER
        $userData = [
            'name' => $request->user_name,
            'email' => $request->user_email,
            'phone' => $request->user_phone,
            'address' => $request->user_address,
            'full_address' => $request->user_full_address,
            'sub_district' => $request->user_sub_district,
        ];

        if (!empty($request->user_password)) {
            $userData['password'] = Hash::make($request->user_password);
        }

        $account->user->update($userData);

        // Update ACCOUNT TABUNGAN UMRAH
        $accountData = [
            'full_name' => $request->account_full_name,
            'whatsapp' => $request->account_whatsapp,
            'saving_type' => $request->account_saving_type,
            'status' => $request->account_status,
            'target_amount' => $request->target_amount,
            'target_departure_date' => $request->target_departure_date,
        ];

        // Kalau status jadi rejected, simpan reason. Kalau bukan rejected, bersihin reason biar rapi.
        if ($request->account_status === 'rejected') {
            $accountData['rejected_reason'] = $request->rejected_reason ?: $account->rejected_reason;
            $accountData['rejected_at'] = $account->rejected_at ?: now();
        } else {
            $accountData['rejected_reason'] = null;
            $accountData['rejected_at'] = null;
        }

        // Kalau status verified, set approved_at jika kosong. Kalau tidak verified, biarin (jangan kacauin histori).
        if ($request->account_status === 'verified' && empty($account->approved_at)) {
            $accountData['approved_at'] = now();
        }

        // Kalau suspended, set suspended_at kalau kosong. Kalau bukan suspended, kosongkan.
        if ($request->account_status === 'suspended') {
            $accountData['suspended_at'] = $account->suspended_at ?: now();
        } else {
            $accountData['suspended_at'] = null;
        }

        $account->update($accountData);
    });

    return redirect()->route('admin.tabungan-umrah.accounts.show', $account->id)
        ->with('success', 'Data akun & user berhasil diperbarui.');
}

}
