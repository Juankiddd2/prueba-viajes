<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ciudad_id');
            $table->decimal('presupuesto_cop', 15, 2);
            $table->decimal('presupuesto_convertido', 15, 2);
            $table->decimal('tasa_cambio', 15, 6);
            $table->decimal('clima', 5, 2);
            $table->timestamp('fecha_consulta')->useCurrent();
            $table->foreign('ciudad_id')->references('id')->on('ciudades');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultas');
    }
};
