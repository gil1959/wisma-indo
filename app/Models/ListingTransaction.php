<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingTransaction extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function listingPackage()
    {
        return $this->belongsTo(ListingPackage::class);
    }

    public function offlinePaymentMethod()
    {
        return $this->belongsTo(OfflinePaymentMethod::class);
    }
}
