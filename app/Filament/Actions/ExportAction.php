<?php

namespace App\Filament\Actions;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Jobs\ExportJob;
use App\Models\Document;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Actions\Action;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExportAction extends Action
{
    use CanCustomizeProcess;

    protected Builder $builder;

    protected array $attributes = [];

    protected array $default_attributes = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->form(function () {
            return [
                TextInput::make('name')
                    ->label(trans('validation.attributes.name'))
                    ->default(Carbon::now()->format('Ymd_His') . '_' . $this->builder->getModel()->getTable())
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
                        'split'    => '分割檔案',
                    ])
                    ->reactive(),
                TextInput::make('split_size')
                    ->label('分割筆數')
                    ->hidden(fn ($get) => ! in_array('split', $get('options')))
                    ->default(30000),
            ];
        });
        $this->label('匯出');
        $this->color('warning');
        $this->action(function (array $data) {
            try {
                DB::beginTransaction();
                $document = Document::create([
                    'type'    => DocumentType::EXPORT,
                    'user_id' => Auth::id(),
                    'model'   => get_class($this->builder->getModel()),
                    'status'  => DocumentStatus::PROCESS,
                ]);

                ExportJob::dispatchNow($document, $this->builder, $this->attributes, $data);
                DB::commit();
                $this->success();
            } catch (\Throwable $exception) {
                DB::rollBack();
                $this->failureNotificationTitle('項目匯出失敗。');
                $this->failure();
            }
        });
        $this->successNotificationTitle('項目匯出完成，請至「檔案」下載。');
    }

    public function builder(Builder $builder): static
    {
        $this->builder = $builder;

        return $this;
    }

    public function attributes(array $attributes, array $default = []): static
    {
        $this->attributes = $attributes;
        $this->default_attributes = $default;

        return $this;
    }

    public static function getDefaultName(): ?string
    {
        return 'export';
    }
}
