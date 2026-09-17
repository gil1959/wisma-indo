<?php
namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ConfirmAccountDeletion extends Notification
{
    public function __construct(public string $url) {}
    public function via($notifiable) { return ['mail']; }
    public function toMail($notifiable)
    {
        return (new MailMessage)->subject('Konfirmasi penghapusan akun WismaIndo')
            ->line('Kami menerima permintaan penghapusan akun WismaIndo Anda beserta data terkait.')
            ->line('Membuka tautan belum menghapus akun. Anda perlu menyetujui penghapusan pada halaman konfirmasi. Tautan berlaku selama 30 menit.')
            ->action('Tinjau penghapusan akun', $this->url)
            ->line('Jika bukan Anda yang meminta, abaikan email ini. Akun Anda tetap tersedia.');
    }
}
