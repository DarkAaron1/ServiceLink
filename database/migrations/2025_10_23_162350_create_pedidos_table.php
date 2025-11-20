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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('comanda_id');
            $table->string('observaciones')->nullable();
            $table->dateTime('hora_creacion')->useCurrent();
            $table->string('estado')->default('pendiente');
            $table->decimal('valor_item_ATM', 8, 3); //Valor del item al momento de la orden
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items__menus')->onDelete('cascade');
            $table->foreign('comanda_id')->references('id')->on('comandas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
