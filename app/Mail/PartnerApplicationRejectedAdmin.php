<?php

namespace App\Mail;

use App\Models\PartnerApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PartnerApplicationRejectedAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PartnerApplication $app) {}

    public function build()
    {
        return $this
            ->subject('Pendaftaran Partner Ditolak')
            ->markdown('emails.partner.application_rejected_admin', [
                'app' => $this->app,
            ]);
    }
}
