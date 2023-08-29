<?php

namespace App\Jobs;

use App\Enums\DocumentStatus;
use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ExportJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public array $files = [];

    public function __construct(public Document $document, public Builder $builder, public array $attributes, public array $params)
    {
    }

    public function handle(): void
    {
        try {
            $this->builder->chunk(in_array('split', $this->params['options']) ? $this->params['split_size'] : $this->builder->count(), function (Collection $collection, int $index) {
                $name = $this->params['name'] . '_' . $index . '.xlsx';
                $items = [];

                if (in_array('headings', $this->params['options'])) {
                    $items[] = $this->headings($this->params['attributes']);
                }

                foreach ($collection as $model) {
                    $items[] = $this->format($model);
                }

                $this->output($name, $items);

                $this->files[] = config('excel.export.directory') . DIRECTORY_SEPARATOR . $name;
            });
            $this->success();
        } catch (\Throwable $exception) {
            $this->failure($exception);
        }
    }

    public function output(string $name, array $data): void
    {
        app('excel.export')->fileName($name)->data($data)->output();
    }

    public function format(Model $model): array
    {
        $item = [];

        foreach (array_keys($this->attributes) as $attribute) {
            if (in_array($attribute, $this->params['attributes'])) {
                $item[$attribute] = data_get($model, $attribute);

                if ($item[$attribute] instanceof Carbon) {
                    $item[$attribute] = $item[$attribute]->toDateString();
                }
            }
        }

        return $item;
    }

    public function headings(array $attributes): array
    {
        $attributes = array_filter($this->attributes, fn ($value, $attribute) => in_array($attribute, $attributes), ARRAY_FILTER_USE_BOTH);

        return count($attributes) ? array_values($attributes) : [];
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
        Log::error('匯出失敗', [
            'exception' => $exception->getMessage(),
            'id'        => $this->document->id,
        ]);

        $this->document->update([
            'status' => DocumentStatus::FAILURE,
        ]);
    }
}
