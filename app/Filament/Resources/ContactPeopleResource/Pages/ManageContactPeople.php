<?php

namespace App\Filament\Resources\ContactPeopleResource\Pages;

use App\Exports\ContactPeopleExport;
use App\Filament\Actions\ExportAction;
use App\Filament\Resources\ContactPeopleResource;
use App\Models\ContactPeople;
use App\Models\Supplier;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ManageContactPeople extends ManageRecords
{
    protected static string $resource = ContactPeopleResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
            ExportAction::make()
                ->exportable(ContactPeopleExport::class)
                ->attributes([
                    'id'                => trans('validation.attributes.id'),
                    'supplier.id'       => trans('validation.attributes.supplier_id'),
                    'supplier.name'     => trans('validation.attributes.supplier'),
                    'brands'            => trans('validation.attributes.brand'),
                    'english_name'      => trans('validation.attributes.english_name'),
                    'chinese_name'      => trans('validation.attributes.chinese_name'),
                    'residential_phone' => trans('validation.attributes.residential_phone'),
                    'mobile_phone'      => trans('validation.attributes.mobile_phone'),
                    'email'             => trans('validation.attributes.email'),
                    'line_id'           => trans('validation.attributes.line_id'),
                    'assistant_name'    => trans('validation.attributes.assistant_name'),
                    'assistant_phone'   => trans('validation.attributes.assistant_phone'),
                    'assistant_email'   => trans('validation.attributes.assistant_email'),
                ], ['id', 'supplier.id', 'supplier.name', 'english_name', 'chinese_name'])
                ->visible(fn () => Auth::user()->can('export', ContactPeople::class)),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ViewAction::make(),
            EditAction::make(),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            DeleteBulkAction::make()
                ->action(function (DeleteBulkAction $action, Collection $records) {
                    try {
                        DB::beginTransaction();
                        $records->each(function (ContactPeople $record) {
                            if ($record->parts->count() > 0) {
                                throw new \Exception('所選的聯絡人有關聯的零件，無法刪除。');
                            }

                            $record->delete();
                        });

                        $action->success();
                        DB::commit();
                    } catch (\Throwable $throwable) {
                        DB::rollBack();
                        $action->failureNotificationTitle($throwable->getMessage());
                        $action->failure();
                    }
                })
                ->visible(fn () => Auth::user()->can('bulkDelete', ContactPeople::class)),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('supplier_id')
                ->label(trans('validation.attributes.supplier'))
                ->options(Supplier::orderBy('name')->pluck('name', 'id'))
                ->searchable(),
        ];
    }
}
