<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'file_path',
        'status',
        'total_rows',
        'processed_rows',
        'failed_rows',
        'error_log',
    ];

    protected $casts = [
        'error_log' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
