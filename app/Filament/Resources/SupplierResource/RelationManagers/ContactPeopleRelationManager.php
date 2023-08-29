<?php

namespace App\Filament\Resources\SupplierResource\RelationManagers;

use App\Models\ContactPeople;
use App\Models\Part;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ContactPeopleRelationManager extends RelationManager
{
    protected static ?string $label = '聯絡人';

    protected static string $relationship = 'contactPeople';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
            ]);
    }

    protected function getTableHeaderActions(): array
    {
        return [
            CreateAction::make(),
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
}
