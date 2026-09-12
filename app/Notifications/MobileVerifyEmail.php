<?php
namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

class MobileVerifyEmail extends VerifyEmail
{
    protected function verificationUrl($notifiable)
    {
        $relative = URL::temporarySignedRoute('mobile.verify', now()->addMinutes(60), [
            'id' => $notifiable->getKey(), 'hash' => sha1($notifiable->getEmailForVerification()),
        ], false);
        return url($relative);
    }
}
