<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\HasTenant;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use SoftDeletes, HasTenant;

    protected $fillable = [
        'ticket_number',
        'order_item_id',
        'customer_id',
        'event_id',
        'ticket_type_id',
        'tenant_id',
        'qr_code',
        'qr_code_path',
        'status',
        'wristband_code',
        'scanned_for_wristband_at',
        'scanned_for_wristband_by',
        'wristband_validated_at',
        'wristband_validated_by',
    ];

    protected $casts = [
        'scanned_for_wristband_at' => 'datetime',
        'wristband_validated_at' => 'datetime',
    ];

    // Auto-generate ticket number and QR code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TIX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -8));
            }

            if (empty($ticket->qr_code)) {
                $ticket->qr_code = Str::uuid()->toString();
            }

            // Auto-set tenant_id from event if not set
            if (empty($ticket->tenant_id) && $ticket->event_id) {
                $event = Event::find($ticket->event_id);
                if ($event && $event->tenant_id) {
                    $ticket->tenant_id = $event->tenant_id;
                }
            }
        });
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function ticketType(): BelongsTo
    {
        return $this->belongsTo(TicketType::class);
    }

    public function scannedForWristbandBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_for_wristband_by');
    }

    public function wristbandValidatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wristband_validated_by');
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class);
    }

    // Check if ticket can be scanned for wristband
    public function canScanForWristband(): bool
    {
        return $this->status === 'active';
    }

    // Check if wristband can be validated
    public function canValidateWristband(): bool
    {
        return $this->status === 'scanned_for_wristband'
            && !empty($this->wristband_code);
    }

    // Get status badge
    public function getStatusBadge(): array
    {
        return match ($this->status) {
            'active' => ['label' => 'Active', 'color' => 'success'],
            'scanned_for_wristband' => ['label' => 'Wristband Issued', 'color' => 'info'],
            'used' => ['label' => 'Used', 'color' => 'secondary'],
            'cancelled' => ['label' => 'Cancelled', 'color' => 'danger'],
            default => ['label' => 'Unknown', 'color' => 'secondary'],
        };
    }
}
