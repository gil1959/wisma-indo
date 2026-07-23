<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankPartner extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo', 'is_active', 'order'];
}
