<?php

namespace App\Filament\Resources;

use App\Enums\UserPermission;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $label = '使用者';

    protected static ?string $model = User::class;

    protected static ?string $navigationGroup = '系統';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    TextInput::make('name')
                        ->label(trans('validation.attributes.name'))
                        ->required(),
                    TextInput::make('email')
                        ->label(trans('validation.attributes.email'))
                        ->required(),
                    TextInput::make('password')
                        ->label(trans('validation.attributes.password'))
                        ->password()
                        ->required(fn (string $context): bool => $context === 'create')
                        ->dehydrated(fn ($state) => filled($state))
                        ->afterStateHydrated(function (TextInput $component) {
                            $component->state('');
                        })
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state)),
                    CheckboxList::make('permissions')
                        ->label(trans('validation.attributes.permissions'))
                        ->columns(3)
                        ->options(UserPermission::asSelectArray()),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(trans('validation.attributes.name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(trans('validation.attributes.email'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('updated_at')
                    ->date()
                    ->label(trans('validation.attributes.updated_at'))
                    ->sortable(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view'   => Pages\ViewUser::route('/{record}'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
