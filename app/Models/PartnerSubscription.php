<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PartnerSubscription extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    protected $casts = [
        'ends_at' => 'datetime',
        'paid_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(PartnerPackage::class, 'partner_package_id');
    }
}
