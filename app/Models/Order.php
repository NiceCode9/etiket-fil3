<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'event_id',
        'total_amount',
        'payment_status',
        'payment_method',
        'payment_channel',
        'snap_token',
        'invoice_path',
        'transaction_id',
        'paid_at',
        'expired_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    // Auto-generate order number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
            }

            // Set expired_at to 24 hours from now if not set
            if (empty($order->expired_at)) {
                $order->expired_at = now()->addHours(24);
            }
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Check if order is expired
    public function isExpired(): bool
    {
        return $this->payment_status === 'pending'
            && $this->expired_at
            && now()->gt($this->expired_at);
    }

    // Get status label with color
    public function getStatusBadge(): array
    {
        return match ($this->payment_status) {
            'pending' => ['label' => 'Pending', 'color' => 'warning'],
            'paid' => ['label' => 'Paid', 'color' => 'success'],
            'failed' => ['label' => 'Failed', 'color' => 'danger'],
            'expired' => ['label' => 'Expired', 'color' => 'secondary'],
            'refunded' => ['label' => 'Refunded', 'color' => 'info'],
            default => ['label' => 'Unknown', 'color' => 'secondary'],
        };
    }
}
