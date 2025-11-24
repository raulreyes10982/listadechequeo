<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Importación necesaria para usar URL::forceScheme

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Verifica si la aplicación está en modo 'production' (que es el caso de Railway)
        // y fuerza a Laravel a generar todas las URLs (incluyendo assets, CSS y JS)
        // usando el esquema HTTPS.
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }
    }
}