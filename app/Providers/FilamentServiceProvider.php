<?php

namespace App\Providers;

use Filament\Facades\Filament;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Filament::registerRenderHook(
            'head.end',
            fn (): string => <<<HTML
                <style>
                    /* Panel de filtros con scroll */
                    .fi-ta-filters {
                        max-height: 80vh !important;
                        overflow-y: auto !important;
                    }
                </style>
            HTML
        );
    }
}
