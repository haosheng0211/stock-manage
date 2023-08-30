<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use App\Filament\Actions\EditBulkAction;
use App\Models\ContactPeople;
use App\Models\Part;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PartsRelationManager extends RelationManager
{
    protected static ?string $label = '零件';

    protected static string $relationship = 'parts';

    protected static ?string $recordTitleAttribute = 'part_number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
                    Select::make('supplier_id')
                        ->label(trans('validation.attributes.supplier'))
                        ->preload()
                        ->required()
                        ->searchable()
                        ->relationship('supplier', 'name'),
                    Select::make('contact_people_id')
                        ->label(trans('validation.attributes.contact_people'))
                        ->relationship('contactPeople', 'english_name', function (Builder $query, callable $get) {
                            $query->where('supplier_id', $get('supplier_id'));
                        })
                        ->getOptionLabelFromRecordUsing(fn (Model $record) => $record->name)
                        ->preload()
                        ->required()
                        ->searchable(),
                ]),
                TextInput::make('part_number')
                    ->label(trans('validation.attributes.part_number'))
                    ->required(),
                TextInput::make('brand')
                    ->label(trans('validation.attributes.brand'))
                    ->required(),
                TextInput::make('quantity')
                    ->label(trans('validation.attributes.quantity'))
                    ->integer(),
                Textarea::make('description')
                    ->label(trans('validation.attributes.description')),
                Grid::make(3)->schema([
                    TextInput::make('package')
                        ->label(trans('validation.attributes.package')),
                    TextInput::make('datecode')
                        ->label(trans('validation.attributes.datecode')),
                    TextInput::make('leadtime')
                        ->label(trans('validation.attributes.leadtime')),
                ]),
                Grid::make()->schema([
                    TextInput::make('twd_price')
                        ->label(trans('validation.attributes.twd_price'))
                        ->numeric(),
                    TextInput::make('usd_price')
                        ->label(trans('validation.attributes.usd_price'))
                        ->numeric(),
                ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('part_number')
                    ->label(trans('validation.attributes.part_number'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('brand')
                    ->label(trans('validation.attributes.brand'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(trans('validation.attributes.quantity')),
                TextColumn::make('twd_price')
                    ->label(trans('validation.attributes.twd_price')),
                TextColumn::make('usd_price')
                    ->label(trans('validation.attributes.usd_price')),
                TextColumn::make('datecode')
                    ->label(trans('validation.attributes.datecode')),
                TextColumn::make('leadtime')
                    ->label(trans('validation.attributes.leadtime')),
                TextColumn::make('package')
                    ->label(trans('validation.attributes.package')),
                TextColumn::make('description')
                    ->label(trans('validation.attributes.description')),
                TextColumn::make('contactPeople.phone')
                    ->label(trans('validation.attributes.phone')),
                TextColumn::make('contactPeople.name')
                    ->label(trans('validation.attributes.contact_people')),
                TextColumn::make('updated_at')
                    ->date()
                    ->label(trans('validation.attributes.updated_at'))
                    ->sortable(),
            ])
            ->defaultSort('part_number');
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
            EditBulkAction::make()
                ->visible(fn () => auth()->user()->can('bulkUpdate', Part::class)),
            DeleteBulkAction::make()
                ->visible(fn () => auth()->user()->can('bulkDelete', Part::class)),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('brand')
                ->label(trans('validation.attributes.brand'))
                ->options(Part::getBrands($this->getOwnerRecord()->getKey()))
                ->searchable(),
            SelectFilter::make('contact_people_id')
                ->label(trans('validation.attributes.contact_people'))
                ->options(ContactPeople::where('supplier_id', $this->getOwnerRecord()->getKey())->get()->pluck('name', 'id'))
                ->searchable(),
        ];
    }
}
