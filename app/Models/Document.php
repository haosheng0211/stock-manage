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
        'type',
        'user_id',
        'model',
        'original_file',
        'files',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
