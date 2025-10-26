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
        Schema::create('items__menus', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->decimal('precio', 8, 2);
            $table->string('estado')->default('disponible');
            $table->unsignedBigInteger('categoria_id');
            $table->unsignedBigInteger('restaurante_id');
            $table->timestamps();

            $table->foreign('categoria_id')->references('id')->on('items__categorias')->onDelete('cascade');
            $table->foreign('restaurante_id')->references('id')->on('restaurantes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items__menus');
    }
};
