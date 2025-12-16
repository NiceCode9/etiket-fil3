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
        Schema::create('war_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_type_id')
                ->constrained('ticket_types')
                ->onDelete('cascade');
            $table->decimal('war_price', 15, 2);
            $table->integer('war_quota');
            $table->integer('war_available_quota');
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('ticket_type_id');
            $table->index(['start_time', 'end_time']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('war_tickets');
    }
};
