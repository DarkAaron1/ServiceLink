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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->string('rut')->primary();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email')->unique();
            $table->string('password');
            $table->date('fecha_nacimiento');
            $table->dateTime('email_verified_at')->nullable();
            $table->string('estado')->default('inactivo');
            $table->unsignedBigInteger('rol_id');
            $table->unsignedBigInteger('restaurante_id');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
            $table->foreign('restaurante_id')->references('id')->on('restaurantes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
