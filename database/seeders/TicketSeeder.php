<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketSeeder extends Seeder
{
    public function run(): void
    {
        // Order 1 - 2 VIP tickets (Active)
        for ($i = 1; $i <= 2; $i++) {
            $ticket = Ticket::create([
                'ticket_number' => 'TIX-20241215-VIP00' . $i,
                'order_item_id' => 1,
                'customer_id' => 1,
                'event_id' => 1,
                'ticket_type_id' => 3,
                'qr_code' => Str::uuid()->toString(),
                'qr_code_path' => null,
                'status' => 'active',
                'wristband_code' => null,
                'scanned_for_wristband_at' => null,
                'scanned_for_wristband_by' => null,
                'wristband_validated_at' => null,
                'wristband_validated_by' => null,
            ]);

            // Generate and save QR code
            $qrCode = QrCode::format('png')
                ->size(300)
                ->generate($ticket->qr_code);

            $path = 'qrcodes/' . $ticket->id . date('Ymdhis') . '.png';
            Storage::disk('public')->put($path, $qrCode);

            // Update qr_code_path
            $ticket->update(['qr_code_path' => $path]);
        }

        // Order 2 - 3 Regular tickets (1 scanned for wristband, 2 active)
        $ticket = Ticket::create([
            'ticket_number' => 'TIX-20241216-REG001',
            'order_item_id' => 2,
            'customer_id' => 2,
            'event_id' => 1,
            'ticket_type_id' => 2,
            'qr_code' => Str::uuid()->toString(),
            'qr_code_path' => null,
            'status' => 'scanned_for_wristband',
            'wristband_code' => 'WB-' . strtoupper(substr(uniqid(), -8)),
            'scanned_for_wristband_at' => now()->subHours(3),
            'scanned_for_wristband_by' => 2,
            'wristband_validated_at' => null,
            'wristband_validated_by' => null,
        ]);

        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($ticket->qr_code);
        $path = 'qrcodes/' . $ticket->id . date('Ymdhis') . '.png';
        Storage::disk('public')->put($path, $qrCode);
        $ticket->update(['qr_code_path' => $path]);

        for ($i = 2; $i <= 3; $i++) {
            $ticket = Ticket::create([
                'ticket_number' => 'TIX-20241216-REG00' . $i,
                'order_item_id' => 2,
                'customer_id' => 2,
                'event_id' => 1,
                'ticket_type_id' => 2,
                'qr_code' => Str::uuid()->toString(),
                'qr_code_path' => null,
                'status' => 'active',
                'wristband_code' => null,
                'scanned_for_wristband_at' => null,
                'scanned_for_wristband_by' => null,
                'wristband_validated_at' => null,
                'wristband_validated_by' => null,
            ]);

            $qrCode = QrCode::format('png')
                ->size(300)
                ->generate($ticket->qr_code);
            $path = 'qrcodes/' . $ticket->id . date('Ymdhis') . '.png';
            Storage::disk('public')->put($path, $qrCode);
            $ticket->update(['qr_code_path' => $path]);
        }

        // Order 3 - 1 Professional Pass (Used)
        $ticket = Ticket::create([
            'ticket_number' => 'TIX-20241216-PRO001',
            'order_item_id' => 3,
            'customer_id' => 3,
            'event_id' => 2,
            'ticket_type_id' => 5,
            'qr_code' => Str::uuid()->toString(),
            'qr_code_path' => null,
            'status' => 'used',
            'wristband_code' => 'WB-' . strtoupper(substr(uniqid(), -8)),
            'scanned_for_wristband_at' => now()->subHours(48),
            'scanned_for_wristband_by' => 2,
            'wristband_validated_at' => now()->subHours(47),
            'wristband_validated_by' => 3,
        ]);

        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($ticket->qr_code);
        $path = 'qrcodes/' . $ticket->id . date('Ymdhis') . '.png';
        Storage::disk('public')->put($path, $qrCode);
        $ticket->update(['qr_code_path' => $path]);

        // Order 4 - 1 Weekend Pass (Active)
        $ticket = Ticket::create([
            'ticket_number' => 'TIX-20241217-WKD001',
            'order_item_id' => 4,
            'customer_id' => 4,
            'event_id' => 3,
            'ticket_type_id' => 7,
            'qr_code' => Str::uuid()->toString(),
            'qr_code_path' => null,
            'status' => 'active',
            'wristband_code' => null,
            'scanned_for_wristband_at' => null,
            'scanned_for_wristband_by' => null,
            'wristband_validated_at' => null,
            'wristband_validated_by' => null,
        ]);

        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($ticket->qr_code);
        $path = 'qrcodes/' . $ticket->id . date('Ymdhis') . '.png';
        Storage::disk('public')->put($path, $qrCode);
        $ticket->update(['qr_code_path' => $path]);

        // Order 5 - 2 Standard Seats (Both active)
        for ($i = 1; $i <= 2; $i++) {
            $ticket = Ticket::create([
                'ticket_number' => 'TIX-20241217-STD00' . $i,
                'order_item_id' => 5,
                'customer_id' => 5,
                'event_id' => 4,
                'ticket_type_id' => 8,
                'qr_code' => Str::uuid()->toString(),
                'qr_code_path' => null,
                'status' => 'active',
                'wristband_code' => null,
                'scanned_for_wristband_at' => null,
                'scanned_for_wristband_by' => null,
                'wristband_validated_at' => null,
                'wristband_validated_by' => null,
            ]);

            $qrCode = QrCode::format('png')
                ->size(300)
                ->generate($ticket->qr_code);
            $path = 'qrcodes/' . $ticket->id . date('Ymdhis') . '.png';
            Storage::disk('public')->put($path, $qrCode);
            $ticket->update(['qr_code_path' => $path]);
        }

        // Order 10 - 2 Early Bird tickets from War (1 used, 1 active)
        $ticket = Ticket::create([
            'ticket_number' => 'TIX-20241217-WAR001',
            'order_item_id' => 10,
            'customer_id' => 10,
            'event_id' => 1,
            'ticket_type_id' => 1,
            'qr_code' => Str::uuid()->toString(),
            'qr_code_path' => null,
            'status' => 'used',
            'wristband_code' => 'WB-' . strtoupper(substr(uniqid(), -8)),
            'scanned_for_wristband_at' => now()->subHours(5),
            'scanned_for_wristband_by' => 2,
            'wristband_validated_at' => now()->subHours(4),
            'wristband_validated_by' => 3,
        ]);

        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($ticket->qr_code);
        $path = 'qrcodes/' . $ticket->id . date('Ymdhis') . '.png';
        Storage::disk('public')->put($path, $qrCode);
        $ticket->update(['qr_code_path' => $path]);

        $ticket = Ticket::create([
            'ticket_number' => 'TIX-20241217-WAR002',
            'order_item_id' => 10,
            'customer_id' => 10,
            'event_id' => 1,
            'ticket_type_id' => 1,
            'qr_code' => Str::uuid()->toString(),
            'qr_code_path' => null,
            'status' => 'active',
            'wristband_code' => null,
            'scanned_for_wristband_at' => null,
            'scanned_for_wristband_by' => null,
            'wristband_validated_at' => null,
            'wristband_validated_by' => null,
        ]);

        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($ticket->qr_code);
        $path = 'qrcodes/' . $ticket->id . date('Ymdhis') . '.png';
        Storage::disk('public')->put($path, $qrCode);
        $ticket->update(['qr_code_path' => $path]);
    }
}
