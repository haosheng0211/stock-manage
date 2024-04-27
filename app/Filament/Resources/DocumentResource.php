<?php

namespace App\Filament\Resources;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Filament\Resources\DocumentResource\Columns\FileColumn;
use App\Filament\Resources\DocumentResource\Pages;
use App\Models\ContactPeople;
use App\Models\Document;
use App\Models\Part;
use App\Models\Supplier;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;

class DocumentResource extends Resource
{
    protected static ?string $label = '檔案';

    protected static ?string $model = Document::class;

    protected static ?int $navigationSort = 1;

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label(trans('validation.attributes.user')),
                BadgeColumn::make('type')
                    ->enum(DocumentType::asSelectArray())
                    ->label(trans('validation.attributes.type')),
                BadgeColumn::make('model')
                    ->enum([
                        ContactPeople::class => trans('validation.attributes.contact_people'),
                        Part::class          => trans('validation.attributes.part'),
                        Supplier::class      => trans('validation.attributes.supplier'),
                    ])
                    ->label(trans('validation.attributes.model')),
                FileColumn::make('original_file')
                    ->label(trans('validation.attributes.original_file'))
                    ->color('success'),
                FileColumn::make('files')
                    ->label(trans('validation.attributes.file'))
                    ->color(fn ($record) => $record->type === DocumentType::EXPORT ? 'primary' : 'danger'),
                BadgeColumn::make('status')
                    ->enum(DocumentStatus::asSelectArray())
                    ->label(trans('validation.attributes.status'))
                    ->colors(DocumentStatus::colors())
                    ->tooltip(fn (Document|Model $record) => $record->error_message),
                TextColumn::make('updated_at')
                    ->label(trans('validation.attributes.updated_at'))
                    ->sortable()
                    ->dateTime(),
            ])->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageDocuments::route('/'),
        ];
    }
}
