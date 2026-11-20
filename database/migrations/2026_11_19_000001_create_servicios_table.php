<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('tipo');
            $table->boolean('agendable');
            $table->boolean('ocupa_dia_completo');
            $table->unsignedInteger('dias_anticipacion_minima')->default(0);
            $table->time('hora_ingreso')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('servicio_id')->nullable()->constrained('servicios')->nullOnDelete();
            $table->text('observaciones')->nullable();
            $table->index(['fecha', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropIndex(['fecha', 'estado']);
            $table->dropForeign(['servicio_id']);
            $table->dropColumn(['servicio_id', 'observaciones']);
        });

        Schema::dropIfExists('servicios');
    }
};
