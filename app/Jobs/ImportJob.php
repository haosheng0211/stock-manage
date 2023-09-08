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
            $rows = $this->open($this->params['file']);
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

                Part::updateOrCreate(['supplier_id' => $item['supplier_id'], 'brand' => $item['brand'], 'part_number' => $item['part_number']], [
                    'contact_people_id' => $item['contact_people_id'],
                    'quantity'          => $item['quantity'],
                    'twd_price'         => $item['twd_price'],
                    'usd_price'         => $item['usd_price'],
                    'datecode'          => $item['datecode'],
                    'leadtime'          => $item['leadtime'],
                    'package'           => $item['leadtime'],
                    'description'       => $item['description'],
                ]);
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
            'quantity'          => (int) $row[2],
            'twd_price'         => (float) $row[3],
            'usd_price'         => (float) $row[4],
            'datecode'          => (string) $row[5],
            'leadtime'          => (string) $row[6],
            'package'           => (string) $row[7],
            'description'       => (string) $row[8],
            'contact_people_id' => (int) $row[9],
            'supplier_id'       => (int) $row[10],
        ];
    }

    public function validate(array $format): \Illuminate\Validation\Validator
    {
        return Validator::make($format, [
            'part_number'       => ['required', new PartNumberRule()],
            'quantity'          => ['required', 'numeric', 'min:1'],
            'supplier_id'       => ['required', 'integer', 'exists:suppliers,id'],
            'contact_people_id' => ['required', 'integer', Rule::exists('contact_people', 'id')->where(fn (Builder $builder) => $builder->where('supplier_id', $format['supplier_id']))],
            'twd_price'         => ['required', 'numeric', 'min:0'],
            'usd_price'         => ['required', 'numeric', 'min:0'],
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
            'status'        => DocumentStatus::FAILURE,
            'error_message' => $exception->getMessage(),
        ]);
    }

    public function output(array $errors): void
    {
        if (! count($errors)) {
            return;
        }

        $name = Carbon::now()->format('Ymd_His') . '_errors.xlsx';

        app('excel.export')->fileName($name)->data($errors)->output();

        if (! file_exists(config('excel.export.directory') . DIRECTORY_SEPARATOR . $name)) {
            return;
        }

        $this->files[] = $name;
    }
}
