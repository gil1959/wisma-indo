<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationCenterController extends Controller
{
    public function indexUser(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        return view('user.notifications.index', compact('notifications'));
    }

    public function indexPartner(Request $request)
    {
        $notifications = $request->user()->notifications()->latest()->paginate(20);
        return view('partner.notifications.index', compact('notifications'));
    }

    public function markRead(Request $request, string $id)
    {
        $n = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $n->markAsRead();
        return back();
    }

    public function readAll(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        return back();
    }
}
