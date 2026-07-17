<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Notifications\PushNotification;

class NotificationController extends Controller
{
    public function create()
    {
        $users = User::all();
        return view('admin.notifications.create', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'target' => 'required',
            'user_ids' => 'required_if:target,specific|array',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('notifications', 'public');
            $imagePath = '/storage/' . $path;
        }

        $notificationData = [
            'title' => $request->title,
            'message' => $request->message,
            'image' => $imagePath,
        ];

        if ($request->target == 'all') {
            $users = User::all();
            foreach ($users as $user) {
                $user->notify(new PushNotification($notificationData));
            }
        } else {
            $users = User::whereIn('id', $request->user_ids)->get();
            foreach ($users as $user) {
                $user->notify(new PushNotification($notificationData));
            }
        }

        return redirect()->route('admin.notifications.create')->with('success', 'Notifikasi berhasil dikirim!');
    }
}
