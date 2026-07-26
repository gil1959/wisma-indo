<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($listing) {
            if (empty($listing->slug) || $listing->isDirty('title')) {
                $slug = \Illuminate\Support\Str::slug($listing->title);
                $originalSlug = $slug;
                $count = 1;

                while (static::where('slug', $slug)->where('id', '!=', $listing->id ?? 0)->exists()) {
                    $slug = "{$originalSlug}-{$count}";
                    $count++;
                }

                $listing->slug = $slug;
            }
        });
    }

    protected $guarded = ['id'];

    protected $casts = [
        'facilities' => 'array',
        'surroundings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(ListingImage::class);
    }

    public function getPrimaryImageAttribute()
    {
        if ($this->cover_image) {
            return $this->cover_image;
        }
        $primary = $this->images()->where('is_primary', true)->first();
        return $primary ? $primary->image_path : ($this->images()->first()->image_path ?? 'images/placeholder.jpg');
    }

    public function listingCategory()
    {
        return $this->belongsTo(ListingCategory::class, 'listing_category_id');
    }

    public function favoriteListings()
    {
        return $this->hasMany(FavoriteListing::class);
    }

    public function getUrlAttribute()
    {
        $prefix = match ($this->category) {
            'property' => 'properti',
            'services' => 'jasa',
            'goods' => 'barang',
            default => 'properti',
        };
        return url("/{$prefix}/{$this->slug}");
    }
}
