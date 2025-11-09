<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        // Para cada registro, actualizar qr_expira al formato correcto
        $puestos = \App\Models\PuestoSeguridad::all();
        
        foreach ($puestos as $puesto) {
            if ($puesto->qr_expira && is_string($puesto->qr_expira)) {
                $puesto->qr_expira = Carbon::parse($puesto->qr_expira);
                $puesto->save();
            }
        }
    }

    public function down(): void
    {
        // No es reversible de forma segura
    }
};