<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meja_sessions', function (Blueprint $table) {
            $table->id();

            // relasi ke meja kamu
            $table->foreignId('meja_id')
                ->constrained('meja')
                ->cascadeOnDelete();

            // UUID untuk lock session (INI INTI SISTEM)
            $table->uuid('session_uuid')->unique();

            // nama customer setelah scan QR
            $table->string('customer_name');

            // status meja
            $table->enum('status', ['active', 'finished', 'cancelled'])
                ->default('active');

            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();

            $table->timestamps();

            $table->index(['meja_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_sessions');
    }
};