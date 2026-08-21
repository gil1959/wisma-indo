<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledAutopost extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_id',
        'platforms',
        'caption',
        'media_url',
        'scheduled_at',
        'status',
        'error_message'
    ];

    protected $casts = [
        'platforms' => 'array',
        'scheduled_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
