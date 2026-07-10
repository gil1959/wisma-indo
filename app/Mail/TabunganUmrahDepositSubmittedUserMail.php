<?php

namespace App\Mail;

use App\Models\TabunganUmrahDeposit;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TabunganUmrahDepositSubmittedUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public TabunganUmrahDeposit $deposit;

    public function __construct(TabunganUmrahDeposit $deposit)
    {
        $this->deposit = $deposit;
    }

    public function build()
    {
        $mail = $this->subject('[Tabungan Umrah] Setoran Diterima - Menunggu Verifikasi')
            ->view('emails.tabungan_umrah.deposit_submitted_user');

        if (!empty($this->deposit->proof_image)) {
            $ext = pathinfo($this->deposit->proof_image, PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'bukti_pembayaran_tabungan_umrah_' . $this->deposit->id . '.' . $ext;
            $mail->attachFromStorageDisk('public', $this->deposit->proof_image, $filename);
        }

        return $mail;
    }
}
