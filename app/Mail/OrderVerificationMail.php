<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Order $order;
    public string $result; // approved | rejected
    public bool $isAdminCopy;

    public function __construct(Order $order, string $result = 'approved', bool $isAdminCopy = false)
    {
        $this->order = $order;
        $this->result = $result;
        $this->isAdminCopy = $isAdminCopy;
    }

    public function build()
{
    $this->order->loadMissing('payments');

    $label = $this->result === 'approved' ? 'Terverifikasi (Lunas)' : 'Ditolak';
    $subject = "Status Pembayaran {$label} - {$this->order->invoice_number}";
    if ($this->isAdminCopy) {
        $subject = '[ADMIN COPY] ' . $subject;
    }

    return $this
        ->subject($subject)
        ->view('emails.order_verification')
        ->with([
            'order' => $this->order,
            'result' => $this->result,
            'isAdminCopy' => $this->isAdminCopy,
        ]);
}

}
