<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactPeopleResource\Pages;
use App\Models\ContactPeople;
use App\Models\Part;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;

class ContactPeopleResource extends Resource
{
    protected static ?string $label = '聯絡人';

    protected static ?string $model = ContactPeople::class;

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('supplier_id')
                    ->label(trans('validation.attributes.supplier'))
                    ->preload()
                    ->required()
                    ->searchable()
                    ->relationship('supplier', 'name'),
                Select::make('brands')
                    ->label(trans('validation.attributes.brand'))
                    ->options(Part::getBrands())
                    ->multiple()
                    ->searchable(),
                Grid::make()
                    ->schema([
                        TextInput::make('english_name')
                            ->label(trans('validation.attributes.english_name'))
                            ->required(function (callable $get) {
                                return blank($get('chinese_name'));
                            })
                            ->reactive(),
                        TextInput::make('chinese_name')
                            ->label(trans('validation.attributes.chinese_name'))
                            ->required(function (callable $get) {
                                return blank($get('english_name'));
                            })
                            ->reactive(),
                    ]),
                Grid::make()
                    ->schema([
                        TextInput::make('residential_phone')
                            ->label(trans('validation.attributes.residential_phone'))
                            ->required(function (callable $get) {
                                return blank($get('mobile_phone'));
                            })
                            ->reactive(),
                        TextInput::make('mobile_phone')
                            ->label(trans('validation.attributes.mobile_phone'))
                            ->required(function (callable $get) {
                                return blank($get('residential_phone'));
                            })
                            ->reactive(),
                    ]),
                Grid::make()
                    ->schema([
                        TextInput::make('email')
                            ->label(trans('validation.attributes.email')),
                        TextInput::make('line_id')
                            ->label(trans('validation.attributes.line_id')),
                    ]),
                Grid::make(3)
                    ->schema([
                        TextInput::make('assistant_name')
                            ->label(trans('validation.attributes.assistant_name')),
                        TextInput::make('assistant_phone')
                            ->label(trans('validation.attributes.assistant_phone')),
                        TextInput::make('assistant_email')
                            ->label(trans('validation.attributes.assistant_email')),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label(trans('validation.attributes.id'))
                    ->searchable(),
                TextColumn::make('chinese_name')
                    ->label(trans('validation.attributes.chinese_name'))
                    ->searchable(),
                TextColumn::make('english_name')
                    ->label(trans('validation.attributes.english_name'))
                    ->searchable(),
                TagsColumn::make('brands')
                    ->label(trans('validation.attributes.brand'))
                    ->searchable(),
                TextColumn::make('supplier.name')
                    ->label(trans('validation.attributes.supplier'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('residential_phone')
                    ->label(trans('validation.attributes.residential_phone'))
                    ->searchable(),
                TextColumn::make('mobile_phone')
                    ->label(trans('validation.attributes.mobile_phone'))
                    ->searchable(),
                TextColumn::make('email')
                    ->label(trans('validation.attributes.email'))
                    ->searchable(),
                TextColumn::make('line_id')
                    ->label(trans('validation.attributes.line_id'))
                    ->searchable(),
                TextColumn::make('parts_count')
                    ->label(trans('validation.attributes.parts_count'))
                    ->counts('parts'),
            ])
            ->defaultSort('supplier.name');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageContactPeople::route('/'),
        ];
    }
}
