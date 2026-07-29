<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'type' => $this->type,
            'listing_type' => $this->listing_type, // dijual, disewakan
            'price' => $this->price,
            'price_formatted' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'description' => $this->description,
            
            // Property Specifics
            'land_area' => $this->land_area,
            'building_area' => $this->building_area,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'floors' => $this->floors,
            'certificate' => $this->certificate,
            'condition' => $this->condition,
            'furnishing' => $this->furnishing,
            'electricity' => $this->electricity,
            'carports' => $this->carports,
            'facing' => $this->facing,
            
            // Location
            'city' => $this->city,
            'district' => $this->district,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'maps_url' => $this->maps_url,
            
            // Relations
            'category' => new ListingCategoryResource($this->whenLoaded('category')),
            'user' => new UserResource($this->whenLoaded('user')),
            'images' => $this->whenLoaded('images', function() {
                return $this->images->map(function($img) {
                    return [
                        'id' => $img->id,
                        'image_path' => url('storage/' . $img->image_path),
                        'is_primary' => $img->is_primary,
                    ];
                });
            }),
            
            // Status & Stats
            'status' => $this->status,
            'is_active' => $this->is_active,
            'views' => $this->views,
            
            // Promotions
            'is_premium' => $this->is_premium,
            'premium_until' => $this->premium_until,
            'is_sundul' => $this->is_sundul,
            'sundul_at' => $this->sundul_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
