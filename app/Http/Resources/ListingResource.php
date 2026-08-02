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
            'transaction_type' => $this->transaction_type, // dijual, disewakan
            'property_type' => $this->property_type,
            'price' => $this->price,
            'price_formatted' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'currency' => $this->currency,
            'price_type' => $this->price_type,
            'rental_period' => $this->rental_period,
            'min_rental' => $this->min_rental,
            'co_broke' => $this->co_broke,
            'negotiable' => $this->negotiable,
            'description' => $this->description,
            'cover_image' => $this->cover_image ? url($this->cover_image) : null,
            'youtube_url' => $this->youtube_url,
            
            // Property Specifics
            'land_area' => $this->land_area,
            'building_area' => $this->building_area,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'maid_bedrooms' => $this->maid_bedrooms,
            'maid_bathrooms' => $this->maid_bathrooms,
            'floors' => $this->floors,
            'build_year' => $this->build_year,
            'certificate' => $this->certificate,
            'imb' => $this->imb,
            'pbb' => $this->pbb,
            'condition' => $this->condition,
            'furnished_status' => $this->furnished_status,
            'electricity' => $this->electricity,
            'water_source' => $this->water_source,
            'carport' => $this->carport,
            'garage' => $this->garage,
            'car_access' => $this->car_access,
            'facing_direction' => $this->facing_direction,
            'facilities' => is_string($this->facilities) ? json_decode($this->facilities, true) : $this->facilities,
            'surroundings' => is_string($this->surroundings) ? json_decode($this->surroundings, true) : $this->surroundings,

            // Goods / Services Specifics
            'brand' => $this->brand,
            'service_area' => $this->service_area,

            // Contact Info
            'phone' => $this->phone,
            'whatsapp' => $this->whatsapp,
            
            // Location
            'location' => $this->location,
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
                        'image_path' => $img->image_path ? url($img->image_path) : null,
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
