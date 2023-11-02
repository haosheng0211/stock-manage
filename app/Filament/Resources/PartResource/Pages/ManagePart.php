<?php

declare(strict_types=1);

namespace App\Filament\Resources\PartResource\Pages;

use App\Filament\Actions\EditBulkAction;
use App\Filament\Actions\ExportAction;
use App\Filament\Actions\ImportAction;
use App\Filament\Resources\PartResource;
use App\Models\Part;
use App\Models\Supplier;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Pages\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ManagePart extends ManageRecords
{
    protected static string $resource = PartResource::class;

    protected function getActions(): array
    {
        return [
            CreateAction::make(),
            ImportAction::make()
                ->visible(fn () => Auth::user()->can('import', Part::class)),
            ExportAction::make()
                ->builder(Part::with(['supplier', 'contactPeople'])->orderBy('part_number'))
                ->attributes([
                    'part_number'         => trans('validation.attributes.part_number'),
                    'brand'               => trans('validation.attributes.brand'),
                    'quantity'            => trans('validation.attributes.quantity'),
                    'twd_price'           => trans('validation.attributes.twd_price'),
                    'usd_price'           => trans('validation.attributes.usd_price'),
                    'datecode'            => trans('validation.attributes.datecode'),
                    'leadtime'            => trans('validation.attributes.leadtime'),
                    'package'             => trans('validation.attributes.package'),
                    'description'         => trans('validation.attributes.description'),
                    'updated_at'          => trans('validation.attributes.updated_at'),
                    'contactPeople.id'    => trans('validation.attributes.contact_people_id'),
                    'contactPeople.phone' => trans('validation.attributes.phone'),
                    'contactPeople.name'  => trans('validation.attributes.contact_people'),
                    'supplier.id'         => trans('validation.attributes.supplier_id'),
                    'supplier.name'       => trans('validation.attributes.supplier'),
                ], ['part_number', 'brand', 'quantity'])
                ->visible(fn () => Auth::user()->can('export', Part::class)),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ViewAction::make(),
            EditAction::make()->using(function (Model $record, array $data): Model {
                $is_updated_at = data_get($data, 'is_updated_at', true);

                if (! $is_updated_at) {
                    $record->timestamps = false;
                }

                $record->update($data);

                return $record;
            }),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            EditBulkAction::make()
                ->visible(fn () => Auth::user()->can('bulkUpdate', Part::class)),
            DeleteBulkAction::make()
                ->visible(fn () => Auth::user()->can('bulkDelete', Part::class)),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('supplier_id')
                ->label(trans('validation.attributes.supplier'))
                ->options(Supplier::orderBy('name')->pluck('name', 'id'))
                ->searchable(),
            SelectFilter::make('brand')
                ->label(trans('validation.attributes.brand'))
                ->options(Part::getBrands())
                ->searchable(),
            Filter::make('updated_at')
                ->form([
                    Grid::make()->schema([
                        DatePicker::make('updated_from')
                            ->label(trans('validation.attributes.updated_from')),
                        DatePicker::make('updated_until')
                            ->label(trans('validation.attributes.updated_until')),
                    ]),
                ])
                ->query(function (Builder $query, array $data) {
                    $query->when($data['updated_from'], fn ($query, $date) => $query->whereDate('updated_at', '>=', $date));
                    $query->when($data['updated_until'], fn ($query, $date) => $query->whereDate('updated_at', '<=', $date));
                }),
        ];
    }
}
