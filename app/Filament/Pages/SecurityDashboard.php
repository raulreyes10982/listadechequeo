<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\GuardiasHoyWidget;
use App\Filament\Widgets\PermisosVencenWidget;
use App\Filament\Widgets\PuestosSinCoberturaWidget;
use App\Filament\Widgets\ReportesPendientesWidget;
use App\Filament\Widgets\ReportesDonaWidget;
use App\Filament\Widgets\ReportesResumenWidget;
use App\Filament\Widgets\ReportesPorMesChart;
use App\Filament\Widgets\ReporteTecnicoStatsWidget;
use App\Filament\Widgets\ReporteTecnicoTablaWidget;
use App\Filament\Widgets\SecurityStatsWidget;
use Filament\Pages\Page;

class SecurityDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title           = 'Centro de Control';
    protected static ?string $slug            = 'dashboard';
    protected static ?int    $navigationSort  = -1;

    protected static string $view = 'filament.pages.security-dashboard';

    // ✅ El dashboard principal es accesible para cualquier usuario autenticado
    // No tiene restricción de rol porque es la página de inicio del panel
    public static function canAccess(): bool
    {
        return auth()->check();
    }

    public string $activeTab = 'seguridad';

    public function getTitle(): string
    {
        return 'Centro de Control de Seguridad';
    }

    public function getTabs(): array
    {
        return [
            'seguridad' => [
                'label' => 'Seguridad',
                'icon'  => 'heroicon-m-shield-check',
            ],
            'reportes' => [
                'label' => 'Reportes',
                'icon'  => 'heroicon-m-document-text',
            ],
            'permisos' => [
                'label' => 'Permisos',
                'icon'  => 'heroicon-m-clipboard-document-check',
            ],
            'tecnico' => [
                'label' => 'Técnico',
                'icon'  => 'heroicon-m-wrench-screwdriver',
            ],
        ];
    }

    public function getWidgetsByTab(): array
    {
        return [
            // ── Seguridad — guardias, puestos, cobertura ──────────────────
            'seguridad' => [
                SecurityStatsWidget::class,
                GuardiasHoyWidget::class,
                PuestosSinCoberturaWidget::class,
            ],

            // ── Reportes — solo reportes (tarjetas resumen + gráfica + pendientes) ──
            'reportes' => [
                ReportesResumenWidget::class,   // tarjetas clickeables
                ReportesPorMesChart::class,     // gráfica barras 6 meses
                ReportesDonaWidget::class,      // 3 donas: estado, prioridad, categoría
                ReportesPendientesWidget::class, // tabla con filtros de período
            ],

            // ── Permisos — separados de Reportes ─────────────────────────
            'permisos' => [
                PermisosVencenWidget::class,
            ],

            // ── Técnico — equipos y fallas ────────────────────────────────
            'tecnico' => [
                ReporteTecnicoStatsWidget::class,
                ReporteTecnicoTablaWidget::class,
            ],
        ];
    }

    public function getWidgets(): array
    {
        return $this->getWidgetsByTab()[$this->activeTab] ?? [];
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function getColumns(): int | array
    {
        return 1;
    }
}
