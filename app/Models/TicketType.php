<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'event_id',
        'name',
        'description',
        'price',
        'quota',
        'available_quota',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function warTickets(): HasMany
    {
        return $this->hasMany(WarTicket::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // Check if war ticket active now
    public function getActiveWarTicket()
    {
        return $this->warTickets()
            ->where('is_active', true)
            ->where('start_time', '<=', now())
            ->where('end_time', '>=', now())
            ->where('war_available_quota', '>', 0)
            ->first();
    }

    // Get current price (war price if active, normal price otherwise)
    public function getCurrentPrice()
    {
        $warTicket = $this->getActiveWarTicket();
        return $warTicket ? $warTicket->war_price : $this->price;
    }

    // Check availability
    public function isAvailable($quantity = 1)
    {
        $warTicket = $this->getActiveWarTicket();

        if ($warTicket) {
            return $warTicket->war_available_quota >= $quantity;
        }

        return $this->available_quota >= $quantity;
    }
}
