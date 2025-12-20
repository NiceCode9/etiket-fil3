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
        Schema::table('orders', function (Blueprint $table) {
            // Snap token untuk Midtrans
            if (!Schema::hasColumn('orders', 'snap_token')) {
                $table->string('snap_token')->nullable()->after('order_number');
            }

            // Waktu expired order (24 jam dari dibuat)
            if (!Schema::hasColumn('orders', 'expired_at')) {
                $table->timestamp('expired_at')->nullable()->after('paid_at');
            }

            // Waktu order dibatalkan
            if (!Schema::hasColumn('orders', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('expired_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'expired_at', 'cancelled_at']);
        });
    }
};
