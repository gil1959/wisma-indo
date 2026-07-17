<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

use App\Models\Listing;

class ListingRejected extends Mailable
{
    use Queueable, SerializesModels;

    public $listing;
    public $note;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Listing $listing, $note)
    {
        $this->listing = $listing;
        $this->note = $note;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Revisi Diperlukan untuk Iklan Anda - Wisma Indo')
                    ->view('emails.listing-rejected');
    }
}
