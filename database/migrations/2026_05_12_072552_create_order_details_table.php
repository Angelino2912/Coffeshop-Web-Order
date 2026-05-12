<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {

            $table->id('id_order_details');

            $table->foreignId('id_orders')
                ->constrained('orders')
                ->onDelete('cascade');

            $table->foreignId('id_menu')
                ->constrained('menus')
                ->onDelete('cascade');

            $table->integer('quantity');

            $table->decimal('harga', 15, 2);

            $table->decimal('subtotal', 15, 2);

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};