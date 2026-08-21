<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoogleIndexingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'action_type',
        'status_code',
        'response_message',
    ];
}
