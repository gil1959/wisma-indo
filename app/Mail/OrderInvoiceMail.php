<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public bool $isAdminCopy;

    public function __construct(Order $order, bool $isAdminCopy = false)
    {
        $this->order = $order;
        $this->isAdminCopy = $isAdminCopy;
    }

    public function build()
{
    $this->order->loadMissing('payments');

    $subject = 'Invoice ' . $this->order->invoice_number . ' - Wisma Indo';
    if ($this->isAdminCopy) {
        $subject = '[ADMIN COPY] ' . $subject;
    }

    return $this
        ->subject($subject)
        ->view('emails.order_invoice')
        ->with([
            'order' => $this->order,
            'isAdminCopy' => $this->isAdminCopy,
        ]);
}


}
