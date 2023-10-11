<?php

namespace App\Filament\Actions;

use App\Models\Part;
use App\Models\Supplier;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Support\Actions\Concerns\CanCustomizeProcess;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class EditBulkAction extends BulkAction
{
    use CanCustomizeProcess;

    protected function setUp(): void
    {
        parent::setUp();
        $this->icon('heroicon-s-pencil');
        $this->form([Select::make('supplier_id')
            ->label(trans('validation.attributes.supplier'))
            ->options(Supplier::orderBy('name')->pluck('name', 'id'))
            ->preload()
            ->reactive()
            ->searchable(),
            Select::make('contact_people_id')
                ->label(trans('validation.attributes.contact_people'))
                ->options(function (callable $get) {
                    if (blank($get('supplier_id'))) {
                        return [];
                    }

                    return Supplier::find($get('supplier_id'))->contactPeople->pluck('name', 'id');
                })
                ->preload()
                ->required(fn (callable $get) => ! blank($get('supplier_id')))
                ->searchable(),
            TextInput::make('brand')
                ->label(trans('validation.attributes.brand'))
                ->datalist(Part::getBrands()),
            CheckboxList::make('options')
                ->label('可選配置')
                ->options([
                    'force_update' => '強制覆蓋',
                ]),
        ]);
        $this->color('primary');
        $this->label('編輯所選項目');
        $this->action(function () {
            $this->process(function (Collection $records, array $data) {
                if ((! $data['supplier_id'] && ! $data['contact_people_id']) && ! $data['brand']) {
                    return;
                }

                try {
                    DB::beginTransaction();

                    foreach ($records as $record) {
                        $attributes = [
                            'brand'             => $data['brand'] ?: $record->brand,
                            'supplier_id'       => $data['supplier_id'] ?: $record->supplier_id,
                            'contact_people_id' => $data['contact_people_id'] ?: $record->contact_people_id,
                        ];
                        $force_update = isset($data['options']) && in_array('force_update', $data['options']);

                        $this->updatePart($record, $attributes, $force_update);
                    }

                    DB::commit();
                } catch (\Throwable $throwable) {
                    DB::rollBack();
                    $this->failureNotificationTitle($throwable->getMessage());
                    $this->failure();
                }
            });

            $this->success();
        });
        $this->successNotificationTitle('項目已編輯');
        $this->deselectRecordsAfterCompletion();
    }

    public static function getDefaultName(): ?string
    {
        return 'edit';
    }

    public function updatePart(Part $record, array $attributes, bool $force_update = false): void
    {
        $part = Part::where(['supplier_id' => $attributes['supplier_id'], 'part_number' => $record->part_number, 'brand' => $attributes['brand']])->first();

        if ($part) {
            if ($part->id !== $record->id && ! $force_update) {
                return;
            }

            $part->delete();
            $record->update($attributes);

            return;
        }

        $record->update($attributes);
    }
}
