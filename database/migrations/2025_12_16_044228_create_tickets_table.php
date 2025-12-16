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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('order_item_id')
                ->constrained('order_items')
                ->onDelete('cascade');
            $table->foreignId('customer_id')
                ->constrained('customers')
                ->onDelete('cascade');
            $table->foreignId('event_id')
                ->constrained('events')
                ->onDelete('cascade');
            $table->foreignId('ticket_type_id')
                ->constrained('ticket_types')
                ->onDelete('cascade');
            $table->string('qr_code')->unique();
            $table->string('qr_code_path')->nullable();
            $table->enum('status', ['active', 'scanned_for_wristband', 'used', 'cancelled'])
                ->default('active');
            $table->string('wristband_code')->unique()->nullable();
            $table->dateTime('scanned_for_wristband_at')->nullable();
            $table->foreignId('scanned_for_wristband_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->dateTime('wristband_validated_at')->nullable();
            $table->foreignId('wristband_validated_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('ticket_number');
            $table->index('qr_code');
            $table->index('wristband_code');
            $table->index('customer_id');
            $table->index('event_id');
            $table->index('status');
            $table->index('order_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
