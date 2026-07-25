<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerPackage extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
        'benefits' => 'array',
        'valid_until' => 'datetime',
    ];

    public function subscriptions()
    {
        return $this->hasMany(PartnerSubscription::class);
    }
}
