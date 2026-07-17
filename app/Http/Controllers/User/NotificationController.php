<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(10);
        return view('front.user.notifications.index', compact('notifications'));
    }

    public function show($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        
        // Mark as read if not already
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        return view('front.user.notifications.show', compact('notification'));
    }
}
