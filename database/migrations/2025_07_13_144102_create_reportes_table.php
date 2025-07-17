
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
           $table->bigIncrements('id');

            $table->string('subidopor', 255);
            $table->date('fecha')->default(DB::raw('CURRENT_DATE'));
            $table->time('hora')->default(DB::raw('CURRENT_TIME'));
            $table->text('descripcion')->nullable();
            $table->json('imagenes')->nullable(); 
            $table->foreignId('categoria_reporte_id')->nullable()->constrained('categorias_reporte')->onDelete('set null');
            $table->foreignId('tipo_reporte_id')->nullable()->constrained('tipo_reportes')->onDelete('set null');
            

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
