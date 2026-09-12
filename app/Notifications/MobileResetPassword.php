<?php
namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class MobileResetPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)->subject('Atur ulang password WismaIndo')
            ->line('Kami menerima permintaan untuk mengatur ulang password akun Anda.')
            ->action('Atur ulang password', url('/api/v1/auth/password-link') . '?' . http_build_query(['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()]))
            ->line('Jika Anda tidak meminta perubahan ini, abaikan email ini.');
    }
}
