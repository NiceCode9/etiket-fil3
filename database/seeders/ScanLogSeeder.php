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
            'ip_address' => '192.168.1.100',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);

        // Scan logs for ticket 4 (used ticket)
        ScanLog::create([
            'ticket_id' => 4,
            'scan_type' => 'qr_for_wristband',
            'scanned_by' => 2,
            'scanned_at' => now()->subHours(48),
            'status' => 'success',
            'notes' => 'Wristband issued',
            'ip_address' => '192.168.1.101',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);

        ScanLog::create([
            'ticket_id' => 4,
            'scan_type' => 'wristband_validation',
            'scanned_by' => 3,
            'scanned_at' => now()->subHours(47),
            'status' => 'success',
            'notes' => 'Wristband validated, entry granted',
            'ip_address' => '192.168.1.102',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15',
        ]);

        // Scan logs for ticket 10 (used war ticket)
        ScanLog::create([
            'ticket_id' => 10,
            'scan_type' => 'qr_for_wristband',
            'scanned_by' => 2,
            'scanned_at' => now()->subHours(5),
            'status' => 'success',
            'notes' => 'War ticket - Wristband issued',
            'ip_address' => '192.168.1.103',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);

        ScanLog::create([
            'ticket_id' => 10,
            'scan_type' => 'wristband_validation',
            'scanned_by' => 3,
            'scanned_at' => now()->subHours(4),
            'status' => 'success',
            'notes' => 'Wristband validated, entry granted',
            'ip_address' => '192.168.1.104',
            'user_agent' => 'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0 Firefox/88.0',
        ]);

        // Failed scan attempt
        ScanLog::create([
            'ticket_id' => 1,
            'scan_type' => 'qr_for_wristband',
            'scanned_by' => 2,
            'scanned_at' => now()->subHours(1),
            'status' => 'failed',
            'notes' => 'QR code could not be read, please try again',
            'ip_address' => '192.168.1.105',
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
        ]);

        // Multiple validation attempts
        ScanLog::create([
            'ticket_id' => 3,
            'scan_type' => 'wristband_validation',
            'scanned_by' => 3,
            'scanned_at' => now()->subMinutes(30),
            'status' => 'failed',
            'notes' => 'Wristband not validated yet',
            'ip_address' => '192.168.1.106',
            'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15',
        ]);
    }
}
