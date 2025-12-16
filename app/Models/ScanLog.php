<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanLog extends Model
{
    protected $fillable = [
        'ticket_id',
        'scan_type',
        'scanned_by',
        'scanned_at',
        'status',
        'notes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function scannedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scanned_by');
    }

    // Get scan type label
    public function getScanTypeLabel(): string
    {
        return match ($this->scan_type) {
            'qr_for_wristband' => 'QR for Wristband',
            'wristband_validation' => 'Wristband Validation',
            default => 'Unknown',
        };
    }
}
