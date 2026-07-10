<?php

namespace App\Mail;

use App\Models\TabunganUmrahAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TabunganUmrahAccountVerifiedUserMail extends Mailable
{
    use Queueable, SerializesModels;

    public TabunganUmrahAccount $account;

    public function __construct(TabunganUmrahAccount $account)
    {
        $this->account = $account;
    }

    public function build()
    {
        return $this->subject('Tabungan Umrah Terverifikasi')
            ->view('emails.tabungan_umrah.account_verified_user');
    }
}
