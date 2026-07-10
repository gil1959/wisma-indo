<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PartnerProductStatusMail extends Mailable
{
    public function __construct(
        public string $partnerName,
        public string $productType,
        public string $productTitle,
        public string $status,
        public ?string $note
    ) {}

    public function build()
    {
        return $this->subject("Status Produk Partner: {$this->status}")
            ->view('emails.partner-product-status');
    }
}
