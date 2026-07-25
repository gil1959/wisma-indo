<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadActivity extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function lead()
    {
        return $this->belongsTo(BuyerLead::class, 'buyer_lead_id');
    }
}
