<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerWithdrawalRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerWithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $status = trim((string) $request->get('status', 'all'));
        $q = trim((string) $request->get('q', ''));

        $query = PartnerWithdrawalRequest::query()
            ->with(['partner','reviewer'])
            ->latest();

        if (in_array($status, ['pending','approved','rejected'], true)) {
            $query->where('status', $status);
        }

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('email', 'like', "%{$q}%")
                  ->orWhere('bank_name', 'like', "%{$q}%")
                  ->orWhere('account_number', 'like', "%{$q}%")
                  ->orWhereHas('partner', function ($p) use ($q) {
                      $p->where('name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                  });
            });
        }

        return view('admin.partners.withdrawals.index', [
            'items' => $query->paginate(25)->withQueryString(),
            'status' => $status,
            'q' => $q,
        ]);
    }

    public function show(PartnerWithdrawalRequest $withdrawal)
    {
        return view('admin.partners.withdrawals.show', [
            'w' => $withdrawal->load(['partner','reviewer']),
        ]);
    }

    public function update(Request $request, PartnerWithdrawalRequest $withdrawal)
    {
        $data = $request->validate([
            'action' => ['required', 'in:approve,reject'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($withdrawal, $data) {

            $w = PartnerWithdrawalRequest::query()->whereKey($withdrawal->id)->lockForUpdate()->first();
            if (!$w) return;

            // hanya proses kalau masih pending (biar gak dobel pindah saldo)
            if ($w->status !== 'pending') {
                return;
            }

            $partner = User::query()->whereKey($w->partner_id)->lockForUpdate()->first();
            if (!$partner) return;

            $amt = (int) $w->amount;

            if ($data['action'] === 'approve') {
                // pending -> withdrawn
                $partner->partner_balance_pending = (int) $partner->partner_balance_pending - $amt;
                $partner->partner_balance_withdrawn = (int) $partner->partner_balance_withdrawn + $amt;
                $partner->save();

                $w->status = 'approved';
            } else {
                // pending -> available
                $partner->partner_balance_pending = (int) $partner->partner_balance_pending - $amt;
                $partner->partner_balance_available = (int) $partner->partner_balance_available + $amt;
                $partner->save();

                $w->status = 'rejected';
            }

            $w->admin_note = $data['admin_note'] ?? null;
            $w->reviewed_by = auth()->id();
            $w->reviewed_at = now();
            $w->save();
        });

        return back()->with('success', 'Request penarikan berhasil diproses.');
    }

    public function destroy(PartnerWithdrawalRequest $withdrawal)
    {
        $withdrawal->delete();
        return back()->with('success', 'Request penarikan dihapus.');
    }
}
