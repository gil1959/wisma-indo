<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title', 'slug', 'content', 'is_active',
        'seo_image', 'meta_keywords', 'meta_title', 'meta_desc', 'social_title', 'social_desc'
    ];
}
