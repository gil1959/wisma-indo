<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PartnerOrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public User $partner;

    public function __construct(Order $order, User $partner)
    {
        $this->order = $order;
        $this->partner = $partner;
    }

    public function build()
    {
        $this->order->loadMissing('payments');

        $subject = '[PARTNER] Notifikasi Order Baru - ' . $this->order->invoice_number . ' - Wisma Indo';

        return $this
            ->subject($subject)
            ->view('emails.orders.partner_invoice')
            ->with([
                'order' => $this->order,
                'partner' => $this->partner,
            ]);
    }
}
