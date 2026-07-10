<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\AdminBroadcastNotification;
use App\Services\WebPushService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function create()
    {
        $users = User::role('user')->select('id','name','email')->orderBy('name')->get();
        $partners = User::role('partner')->select('id','name','email')->orderBy('name')->get();

        return view('admin.notifications.create', compact('users', 'partners'));
    }

    public function store(Request $request, WebPushService $webPush)
    {
        $data = $request->validate([
            'target_role' => 'required|in:user,partner',
            'send_mode' => 'required|in:all,selected',
            'message' => 'required|string|max:1000',
            'recipient_ids' => 'array',
            'recipient_ids.*' => 'integer',
        ]);

        $query = User::role($data['target_role']);

        if ($data['send_mode'] === 'selected') {
            $ids = $data['recipient_ids'] ?? [];
            if (count($ids) === 0) {
                return back()->withErrors(['recipient_ids' => 'Pilih minimal 1 penerima.'])->withInput();
            }
            $query->whereIn('id', $ids);
        }

        $recipients = $query->get();

        foreach ($recipients as $u) {
            $u->notify(new AdminBroadcastNotification($data['message'], $data['target_role']));

            // push payload untuk service worker
            $payload = [
                'title' => 'Notifikasi dari Admin',
                'body' => $data['message'],
                'url' => $data['target_role'] === 'partner'
                    ? route('partner.notifications.index')
                    : route('user.notifications.index'),
            ];

            $webPush->sendToUserId((int)$u->id, $payload);
        }

        return redirect()->route('admin.notifications.create')->with('success', 'Notifikasi berhasil dikirim.');
    }
}
