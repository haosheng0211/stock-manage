<?php

namespace App\Exports;

use App\Models\Supplier;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;

class SupplierExport implements FromCollection
{
    public function __construct(
        protected array $columns = [],
        protected array $options = [],
    ) {}

    public function headings(): array
    {
        if (isset($this->options['headings'])) {
            return array_values($this->columns);
        }

        return [];
    }

    public function collection(): Collection
    {
        return Supplier::orderBy('name')->get();
    }

    public function map($row): array
    {
        return array_map(function ($column) use ($row) {
            return data_get($row, $column);
        }, array_keys($this->columns));
    }
}
