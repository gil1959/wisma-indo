<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AffiliateApprovalController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q'));

        $users = User::query()
            ->whereDoesntHave('roles', fn($r) => $r->where('name', 'admin'))
            ->where('affiliate_status', 'pending')
            ->when($q !== '', function ($qq) use ($q) {
                $qq->where(function ($w) use ($q) {
                    $w->where('name', 'like', "%{$q}%")
                      ->orWhere('email', 'like', "%{$q}%")
                      ->orWhere('phone', 'like', "%{$q}%");
                });
            })
            ->orderByDesc('affiliate_requested_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.affiliate.requests.index', compact('users', 'q'));
    }

    public function show(User $user)
    {
        abort_if($user->hasRole('admin'), 404);

        return view('admin.affiliate.requests.show', [
            'user' => $user,
        ]);
    }

    public function approve(Request $request, User $user)
{
    abort_if($user->hasRole('admin'), 404);

    $data = $request->validate([
        'note' => ['nullable', 'string', 'max:2000'],
    ]);

    $user->affiliate_status = 'approved';
    $user->is_affiliate = true;

    $user->affiliate_reviewed_at = now();
    $user->affiliate_reviewed_by = auth()->id();
    $user->affiliate_review_note = $data['note'] ?: null;

    $user->save();

    return redirect()->route('admin.affiliate.requests.index')
        ->with('success', 'User berhasil di-approve untuk akses affiliate.');
}


    public function decline(Request $request, User $user)
    {
        abort_if($user->hasRole('admin'), 404);

        $data = $request->validate([
            'note' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $user->affiliate_status = 'declined';
        $user->is_affiliate = false;

        $user->affiliate_reviewed_at = now();
        $user->affiliate_reviewed_by = auth()->id();
        $user->affiliate_review_note = $data['note'];

        $user->save();

        return redirect()->route('admin.affiliate.requests.index')->with('success', 'Pengajuan affiliate ditolak.');
    }
}
