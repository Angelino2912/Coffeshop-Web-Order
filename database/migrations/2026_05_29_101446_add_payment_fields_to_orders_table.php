<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('status');
            $table->decimal('cash_received', 12, 2)->nullable()->after('payment_method');
            $table->decimal('change_amount', 12, 2)->nullable()->after('cash_received');
            $table->timestamp('paid_at')->nullable()->after('change_amount');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'cash_received', 'change_amount', 'paid_at']);
        });
    }
};