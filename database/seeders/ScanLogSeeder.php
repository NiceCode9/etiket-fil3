<?php

namespace Database\Seeders;

use App\Models\ScanLog;
use Illuminate\Database\Seeder;

class ScanLogSeeder extends Seeder
{
    public function run(): void
    {
        // Scan logs for ticket 3 (scanned for wristband)
        ScanLog::create([
            'ticket_id' => 3,
            'scan_type' => 'qr_for_wristband',
            'scanned_by' => 2,
            'scanned_at' => now()->subHours(3),
            'status' => 'success',
            'notes' => 'Wristband issued successfully',
        ]);

        // Scan logs for ticket 4 (used ticket)
        ScanLog::create([
            'ticket_id' => 4,
            'scan_type' => 'qr_for_wristband',
            'scanned_by' => 2,
            'scanned_at' => now()->subHours(48),
            'status' => 'success',
            'notes' => 'Wristband issued',
        ]);

        ScanLog::create([
            'ticket_id' => 4,
            'scan_type' => 'wristband_validation',
            'scanned_by' => 3,
            'scanned_at' => now()->subHours(47),
            'status' => 'success',
            'notes' => 'Wristband validated, entry granted',
        ]);

        // Scan logs for ticket 10 (used war ticket)
        ScanLog::create([
            'ticket_id' => 10,
            'scan_type' => 'qr_for_wristband',
            'scanned_by' => 2,
            'scanned_at' => now()->subHours(5),
            'status' => 'success',
            'notes' => 'War ticket - Wristband issued',
        ]);

        ScanLog::create([
            'ticket_id' => 10,
            'scan_type' => 'wristband_validation',
            'scanned_by' => 3,
            'scanned_at' => now()->subHours(4),
            'status' => 'success',
            'notes' => 'Wristband validated, entry granted',
        ]);

        // Failed scan attempt
        ScanLog::create([
            'ticket_id' => 1,
            'scan_type' => 'qr_for_wristband',
            'scanned_by' => 2,
            'scanned_at' => now()->subHours(1),
            'status' => 'failed',
            'notes' => 'QR code could not be read, please try again',
        ]);

        // Multiple validation attempts
        ScanLog::create([
            'ticket_id' => 3,
            'scan_type' => 'wristband_validation',
            'scanned_by' => 3,
            'scanned_at' => now()->subMinutes(30),
            'status' => 'failed',
            'notes' => 'Wristband not validated yet',
        ]);
    }
}
