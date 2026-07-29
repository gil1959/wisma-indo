<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TopupTransactionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'amount' => $this->amount,
            'amount_formatted' => 'Rp ' . number_format($this->amount, 0, ',', '.'),
            'unique_code' => $this->unique_code,
            'total_amount' => $this->amount + $this->unique_code,
            'total_amount_formatted' => 'Rp ' . number_format($this->amount + $this->unique_code, 0, ',', '.'),
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_channel' => $this->payment_channel,
            'payment_reference' => $this->payment_reference,
            'proof_of_payment' => $this->proof_of_payment ? url('storage/' . $this->proof_of_payment) : null,
            'note' => $this->note,
            'package' => $this->whenLoaded('package', function() {
                return [
                    'id' => $this->package->id,
                    'name' => $this->package->name,
                    'price' => $this->package->price,
                    'quota_amount' => $this->package->quota_amount,
                ];
            }),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
