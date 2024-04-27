<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class Part extends Model
{
    protected $fillable = [
        'supplier_id',
        'contact_people_id',
        'part_number',
        'brand',
        'description',
        'package',
        'datecode',
        'leadtime',
        'quantity',
        'twd_price',
        'usd_price',
        'updated_at',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function contactPeople(): BelongsTo
    {
        return $this->belongsTo(ContactPeople::class);
    }

    public static function getBrands(?int $supplier_id = null): Collection
    {
        $builder = self::query();

        if (! blank($supplier_id)) {
            $builder->where('supplier_id', $supplier_id);
        }

        return $builder->whereNot('brand', '')->orderBy('brand')->distinct()->pluck('brand', 'brand');
    }
}
