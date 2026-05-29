<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            if (!Schema::hasColumn('admin', 'role')) {
                $table->string('role')->default('admin')->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('admin', function (Blueprint $table) {
            if (Schema::hasColumn('admin', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};