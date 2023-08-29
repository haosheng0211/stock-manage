<?php

Route::get('/', function () {
    return redirect(config('filament.home_url'));
});
