<?php

namespace App\Filament\Actions;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Document;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class ExportAction extends Action
{
    use CanCustomizeProcess;

    protected array $attributes = [];

    protected array $default_attributes = [];

    protected string $exportable;

    protected function setUp(): void
    {
        parent::setUp();
        $this->form(function () {
            return [
                TextInput::make('name')
                    ->label(trans('validation.attributes.name'))
                    ->default(Carbon::now()->format('Ymd_His'))
                    ->required(),
                CheckboxList::make('attributes')
                    ->label(trans('validation.attributes.attributes'))
                    ->columns(4)
                    ->options($this->attributes)
                    ->default($this->default_attributes)
                    ->required(),
                CheckboxList::make('options')
                    ->label('可選配置')
                    ->columns(4)
                    ->options([
                        'headings' => '顯示表頭',
                    ])
                    ->reactive(),
            ];
        });
        $this->label('匯出');
        $this->color('warning');
        $this->action(function (array $data) {
            $document = Document::create([
                'type'    => DocumentType::EXPORT,
                'user_id' => Auth::id(),
                'model'   => $this->getModelName(),
                'status'  => DocumentStatus::PROCESS,
            ]);

            try {
                $file_path = config('excel.export.directory') . DIRECTORY_SEPARATOR . $data['name'] . '.xlsx';

                Excel::store(app($this->exportable, ['columns' => $this->transformAttributes($data['attributes']), 'options' => $data['options']]), $file_path, 'public');
                $document->update([
                    'files' => [
                        $file_path,
                    ],
                    'status' => DocumentStatus::SUCCESS,
                ]);
                $this->success();
            } catch (\Throwable $exception) {
                $document->update([
                    'status'        => DocumentStatus::FAILURE,
                    'error_message' => $exception->getMessage(),
                ]);
                $this->failureNotificationTitle('項目匯出失敗。');
                $this->failure();
            }
        });
        $this->successNotificationTitle('項目匯出完成，請至「檔案」下載。');
    }

    public function exportable(string $exportable): static
    {
        $this->exportable = $exportable;

        return $this;
    }

    public function attributes(array $attributes, array $default = []): static
    {
        $this->attributes = $attributes;
        $this->default_attributes = $default;

        return $this;
    }

    public function getModelName(): string
    {
        $manage = get_class($this->livewire);

        return $manage::getResource()::getModel();
    }

    public function transformAttributes(array $attributes): array
    {
        $list = [];

        foreach ($attributes as $attribute) {
            $list[$attribute] = $this->attributes[$attribute];
        }

        return $list;
    }

    public static function getDefaultName(): ?string
    {
        return 'export';
    }
}
