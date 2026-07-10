<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateWithdrawalRequest;
use Illuminate\Http\Request;

class AffiliateWithdrawalController extends Controller
{
    public function index(Request $request)
    {
       $status = trim((string) $request->get('status', 'all'));
$q = trim((string) $request->get('q'));

$query = AffiliateWithdrawalRequest::query()->with('user');

if (in_array($status, ['pending','approved','declined','paid'], true)) {
    $query->where('status', $status);
}

        if ($q !== '') {
            $query->whereHas('user', function ($u) use ($q) {
                $u->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
            });
        }

        $requests = $query->orderByDesc('id')->paginate(20)->withQueryString();

        return view('admin.affiliate.withdrawals.index', compact('requests', 'status', 'q'));
    }

    public function show(AffiliateWithdrawalRequest $requestModel)
    {
        $requestModel->load('user', 'reviewer');

        return view('admin.affiliate.withdrawals.show', [
            'req' => $requestModel
        ]);
    }

    public function updateStatus(Request $request, AffiliateWithdrawalRequest $requestModel)
    {
        $data = $request->validate([
            'status' => ['required', 'in:approved,declined,paid'],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $requestModel->status = $data['status'];
$requestModel->admin_note = $data['admin_note'] ?: null;
$requestModel->reviewed_by = auth()->id();
$requestModel->reviewed_at = now();

// paid_at harus konsisten (kalau status dibalik manual)
if ($data['status'] === 'paid') {
    $requestModel->paid_at = now();
} else {
    $requestModel->paid_at = null;
}

$requestModel->save();

/**
 * SYNC STATUS COMMISSION:
 * - Semua order komisi yang "ikut" request withdrawal ini akan ikut berubah statusnya.
 * - Admin tetap bisa ubah manual dari halaman order affiliate (kalau perlu override).
 */
$requestModel->load('items.order');

foreach ($requestModel->items as $item) {
    if (!$item->order) continue;

    $item->order->affiliate_commission_status = $data['status'];
    $item->order->affiliate_commission_set_by = auth()->id();
    $item->order->affiliate_commission_set_at = now();
    $item->order->save();
}

return back()->with('success', 'Status penarikan berhasil diupdate.');

    }
}
