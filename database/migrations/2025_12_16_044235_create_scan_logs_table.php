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
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->onDelete('cascade');
            $table->enum('scan_type', ['qr_for_wristband', 'wristband_validation']);
            $table->foreignId('scanned_by')
                ->constrained('users')
                ->onDelete('cascade');
            $table->dateTime('scanned_at');
            $table->enum('status', ['success', 'failed', 'already_used']);
            $table->text('notes')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('ticket_id');
            $table->index('scanned_by');
            $table->index('scan_type');
            $table->index('scanned_at');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
    }
};
