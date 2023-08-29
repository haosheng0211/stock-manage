<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Enums\SupplierType;
use App\Filament\Actions\ExportAction;
use App\Filament\Resources\SupplierResource;
use App\Models\Part;
use App\Models\Supplier;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
            ExportAction::make()
                ->builder(Supplier::query()->orderBy('name'))
                ->attributes([
                    'id'    => trans('validation.attributes.id'),
                    'name'  => trans('validation.attributes.name'),
                    'type'  => trans('validation.attributes.type'),
                    'phone' => trans('validation.attributes.phone'),
                ], ['id', 'name', 'type', 'phone'])
                ->visible(fn () => Auth::user()->can('export', Supplier::class)),
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
                        $records->each(function (Supplier $record) {
                            if ($record->parts->count() > 0) {
                                throw new \Exception('所選的供應商有關聯的零件，無法刪除。');
                            }

                            if ($record->contactPeople->count() > 0) {
                                throw new \Exception('所選的供應商有關聯的聯絡人，無法刪除。');
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
                ->visible(fn () => Auth::user()->can('bulkDelete', Supplier::class)),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('type')
                ->label(trans('validation.attributes.type'))
                ->options(SupplierType::asSelectArray()),
            SelectFilter::make('brand')
                ->label('品牌')
                ->query(function (Builder $query, $data) {
                    if (! blank($data['value'])) {
                        $query->whereHas('parts', function (Builder $query) use ($data) {
                            $query->where('brand', $data['value']);
                        });
                    }
                })
                ->options(Part::getBrands())
                ->searchable(),
        ];
    }
}
