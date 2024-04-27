<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $casts = [
        'original_file' => 'array',
        'files'         => 'array',
    ];

    protected $fillable = [
        'user_id',
        'type',
        'model',
        'files',
        'status',
        'error_message',
        'original_file',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
