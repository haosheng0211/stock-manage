<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithMapping;

class PartErrorExport implements FromArray, WithMapping
{
    public function __construct(
        protected array $failures
    ) {}

    public function array(): array
    {
        return $this->failures;
    }

    public function map($row): array
    {
        return array_values($row);
    }
}
