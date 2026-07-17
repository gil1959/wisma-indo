<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\Listing;

class ListingApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $listing;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Listing $listing)
    {
        $this->listing = $listing;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Iklan Anda Disetujui! - Wisma Indo')
                    ->view('emails.listing-approved');
    }
}
