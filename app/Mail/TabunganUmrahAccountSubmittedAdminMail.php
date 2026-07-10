<?php

namespace App\Mail;

use App\Models\TabunganUmrahAccount;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TabunganUmrahAccountSubmittedAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public TabunganUmrahAccount $account;

    public function __construct(TabunganUmrahAccount $account)
    {
        $this->account = $account;
    }

    public function build()
    {
        return $this->subject('Ada Registrasi Tabungan Umrah Baru (Butuh Verifikasi)')
            ->view('emails.tabungan_umrah.account_submitted_admin');
    }
}
