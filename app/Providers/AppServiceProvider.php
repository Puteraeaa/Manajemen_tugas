<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        FilamentColor::register([
            'primary' => Color::hex('#1687FE'),
        ]);
    }
}
