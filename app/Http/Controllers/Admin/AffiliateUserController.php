<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffiliateUserController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        // List: semua user yang affiliate (dan juga yang belum affiliate kalau search dipakai)
        $users = User::query()
            ->whereDoesntHave('roles', fn($r) => $r->where('name', 'admin')) // konsisten sama guard di UserController
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('is_affiliate')
            ->orderBy('name')
            ->paginate(15);

        // Stats affiliate per user: links/clicks/conversions + order count + sum komisi
        // Pake subquery biar gak bikin N+1 query gila.
        $ids = $users->pluck('id')->all();

        $stats = [];
        if (!empty($ids)) {
            $linkAgg = DB::table('affiliate_links')
                ->selectRaw('user_id, COUNT(*) as links, COALESCE(SUM(clicks),0) as clicks, COALESCE(SUM(conversions),0) as conversions')
                ->whereIn('user_id', $ids)
                ->groupBy('user_id')
                ->get()
                ->keyBy('user_id');

            $orderAgg = DB::table('orders')
                ->selectRaw("
                    affiliate_user_id as user_id,
                    COUNT(*) as orders,
                    COALESCE(SUM(CASE WHEN affiliate_commission_status='pending' THEN affiliate_commission_amount ELSE 0 END),0) as comm_pending,
                    COALESCE(SUM(CASE WHEN affiliate_commission_status='approved' THEN affiliate_commission_amount ELSE 0 END),0) as comm_approved,
                    COALESCE(SUM(CASE WHEN affiliate_commission_status='paid' THEN affiliate_commission_amount ELSE 0 END),0) as comm_paid
                ")
                ->whereIn('affiliate_user_id', $ids)
                ->groupBy('affiliate_user_id')
                ->get()
                ->keyBy('user_id');

            foreach ($ids as $id) {
                $la = $linkAgg->get($id);
                $oa = $orderAgg->get($id);

                $stats[$id] = [
                    'links' => (int) ($la->links ?? 0),
                    'clicks' => (int) ($la->clicks ?? 0),
                    'conversions' => (int) ($la->conversions ?? 0),
                    'orders' => (int) ($oa->orders ?? 0),
                    'comm_pending' => (int) ($oa->comm_pending ?? 0),
                    'comm_approved' => (int) ($oa->comm_approved ?? 0),
                    'comm_paid' => (int) ($oa->comm_paid ?? 0),
                ];
            }
        }

        return view('admin.users.affiliate', compact('users', 'stats', 'q'));
    }

    public function update(Request $request, User $user)
    {
        if ($user->hasRole('admin')) {
            abort(404);
        }

        $data = $request->validate([
            'is_affiliate' => ['nullable', 'boolean'],
            'affiliate_commission_type' => ['nullable', 'in:percent,fixed'],
            'affiliate_commission_value' => ['nullable', 'numeric', 'min:0'],
        ]);

        $isAffiliate = (bool) ($data['is_affiliate'] ?? false);

        $user->is_affiliate = $isAffiliate;

        if ($isAffiliate) {
            $user->affiliate_commission_type = $data['affiliate_commission_type'] ?? 'percent';
            $user->affiliate_commission_value = $data['affiliate_commission_value'] ?? 0;
        } else {
            // kalau dimatiin, bersihin setting biar rapih
            $user->affiliate_commission_type = null;
            $user->affiliate_commission_value = null;
        }

        $user->save();

        return back()->with('success', 'Setting affiliate user berhasil disimpan.');
    }
}
