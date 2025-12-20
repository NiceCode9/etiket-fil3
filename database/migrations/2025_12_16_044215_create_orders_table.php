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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->onDelete('cascade');
            $table->foreignId('event_id')
                ->constrained('events')
                ->onDelete('cascade');
            $table->decimal('total_amount', 15, 2);
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'expired', 'cancelled', 'refunded'])
                ->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_channel')->nullable();
            $table->string('snap_token')->nullable();
            $table->string('transaction_id')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('order_number');
            $table->index('customer_id');
            $table->index('event_id');
            $table->index('payment_status');
            $table->index('transaction_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
