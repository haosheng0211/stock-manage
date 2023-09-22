<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use Filament\Resources\Pages\ManageRecords;
use Filament\Tables\Actions\DeleteAction;

class ManageDocuments extends ManageRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getActions(): array
    {
        return [
        ];
    }

    protected function getTableActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
