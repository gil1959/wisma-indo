<?php

namespace App\Mail;

use App\Models\PartnerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PartnerApplicationSubmittedAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PartnerApplication $app) {}

    public function build()
    {
        return $this
            ->subject('Ada Pendaftaran Partner Baru')
            ->markdown('emails.partner.application_submitted_admin', [
                'app' => $this->app,
            ]);
    }
}
