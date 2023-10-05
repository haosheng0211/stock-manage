<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'type',
        'phone',
    ];

    public function parts(): HasMany
    {
        return $this->hasMany(Part::class);
    }

    public function brands(): Attribute
    {
        return Attribute::get(function () {
            return $this->parts->pluck('brand')->unique()->values()->toArray();
        });
    }

    public function contactPeople(): HasMany
    {
        return $this->hasMany(ContactPeople::class);
    }
}
