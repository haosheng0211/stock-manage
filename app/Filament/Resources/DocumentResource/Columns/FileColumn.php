<?php

namespace App\Filament\Resources\DocumentResource\Columns;

use Filament\Tables\Columns\Concerns\HasColor;
use Filament\Tables\Columns\ViewColumn;

class FileColumn extends ViewColumn
{
    use HasColor;

    protected string $view = 'filament.tables.columns.document-files';
}
