<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingPackage extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'benefits' => 'array',
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(ListingTransaction::class);
    }
}
