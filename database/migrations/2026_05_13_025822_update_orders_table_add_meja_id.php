<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {

            $table->foreignId('meja_id')
                ->nullable()
                ->constrained('meja')
                ->nullOnDelete();
                
            // optional: tracking session
            $table->uuid('session_uuid')->nullable();

        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('meja_id');
            $table->dropColumn(['customer_name', 'session_uuid']);
        });
    }
};
