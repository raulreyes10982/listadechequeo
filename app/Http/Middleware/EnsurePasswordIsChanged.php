<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePasswordIsChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->must_change_password) {
            return $next($request);
        }

        // Permitir acceso solo a la página de cambio y al logout
        $rutasCambio = [
            route('filament.dashboard.pages.cambiar-password', [], false),
            route('filament.dashboard.auth.logout', [], false),
        ];

        if (in_array($request->path(), array_map(fn($r) => ltrim($r, '/'), $rutasCambio))) {
            return $next($request);
        }

        return redirect()->route('filament.dashboard.pages.cambiar-password')
            ->with('warning', '⚠️ Debes cambiar tu contraseña antes de continuar.');
    }
}
