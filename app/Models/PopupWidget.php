<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PopupWidget extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'is_enabled' => 'boolean',
        'include_paths' => 'array',
        'exclude_paths' => 'array',
        'show_on_mobile' => 'boolean',
        'show_on_desktop' => 'boolean',
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];
}
