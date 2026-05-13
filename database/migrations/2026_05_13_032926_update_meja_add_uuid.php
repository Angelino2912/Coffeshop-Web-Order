<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meja', function (Blueprint $table) {

            // ganti qr_code jadi uuid (lebih aman)
            $table->uuid('qr_uuid')->unique()->after('no_meja');

        });
    }

    public function down(): void
    {
        Schema::table('meja', function (Blueprint $table) {
            $table->dropColumn('qr_uuid');
        });
    }
};
