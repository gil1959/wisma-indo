<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BuyerLeadResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'message' => $this->message,
            'source' => $this->source,
            'listing' => new ListingResource($this->whenLoaded('listing')),
            'partner' => new UserResource($this->whenLoaded('partner')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
