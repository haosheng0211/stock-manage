<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Vtiful\Kernel\Excel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('excel.export', function () {
            return new Excel([
                'path' => storage_path('app/public/' . config('excel.export.directory')),
            ]);
        });
        $this->app->bind('excel.import', function () {
            return new Excel([
                'path' => storage_path('app/public/' . config('excel.import.directory')),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
