<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('producto', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('nombre');
            $table->string('categoria')->nullable();
            $table->integer('cantidad')->default(0);
            $table->decimal('precio', 10, 2)->default(0);
            $table->text('descripcion')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};
