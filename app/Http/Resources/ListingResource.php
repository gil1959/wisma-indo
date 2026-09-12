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
            'listing_category_id' => $this->listing_category_id,
            'listing_type' => $this->transaction_type,
            'full_address' => $this->address,
            'transaction_type' => $this->transaction_type, // dijual, disewakan
            'property_type' => $this->property_type,
            'price' => $this->price,
            'price_formatted' => 'Rp ' . number_format($this->price, 0, ',', '.'),
            'currency' => $this->currency,
            'price_type' => $this->price_type,
            'rental_period' => $this->rental_period,
            'min_rental' => $this->min_rental,
            'co_broke' => (int) $this->co_broke,
            'negotiable' => (int) $this->negotiable,
            'description' => $this->description,
            'cover_image' => $this->mediaUrl($this->cover_image),
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
            'imb' => (int) $this->imb,
            'pbb' => (int) $this->pbb,
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
            'category' => new ListingCategoryResource($this->whenLoaded('listingCategory')),
            'user' => new UserResource($this->whenLoaded('user')),
            'images' => $this->whenLoaded('images', function() {
                return $this->images->map(function($img) {
                    return [
                        'id' => $img->id,
                        'image_path' => $this->mediaUrl($img->image_path),
                        'is_primary' => $img->is_primary,
                    ];
                });
            }),
            
            // Status & Stats
            'status' => $this->status,
            'is_active' => $this->is_active,
            'views' => $this->views,
            
            // Promotions
            'is_premium' => (int) $this->is_premium,
            'premium_until' => $this->premium_until,
            'is_sundul' => (int) $this->is_sundul,
            'sundul_at' => $this->sundul_at,

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    private function mediaUrl($value)
    {
        if (!$value) return null;
        $path = parse_url($value, PHP_URL_PATH) ?: $value;
        if (strpos(ltrim($path, '/'), 'storage/') === 0) {
            return url('/' . ltrim($path, '/'));
        }
        return url($value);
    }
}
