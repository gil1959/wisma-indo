<?php

namespace App\Mail;

use App\Models\TabunganUmrahAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TabunganUmrahAccountRejectedUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public TabunganUmrahAccount $account;

    public function __construct(TabunganUmrahAccount $account)
    {
        $this->account = $account;
    }

    public function build()
    {
        return $this->subject('Registrasi Tabungan Umrah Ditolak')
            ->view('emails.tabungan_umrah.account_rejected_user');
    }
}
