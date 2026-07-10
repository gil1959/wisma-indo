<?php

namespace App\Mail;

use App\Models\PartnerApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PartnerApplicationApprovedUser extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public PartnerApplication $app,
        public User $user,
        public string $plainPassword
    ) {}

    public function build()
    {
        return $this
            ->subject('Akun Partner Kamu Sudah Diverifikasi')
            ->markdown('emails.partner.application_approved_user', [
                'app' => $this->app,
                'user' => $this->user,
                'plainPassword' => $this->plainPassword,
            ]);
    }
}
