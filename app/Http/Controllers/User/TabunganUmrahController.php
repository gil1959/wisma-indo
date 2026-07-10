<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Models\TabunganUmrahAccount;
use App\Models\TabunganUmrahDeposit;
use App\Mail\TabunganUmrahAccountSubmittedUserMail;
use App\Mail\TabunganUmrahAccountSubmittedAdminMail;
use App\Mail\TabunganUmrahDepositSubmittedUserMail;
use App\Mail\TabunganUmrahDepositSubmittedAdminMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Models\PaymentMethod;
use Carbon\Carbon;


class TabunganUmrahController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $account = TabunganUmrahAccount::where('user_id', $user->id)
            ->latest()
            ->first();

        // Belum pernah daftar, atau terakhir rejected (boleh daftar ulang)
        if (!$account || $account->status === 'rejected') {
            return view('user.tabungan-umrah.register', compact('account'));
        }

        // Masih pending / suspended
        if (in_array($account->status, ['pending', 'suspended'], true)) {
            return view('user.tabungan-umrah.pending', compact('account'));
        }

        // Verified -> dashboard tabungan
        $approvedTotal = $account->approved_total;
        $target = (int) ($account->target_amount ?? 0);
        $progress = $target > 0 ? min(100, (int) round(($approvedTotal / $target) * 100)) : 0;

        $lastDeposit = $account->deposits()->latest()->first();
        $deposits = $account->deposits()->latest()->paginate(10);

        return view('user.tabungan-umrah.show', compact(
            'account',
            'approvedTotal',
            'target',
            'progress',
            'lastDeposit',
            'deposits'
        ));
    }

    public function storeRegistration(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'full_name' => ['required', 'string', 'max:191'],
            'whatsapp' => ['required', 'string', 'max:50'],
            'saving_type' => ['required', 'in:umroh_reguler,haji_furoda'],
        ]);

        $account = TabunganUmrahAccount::create([
            'user_id' => $user->id,
            'full_name' => $request->full_name,
            'whatsapp' => $request->whatsapp,
            'saving_type' => $request->saving_type,
            'status' => 'pending',
        ]);

        // email ke user + admin
        Mail::to($user->email)->send(new TabunganUmrahAccountSubmittedUserMail($account));
        $adminEmail = config('mail.from.address'); // pola di project ini sering pakai config/mail; kalau lu punya setting lain, ganti di sini.
        Mail::to($adminEmail)->send(new TabunganUmrahAccountSubmittedAdminMail($account));

        $isEn = app()->getLocale() === 'en';

        return redirect()->route('user.tabungan-umrah.index')
            ->with(
                'success',
                $isEn
                    ? 'Umrah savings registration submitted. Waiting for admin verification.'
                    : 'Registrasi tabungan umrah berhasil. Menunggu verifikasi admin.'
            );
    }

    public function createDeposit()
    {
        $user = auth()->user();
        $account = TabunganUmrahAccount::where('user_id', $user->id)->latest()->firstOrFail();

        if ($account->status !== 'verified') {
            $isEn = app()->getLocale() === 'en';
            abort(403, $isEn ? 'Umrah savings account is not verified yet.' : 'Akun tabungan umrah belum terverifikasi.');
        }

        $methods = PaymentMethod::where('type', 'manual')
            ->where('is_active', 1)
            ->orderBy('id', 'desc')
            ->get();

        return view('user.tabungan-umrah.deposit-create', compact('account', 'methods'));
    }

    public function storeDeposit(Request $request)
    {
        $user = auth()->user();
        $account = TabunganUmrahAccount::where('user_id', $user->id)->latest()->firstOrFail();

        if ($account->status !== 'verified') {
            abort(403, 'Akun tabungan umrah belum terverifikasi.');
        }

        $request->validate([
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'amount' => ['required', 'integer', 'min:1000'],
            'proof_image' => ['required', 'image', 'max:4096'],
        ]);
        $pm = PaymentMethod::where('id', $request->payment_method_id)
            ->where('type', 'manual')
            ->where('is_active', 1)
            ->first();

        if (!$pm) {
            $isEn = app()->getLocale() === 'en';
            return back()->withErrors([
                'payment_method_id' => $isEn ? 'Destination account is invalid / inactive.' : 'Rekening tujuan tidak valid / nonaktif.'
            ])->withInput();
        }

        $path = $request->file('proof_image')->store('tabungan_umrah/proofs', 'public');

        $deposit = TabunganUmrahDeposit::create([
            'account_id' => $account->id,
            'user_id' => $user->id,
            'payment_method_id' => $pm->id,
            'amount' => $request->amount,
            'proof_image' => $path,
            'status' => 'waiting_verification',
            'submitted_at' => now(),
        ]);

        Mail::to($user->email)->send(new TabunganUmrahDepositSubmittedUserMail($deposit));
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new TabunganUmrahDepositSubmittedAdminMail($deposit));

        $isEn = app()->getLocale() === 'en';

        return redirect()->route('user.tabungan-umrah.index')
            ->with(
                'success',
                $isEn
                    ? 'Deposit submitted. Waiting for admin verification.'
                    : 'Setoran berhasil dikirim. Menunggu verifikasi admin.'
            );
    }

    public function showDeposit(TabunganUmrahDeposit $deposit)
    {
        $user = auth()->user();

        if ((int)$deposit->user_id !== (int)$user->id) {
            abort(403);
        }

        $deposit->load(['paymentMethod', 'verifier']);

        return view('user.tabungan-umrah.deposit-show', compact('deposit'));
    }

    public function printStatement(Request $request)
    {
        $user = auth()->user();

        $account = TabunganUmrahAccount::where('user_id', $user->id)
            ->where('status', 'verified')
            ->latest()
            ->firstOrFail();

        $from = Carbon::parse($request->query('from', now()->startOfMonth()->toDateString()))->startOfDay();
        $to   = Carbon::parse($request->query('to', now()->endOfMonth()->toDateString()))->endOfDay();

        if ($from->gt($to)) {
            $isEn = app()->getLocale() === 'en';
            abort(400, $isEn ? 'Invalid date range.' : 'Range tanggal tidak valid.');
        }

        $openingBalance = (int) TabunganUmrahDeposit::where('account_id', $account->id)
            ->where('status', 'approved')
            ->whereNotNull('verified_at')
            ->where('verified_at', '<', $from)
            ->sum('amount');

        $deposits = TabunganUmrahDeposit::with(['paymentMethod', 'verifier'])
            ->where('account_id', $account->id)
            ->whereBetween('submitted_at', [$from, $to])
            ->orderBy('submitted_at', 'asc')
            ->get();

        $running = $openingBalance;

        $rows = $deposits->map(function ($d) use (&$running) {
            $credit = 0;
            $debit = 0;

            // Hanya approved yang mempengaruhi saldo
            if ($d->status === 'approved') {
                $credit = (int) $d->amount;
                $running += $credit;
            }

            $method = optional($d->paymentMethod)->name ?: '—';
            $isEn = app()->getLocale() === 'en';
            $desc = ($isEn ? 'Umrah Savings Deposit' : 'Setoran Tabungan Umrah') . ' (' . $method . ')';

            if (!empty($d->note)) {
                $desc .= ' - ' . $d->note;
            }

            return [
                'date' => optional($d->submitted_at)->format('d/m/Y H:i'),
                'desc' => $desc,
                'status' => $d->status,
                'debit' => $debit,
                'credit' => $credit,
                'balance' => $running,
                'ref' => 'DEP-' . $d->id,
            ];
        });

        $totals = [
            'total_credit_approved' => (int) $deposits->where('status', 'approved')->sum('amount'),
            'closing_balance' => (int) $running,
        ];

        $statementNo = 'STMT-UMR-' . $account->id . '-' . now()->format('YmdHis');

        return view('shared.tabungan-umrah.statement-print', [
            'account' => $account,
            'from' => $from,
            'to' => $to,
            'openingBalance' => $openingBalance,
            'rows' => $rows,
            'totals' => $totals,
            'statementNo' => $statementNo,
            'printedBy' => $user,
            'contextLabel' => 'User',
        ]);
    }
}
