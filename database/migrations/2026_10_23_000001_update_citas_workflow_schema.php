<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->enum('estado', [
                'pendiente',
                'confirmada',
                'cancelada',
                'completada',
                'solicitada',
                'aceptada',
                'atendida',
            ])->change();
        });

        DB::table('citas')->where('estado', 'pendiente')->update(['estado' => 'solicitada']);
        DB::table('citas')->where('estado', 'confirmada')->update(['estado' => 'aceptada']);
        DB::table('citas')->where('estado', 'completada')->update(['estado' => 'atendida']);

        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('mecanico_id')->nullable()->change();
            $table->string('servicio')->default('');
            $table->text('motivo_cancelacion')->nullable();
            $table->enum('estado', ['solicitada', 'aceptada', 'cancelada', 'atendida'])->change();
        });
    }

    public function down(): void
    {
        if (DB::table('citas')->whereNull('mecanico_id')->exists()) {
            throw new RuntimeException('No se puede revertir: hay citas sin mecánico asignado.');
        }

        Schema::table('citas', function (Blueprint $table) {
            $table->enum('estado', [
                'solicitada',
                'aceptada',
                'cancelada',
                'atendida',
                'pendiente',
                'confirmada',
                'completada',
            ])->change();
        });

        DB::table('citas')->where('estado', 'solicitada')->update(['estado' => 'pendiente']);
        DB::table('citas')->where('estado', 'aceptada')->update(['estado' => 'confirmada']);
        DB::table('citas')->where('estado', 'atendida')->update(['estado' => 'completada']);

        Schema::table('citas', function (Blueprint $table) {
            $table->foreignId('mecanico_id')->nullable(false)->change();
            $table->dropColumn(['servicio', 'motivo_cancelacion']);
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'completada'])->change();
        });
    }
};
