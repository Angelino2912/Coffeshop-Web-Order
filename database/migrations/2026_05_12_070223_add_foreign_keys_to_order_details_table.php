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
        Schema::table('order_details', function (Blueprint $table) {

            // FOREIGN KEY KE ORDERS
            $table->foreign('id_orders')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');

            // FOREIGN KEY KE MENUS
            $table->foreign('id_menu')
                ->references('id')
                ->on('menus')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {

            $table->dropForeign(['id_orders']);

            $table->dropForeign(['id_menu']);
        });
    }
};
