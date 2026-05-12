<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_details', function (Blueprint $table) {

            // HAPUS FOREIGN KEY LAMA JIKA ADA
            $table->dropForeign(['id_orders']);
            $table->dropForeign(['id_menu']);

        });

        // UBAH TIPE DATA
        DB::statement('ALTER TABLE order_details MODIFY id_orders BIGINT UNSIGNED');
        DB::statement('ALTER TABLE order_details MODIFY id_menu BIGINT UNSIGNED');

        Schema::table('order_details', function (Blueprint $table) {

            // TAMBAH FOREIGN KEY BARU
            $table->foreign('id_orders')
                ->references('id')
                ->on('orders')
                ->onDelete('cascade');

            $table->foreign('id_menu')
                ->references('id')
                ->on('menus')
                ->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::table('order_details', function (Blueprint $table) {

            $table->dropForeign(['id_orders']);
            $table->dropForeign(['id_menu']);

        });
    }
};