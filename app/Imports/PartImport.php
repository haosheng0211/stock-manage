<?php

namespace App\Imports;

use App\Enums\DocumentStatus;
use App\Exports\PartErrorExport;
use App\Models\Document;
use App\Models\Part;
use App\Rules\PartNumberRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Cell\StringValueBinder;

class PartImport extends StringValueBinder implements ToArray, WithMapping, WithStartRow, WithCustomValueBinder, SkipsEmptyRows
{
    public array $failures = [];

    public function __construct(
        protected Document $document
    ) {}

    public function array(array $array): void
    {
        $files = [];

        foreach ($array as $row) {
            $validator = $this->validator($row);

            if ($validator->fails()) {
                $row['errors'] = $validator->errors()->all();
                $this->failures[] = $row;

                continue;
            }

            $wheres = ['supplier_id' => $row['supplier_id'], 'brand' => $row['brand'], 'part_number' => $row['part_number']];

            if ($part = Part::where($wheres)->first()) {
                $part->update([
                    'contact_people_id' => $row['contact_people_id'],
                    'quantity'          => $row['quantity'],
                    'datecode'          => $row['datecode'],
                    'leadtime'          => $row['leadtime'],
                    'package'           => $row['leadtime'],
                    'description'       => $row['description'],
                    'updated_at'        => now(),
                ]);
            } else {
                Part::create(array_merge($wheres, [
                    'contact_people_id' => $row['contact_people_id'],
                    'quantity'          => $row['quantity'],
                    'twd_price'         => is_numeric($row['twd_price']) ? $row['twd_price'] : 0,
                    'usd_price'         => is_numeric($row['usd_price']) ? $row['usd_price'] : 0,
                    'datecode'          => $row['datecode'],
                    'leadtime'          => $row['leadtime'],
                    'package'           => $row['leadtime'],
                    'description'       => $row['description'],
                ]));
            }
        }

        $this->outputFailures();

        $this->document->update([
            'files'  => $files,
            'status' => count($this->failures) ? DocumentStatus::FAILURE : DocumentStatus::SUCCESS,
        ]);
    }

    public function map($row): array
    {
        return [
            'part_number'       => trim($row[0]),
            'brand'             => trim($row[1]),
            'quantity'          => force_format_number($row[2]),
            'twd_price'         => $row[3] ?: 0,
            'usd_price'         => $row[4] ?: 0,
            'datecode'          => $row[5],
            'leadtime'          => $row[6],
            'package'           => $row[7],
            'description'       => $row[8],
            'contact_people_id' => force_format_number($row[9]),
            'supplier_id'       => force_format_number($row[10]),
        ];
    }

    public function startRow(): int
    {
        return 2;
    }

    public function validator(array $row): Validator
    {
        return validator($row, [
            'part_number'       => ['required', new PartNumberRule()],
            'quantity'          => ['required', 'numeric', 'min:1'],
            'supplier_id'       => ['required', 'integer', Rule::exists('suppliers', 'id')],
            'contact_people_id' => ['required', 'integer', Rule::exists('contact_people', 'id')->where(fn (Builder $builder) => $builder->where('supplier_id', $row['supplier_id']))],
            'twd_price'         => ['nullable', 'numeric', 'min:0'],
            'usd_price'         => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    public function outputFailures(): void
    {
        if (! count($this->failures)) {
            return;
        }

        $name = Carbon::now()->format('Ymd_His') . '_errors.xlsx';
        $path = config('excel.export.directory') . DIRECTORY_SEPARATOR . $name;

        Excel::store(new PartErrorExport(failures: $this->failures), $path);

        $this->document->update([
            'files' => [
                $path,
            ],
        ]);
    }
}
