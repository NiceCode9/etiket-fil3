<?php

namespace Database\Seeders;

use App\Models\Scan;
use Illuminate\Database\Seeder;

class ScanSeeder extends Seeder
{
    public function run(): void
    {
        // Get tenant and tickets
        $tenant = \App\Models\Tenant::where('slug', 'sample-tenant')->first();
        $ticket3 = \App\Models\Ticket::find(3);
        $ticket4 = \App\Models\Ticket::find(4);
        $ticket10 = \App\Models\Ticket::find(10);

        // Scan for QR to wristband
        Scan::create([
            'user_id' => 2,
            'ticket_id' => 3,
            'tenant_id' => $ticket3?->tenant_id ?? $tenant?->id,
            'scan_type' => 'qr_scan',
            'scanned_at' => now()->subHours(3),
        ]);

        // Scan for wristband validation
        Scan::create([
            'user_id' => 3,
            'ticket_id' => 4,
            'tenant_id' => $ticket4?->tenant_id ?? $tenant?->id,
            'scan_type' => 'wristband_validation',
            'scanned_at' => now()->subHours(47),
        ]);

        // Scan for war ticket
        Scan::create([
            'user_id' => 2,
            'ticket_id' => 10,
            'tenant_id' => $ticket10?->tenant_id ?? $tenant?->id,
            'scan_type' => 'qr_scan',
            'scanned_at' => now()->subHours(5),
        ]);

        // Another wristband validation
        Scan::create([
            'user_id' => 3,
            'ticket_id' => 10,
            'tenant_id' => $ticket10?->tenant_id ?? $tenant?->id,
            'scan_type' => 'wristband_validation',
            'scanned_at' => now()->subHours(4),
        ]);
    }
}
