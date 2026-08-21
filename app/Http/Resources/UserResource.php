<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar ? url($this->avatar) : null,
            'slug' => $this->slug,
            'bio' => $this->bio,
            'address' => $this->address,
            'full_address' => $this->full_address,
            'sub_district' => $this->sub_district,
            'whatsapp_template' => $this->whatsapp_template,
            'suspended_until' => $this->suspended_until,
            'is_verified' => $this->email_verified_at !== null,
            'roles' => $this->roles->pluck('name'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'quota' => [
                'total_bought' => (int) (\App\Models\TopupTransaction::where('user_id', $this->id)->where('status', 'success')->sum('amount') ?? 0),
                'used' => \App\Models\Listing::where('user_id', $this->id)->count(),
                'remaining' => $this->quota ? (int) $this->quota->listing_quota : 0,
            ],
        ];
    }
}
