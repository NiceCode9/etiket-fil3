<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Ticket;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketEmail;

class TicketService
{
    public function generateTicketsForOrder(Order $order)
    {
        $tickets = [];

        foreach ($order->orderItems as $orderItem) {
            for ($i = 0; $i < $orderItem->quantity; $i++) {
                $ticket = Ticket::create([
                    'order_item_id' => $orderItem->id,
                    'customer_id' => $order->customer_id,
                    'event_id' => $order->event_id,
                    'ticket_type_id' => $orderItem->ticket_type_id,
                    'status' => 'active',
                ]);

                // Generate QR Code image
                $this->generateQRCode($ticket);

                $tickets[] = $ticket;
            }
        }

        // Send email with tickets
        $this->sendTicketEmail($order, $tickets);

        return $tickets;
    }

    public function generateQRCode(Ticket $ticket)
    {
        $qrCode = QrCode::format('png')
            ->size(300)
            ->generate($ticket->qr_code);

        $path = "qrcodes/{$ticket->id}.png";
        Storage::disk('public')->put($path, $qrCode);

        $ticket->update(['qr_code_path' => $path]);

        return $ticket;
    }

    public function sendTicketEmail(Order $order, array $tickets)
    {
        Mail::to($order->customer->email)->send(new TicketEmail($order, $tickets));
    }

    public function scanQRForWristband(string $qrCode, int $userId)
    {
        $ticket = Ticket::where('qr_code', $qrCode)->firstOrFail();

        if (!$ticket->canScanForWristband()) {
            throw new \Exception('Ticket already scanned or not valid');
        }

        // Generate wristband code
        $wristbandCode = 'WB-' . strtoupper(substr(uniqid(), -10));

        $ticket->update([
            'status' => 'scanned_for_wristband',
            'wristband_code' => $wristbandCode,
            'scanned_for_wristband_at' => now(),
            'scanned_for_wristband_by' => $userId,
        ]);

        // Log scan
        $ticket->scanLogs()->create([
            'scan_type' => 'qr_for_wristband',
            'scanned_by' => $userId,
            'scanned_at' => now(),
            'status' => 'success',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $ticket;
    }

    public function validateWristband(string $wristbandCode, int $userId)
    {
        $ticket = Ticket::where('wristband_code', $wristbandCode)->firstOrFail();

        if (!$ticket->canValidateWristband()) {
            throw new \Exception('Wristband already validated or not valid');
        }

        $ticket->update([
            'status' => 'used',
            'wristband_validated_at' => now(),
            'wristband_validated_by' => $userId,
        ]);

        // Log validation
        $ticket->scanLogs()->create([
            'scan_type' => 'wristband_validation',
            'scanned_by' => $userId,
            'scanned_at' => now(),
            'status' => 'success',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return $ticket;
    }
}
