<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (
            DB::connection()->getDriverName() === 'mysql'
            && Schema::hasTable('menus')
            && Schema::hasColumn('menus', 'category')
        ) {
            DB::statement("ALTER TABLE menus MODIFY category ENUM('Makanan', 'Minuman', 'Snack') NOT NULL");
        }
    }

    public function down(): void
    {
        if (
            DB::connection()->getDriverName() === 'mysql'
            && Schema::hasTable('menus')
            && Schema::hasColumn('menus', 'category')
        ) {
            DB::table('menus')->where('category', 'Snack')->update(['category' => 'Makanan']);
            DB::statement("ALTER TABLE menus MODIFY category ENUM('Makanan', 'Minuman') NOT NULL");
        }
    }
};
