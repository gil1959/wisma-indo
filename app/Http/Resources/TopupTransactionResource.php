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
            'quota_amount' => $this->amount,
            'amount' => $this->price,
            'amount_formatted' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'unique_code' => $this->unique_code,
            'total_amount' => $this->total_amount,
            'total_amount_formatted' => 'Rp ' . number_format($this->total_amount, 0, ',', '.'),
            'status' => $this->status,
            'payment_method' => $this->payment_method,
            'payment_channel' => $this->payment_channel,
            'payment_reference' => $this->payment_reference,
            'payment_url' => $this->payment_url,
            'proof_of_payment' => $this->proof_of_payment ? url('storage/' . $this->proof_of_payment) : null,
            'note' => $this->note,
            'package' => $this->whenLoaded('topupPackage', function() {
                return [
                    'id' => $this->topupPackage->id,
                    'name' => $this->topupPackage->name,
                    'price' => $this->topupPackage->price,
                    'quota_amount' => $this->topupPackage->amount,
                ];
            }),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
