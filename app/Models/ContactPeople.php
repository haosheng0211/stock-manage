<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContactPeople extends Model
{
    protected $casts = [
        'brands' => 'array',
    ];

    protected $fillable = [
        'supplier_id',
        'brands',
        'english_name',
        'chinese_name',
        'residential_phone',
        'mobile_phone',
        'email',
        'line_id',
        'assistant_name',
        'assistant_email',
        'assistant_phone',
    ];

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function name(): Attribute
    {
        return Attribute::get(function () {
            $chinese_name = $this->attributes['chinese_name'];
            $english_name = $this->attributes['english_name'];

            if ($chinese_name && $english_name) {
                return "{$chinese_name} ({$english_name})";
            }

            return $chinese_name ?? $english_name;
        });
    }

    public function phone(): Attribute
    {
        return Attribute::get(function () {
            return $this->attributes['residential_phone'] ?? $this->attributes['mobile_phone'];
        });
    }
}
