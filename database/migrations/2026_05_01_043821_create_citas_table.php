<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('moto_id')->constrained('motos')->onDelete('cascade');
            $table->foreignId('mecanico_id')->constrained('users')->onDelete('cascade');
            $table->date('fecha');
            $table->time('hora');
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'completada']);
            $table->timestamps();
            
            // Índices
            $table->index(['cliente_id', 'fecha']);
            $table->index(['mecanico_id', 'fecha']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
