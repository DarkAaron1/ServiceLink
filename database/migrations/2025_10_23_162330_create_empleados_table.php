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
        Schema::create('empleados', function (Blueprint $table) {
            $table->string('rut')->primary();
            $table->string('nombre');
            $table->string('apellido');
            $table->date('fecha_nacimiento');
            $table->integer('fono');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('cargo'); //hace referencia al nombre del rol en la tabla roles
            $table->string('estado')->default('inactivo');
            $table->unsignedBigInteger('restaurante_id');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('cargo')->references('nombre')->on('roles')->onDelete('cascade');
            $table->foreign('restaurante_id')->references('id')->on('restaurantes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};
