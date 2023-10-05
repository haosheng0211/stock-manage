<?php

namespace App\Filament\Resources;

use App\Enums\SupplierType;
use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class SupplierResource extends Resource
{
    protected static ?string $label = '供應商';

    protected static ?string $model = Supplier::class;

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema(function (string $context) {
                $schema = [
                    TextInput::make('name')
                        ->label(trans('validation.attributes.name'))
                        ->unique(ignoreRecord: true)
                        ->required(),
                    TextInput::make('phone')
                        ->label(trans('validation.attributes.phone'))
                        ->required(),
                    Select::make('type')
                        ->label(trans('validation.attributes.type'))
                        ->options(SupplierType::asSelectArray())
                        ->required(),
                ];

                return $context === 'create' ? $schema : [Card::make($schema)];
            })
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(trans('validation.attributes.id'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label(trans('validation.attributes.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label(trans('validation.attributes.phone'))
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('type')
                    ->enum(SupplierType::asSelectArray())
                    ->label(trans('validation.attributes.type'))
                    ->sortable(),
                TagsColumn::make('brands')
                    ->label(trans('validation.attributes.brand')),
                TextColumn::make('contact_people_count')
                    ->label(trans('validation.attributes.contact_people_count'))
                    ->counts('contactPeople')
                    ->sortable(),
                TextColumn::make('parts_count')
                    ->label(trans('validation.attributes.parts_count'))
                    ->counts('parts')
                    ->sortable(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuppliers::route('/'),
            'view'  => Pages\ViewSupplier::route('/{record}'),
            'edit'  => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ContactPeopleRelationManager::class,
            RelationManagers\PartsRelationManager::class,
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['parts']);
    }
}
