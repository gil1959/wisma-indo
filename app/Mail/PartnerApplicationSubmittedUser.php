<?php

namespace App\Mail;

use App\Models\PartnerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PartnerApplicationSubmittedUser extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PartnerApplication $app) {}

    public function build()
    {
        return $this
            ->subject('Pendaftaran Partner - Menunggu Verifikasi Admin')
            ->markdown('emails.partner.application_submitted_user', [
                'app' => $this->app,
            ]);
    }
}
