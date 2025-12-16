<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WarTicket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_type_id',
        'war_price',
        'war_quota',
        'war_available_quota',
        'start_time',
        'end_time',
        'is_active',
    ];

    protected $casts = [
        'war_price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Check if war is currently active
    public function isCurrentlyActive(): bool
    {
        return $this->is_active
            && now()->between($this->start_time, $this->end_time)
            && $this->war_available_quota > 0;
    }

    // Get status label
    public function getStatusLabel(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        if (now()->lt($this->start_time)) {
            return 'Upcoming';
        }

        if (now()->gt($this->end_time)) {
            return 'Ended';
        }

        if ($this->war_available_quota <= 0) {
            return 'Sold Out';
        }

        return 'Active';
    }
}
