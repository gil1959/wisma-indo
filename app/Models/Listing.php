<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

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
        $primary = $this->images()->where('is_primary', true)->first();
        return $primary ? $primary->image_path : ($this->images()->first()->image_path ?? 'images/placeholder.jpg');
    }
}
