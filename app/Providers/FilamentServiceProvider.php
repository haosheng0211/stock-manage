<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Filament\Tables\Columns\Column;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Filament::serving(function () {
            Filament::registerViteTheme('resources/css/filament.css');
        });

        Column::configureUsing(function (Column $column) {
            return $column->toggleable();
        });
    }
}
