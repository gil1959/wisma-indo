<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AdminBroadcastNotification extends Notification
{
    use Queueable;

    protected string $message;
    protected string $targetRole;

    public function __construct(string $message, string $targetRole)
    {
        $this->message = $message;
        $this->targetRole = $targetRole;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Notifikasi dari Admin',
            'message' => $this->message,
            'role' => $this->targetRole,
            'url' => $this->targetRole === 'partner'
                ? route('partner.notifications.index')
                : route('user.notifications.index'),
        ];
    }
}
