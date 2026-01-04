 <?php

use App\Models\PuestoSeguridad;
use Illuminate\Support\Str;
use Carbon\Carbon;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "🔐 GENERANDO TOKENS QR PARA PUESTOS...\n";
echo "=====================================\n\n";

$resultados = [];

PuestoSeguridad::all()->each(function($puesto) use (&$resultados) {
    echo "Procesando: {$puesto->codigo} ({$puesto->puesto})... ";
    
    try {
        // GENERAR NUEVO TOKEN SEGURO
        $nuevoToken = hash('sha256', 
            Str::uuid() . config('app.key') . microtime(true) . $puesto->id
        );
        
        $puesto->qr_token = $nuevoToken;
        $puesto->qr_expira = Carbon::now()->addDays(30);
        $puesto->qr_generado_en = Carbon::now();
        
        // Guardar cambios
        $guardado = $puesto->save();
        
        if ($guardado) {
            $tokenCorto = substr($nuevoToken, 0, 10) . '...';
            echo "✅ Token: {$tokenCorto}\n";
            
            $resultados[] = [
                'codigo' => $puesto->codigo,
                'token_generado' => 'NUEVO',
                'token_corto' => $tokenCorto,
                'expira' => $puesto->qr_expira->format('d/m/Y')
            ];
        } else {
            echo "❌ Error al guardar\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
});

echo "\n📊 RESUMEN DE TOKENS GENERADOS:\n";
echo "================================\n";
foreach ($resultados as $r) {
    echo "{$r['codigo']}: {$r['token_generado']} - {$r['token_corto']} (Expira: {$r['expira']})\n";
}

// Verificación final
echo "\n✅ VERIFICACIÓN FINAL:\n";
echo "=====================\n";
echo "Puestos totales: " . PuestoSeguridad::count() . "\n";
echo "Puestos con token: " . PuestoSeguridad::whereNotNull('qr_token')->count() . "\n";
echo "Tokens con formato SHA256 (64 chars): " . 
     PuestoSeguridad::whereRaw('LENGTH(qr_token) = 64')->count() . "\n";

// Mostrar ejemplo
$ejemplo = PuestoSeguridad::first();
echo "\n📋 EJEMPLO PARA PROBAR:\n";
echo "Código: {$ejemplo->codigo}\n";
echo "Token completo: {$ejemplo->qr_token}\n";
echo "Expira: {$ejemplo->qr_expira->format('d/m/Y H:i')}\n";