<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyerLead extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function partner()
    {
        return $this->belongsTo(User::class, 'partner_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
    
    public function activities()
    {
        return $this->hasMany(LeadActivity::class, 'buyer_lead_id')->latest();
    }
}
