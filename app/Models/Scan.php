<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\HasTenant;

class Scan extends Model
{
    use HasTenant;

    protected $fillable = [
        'user_id',
        'ticket_id',
        'tenant_id',
        'scan_type',
        'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    // Get scan type label
    public function getScanTypeLabel(): string
    {
        return match ($this->scan_type) {
            'qr_scan' => 'QR Scan',
            'wristband_validation' => 'Wristband Validation',
            default => 'Unknown',
        };
    }
}
