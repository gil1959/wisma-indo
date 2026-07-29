<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SurveyScheduleResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'survey_date' => $this->survey_date,
            'survey_time' => $this->survey_time,
            'notes' => $this->notes,
            'status' => $this->status, // pending, confirmed, completed, cancelled
            'listing' => new ListingResource($this->whenLoaded('listing')),
            'partner' => new UserResource($this->whenLoaded('partner')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
