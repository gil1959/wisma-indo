<?php

namespace App\Mail;

use App\Models\PartnerApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PartnerApplicationApprovedAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public PartnerApplication $app, public User $user) {}

    public function build()
    {
        return $this
            ->subject('Pendaftaran Partner Disetujui')
            ->markdown('emails.partner.application_approved_admin', [
                'app' => $this->app,
                'user' => $this->user,
            ]);
    }
}
