<?php

namespace App\Mail;

use App\Models\TabunganUmrahAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TabunganUmrahAccountSubmittedUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public TabunganUmrahAccount $account;

    public function __construct(TabunganUmrahAccount $account)
    {
        $this->account = $account;
    }

    public function build()
    {
        return $this->subject('Registrasi Tabungan Umrah - Menunggu Verifikasi')
            ->view('emails.tabungan_umrah.account_submitted_user');
    }
}
