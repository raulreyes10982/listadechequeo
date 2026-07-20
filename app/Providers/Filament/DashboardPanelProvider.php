<?php

namespace App\Providers\Filament;

use App\Filament\Pages\SecurityDashboard;          // ✅ namespace correcto
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\EnsurePasswordIsChanged;
use App\Filament\Pages\CambiarPassword;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->brandName('Lista de Chequeo')
            //->brandLogo(asset('images/logo.png'))
            //->favicon(asset('images/favicon.ico'))
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])

            // ✅ Auto-descubre Resources, Pages y Widgets de sus carpetas
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')

            // ✅ Solo listamos lo que NO se auto-descubre o necesita orden específico
            ->pages([
                SecurityDashboard::class,   // Reemplaza el Dashboard por defecto
                CambiarPassword::class,
            ])

            // ✅ Los widgets de Filament base que quieras mantener
            ->widgets([
                Widgets\AccountWidget::class,
                // Widgets\FilamentInfoWidget::class, // puedes quitarlo si no lo necesitas
                
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                EnsureUserIsActive::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsActive::class,
                EnsurePasswordIsChanged::class,
            ])
            ->navigationGroups([
                'Gestión de Usuarios',
                'Datos Personales',
                'Organización',
                'Localización',
                'Programación',
                'Permisos',
                'Equipos',
                'Reportes',
                'Novedades',
                
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => view('filament.components.qr-scanner-alpine')->render(),
            );
    }
}
