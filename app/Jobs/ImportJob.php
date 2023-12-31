<?php

namespace App\Jobs;

use App\Enums\DocumentStatus;
use App\Models\Document;
use App\Models\Part;
use App\Rules\PartNumberRule;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ImportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public array $files = [];

    public function __construct(public Document $document, public array $params)
    {
    }

    public function handle(): void
    {
        try {
            $rows = array_filter($this->open($this->params['file']), function ($item) {
                // 使用 array_filter 移除所有空值
                $filtered = array_filter($item, function ($value) {
                    return trim($value) !== '';
                });

                // 如果過濾後的數組不為空，則保留該數組
                return ! empty($filtered);
            });
            $errors = [];

            DB::beginTransaction();

            foreach ($rows as $row) {
                if (count($row) !== 11) {
                    throw new \Exception('匯入格式錯誤');
                }

                $item = $this->format($row);
                $validate = $this->validate($item);

                if ($validate->fails()) {
                    $errors[] = array_merge($row, [implode(', ', $validate->errors()->all())]);

                    continue;
                }

                $wheres = ['supplier_id' => $item['supplier_id'], 'brand' => $item['brand'], 'part_number' => $item['part_number']];

                if ($part = Part::where($wheres)->first()) {
                    $part->update([
                        'contact_people_id' => $item['contact_people_id'],
                        'quantity'          => $item['quantity'],
                        'datecode'          => $item['datecode'],
                        'leadtime'          => $item['leadtime'],
                        'package'           => $item['leadtime'],
                        'description'       => $item['description'],
                        'updated_at'        => Carbon::now(),
                    ]);
                } else {
                    Part::create(array_merge($wheres, [
                        'contact_people_id' => $item['contact_people_id'],
                        'quantity'          => $item['quantity'],
                        'twd_price'         => is_numeric($item['twd_price']) ? $item['twd_price'] : 0,
                        'usd_price'         => is_numeric($item['usd_price']) ? $item['usd_price'] : 0,
                        'datecode'          => $item['datecode'],
                        'leadtime'          => $item['leadtime'],
                        'package'           => $item['leadtime'],
                        'description'       => $item['description'],
                    ]));
                }
            }

            $this->output($errors);
            $this->success();
            DB::commit();
        } catch (\Throwable $exception) {
            DB::rollBack();
            $this->failure($exception);
        }
    }

    public function open(string $name)
    {
        return app('excel.import')->openFile($name)->openSheet()->setSkipRows(1)->getSheetData();
    }

    public function format(array $row): array
    {
        return [
            'part_number'       => (string) $row[0],
            'brand'             => (string) $row[1],
            'quantity'          => force_format_number($row[2]),
            'twd_price'         => (string) $row[3] ?: 0,
            'usd_price'         => (string) $row[4] ?: 0,
            'datecode'          => (string) $row[5],
            'leadtime'          => (string) $row[6],
            'package'           => (string) $row[7],
            'description'       => (string) $row[8],
            'contact_people_id' => force_format_number($row[9]),
            'supplier_id'       => force_format_number($row[10]),
        ];
    }

    public function validate(array $format): \Illuminate\Validation\Validator
    {
        return Validator::make($format, [
            'part_number'       => ['required', new PartNumberRule()],
            'quantity'          => ['required', 'numeric', 'min:1'],
            'supplier_id'       => ['required', 'integer', 'exists:suppliers,id'],
            'contact_people_id' => ['required', 'integer', Rule::exists('contact_people', 'id')->where(fn (Builder $builder) => $builder->where('supplier_id', $format['supplier_id']))],
            'twd_price'         => ['nullable', 'numeric', 'min:0'],
            'usd_price'         => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    public function success(): void
    {
        $this->document->update([
            'files'  => $this->files,
            'status' => DocumentStatus::SUCCESS,
        ]);
    }

    public function failure(\Throwable $exception): void
    {
        Log::error('匯入失敗', [
            'exception'   => $exception->getMessage(),
            'document_id' => $this->document->id,
        ]);

        $this->document->update([
            'status' => DocumentStatus::FAILURE,
        ]);
    }

    public function output(array $errors): void
    {
        if (! count($errors)) {
            return;
        }

        $name = Carbon::now()->format('Ymd_His') . '_errors.xlsx';

        app('excel.export')->fileName($name)->data($errors)->output();

        if (! Storage::exists(config('excel.export.directory') . DIRECTORY_SEPARATOR . $name)) {
            return;
        }

        $this->files[] = config('excel.export.directory') . DIRECTORY_SEPARATOR . $name;
    }
}
