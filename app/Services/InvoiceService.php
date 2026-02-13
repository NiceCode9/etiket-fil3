<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Codedge\Fpdf\Fpdf\Fpdf;

class InvoiceService
{
    /**
     * Generate invoice PDF for order
     */
    public function generateInvoice(Order $order): string
    {
        // Ensure tickets are generated
        $this->ensureTicketsGenerated($order);

        // Create PDF instance
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 15);

        // Add content
        $this->addHeader($pdf, $order);
        $this->addCustomerInfo($pdf, $order);
        $this->addEventInfo($pdf, $order);
        $this->addOrderItems($pdf, $order);
        $this->addTicketsWithQRCodes($pdf, $order);
        $this->addFooter($pdf, $order);

        // Save PDF
        $filename = "invoice_{$order->order_number}.pdf";
        $path = "invoices/{$filename}";

        $pdfOutput = $pdf->Output('S'); // Output as string
        Storage::disk('public')->put($path, $pdfOutput);

        // Update order with invoice path
        $order->update(['invoice_path' => $path]);

        Log::info('Invoice generated', [
            'order_number' => $order->order_number,
            'path' => $path,
            'tickets_count' => $order->orderItems->sum(fn($item) => $item->tickets->count()),
        ]);

        return $path;
    }

    /**
     * Ensure all tickets are generated for the order
     */
    private function ensureTicketsGenerated(Order $order)
    {
        foreach ($order->orderItems as $orderItem) {
            $existingTicketsCount = $orderItem->tickets()->count();
            $neededTickets = $orderItem->quantity - $existingTicketsCount;

            if ($neededTickets > 0) {
                for ($i = 0; $i < $neededTickets; $i++) {
                    $orderItem->tickets()->create([
                        'customer_id' => $order->customer_id,
                        'event_id' => $order->event_id,
                        'ticket_type_id' => $orderItem->ticket_type_id,
                        'tenant_id' => $order->tenant_id,
                        'status' => 'active',
                    ]);
                }
            }
        }

        // Refresh the relationship
        $order->load('orderItems.tickets.customer', 'orderItems.ticketType');
    }

    /**
     * Add header to PDF
     */
    private function addHeader($pdf, Order $order)
    {
        // Logo (optional - add your logo path)
        // Check if logo exists
        if (file_exists(public_path('logo.png'))) {
            $pdf->Image(public_path('logo.png'), 90, 6, 30);
            $pdf->Ln(12);
        } else {
            $pdf->Ln(6);
        }

        // Company name
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(0, 10, 'INVOICE', 0, 1, 'C');

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 5, 'Untix By Unovia Creative', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Phone: +62 821-4081-7545 | Email: unoviacreative@gmail.com', 0, 1, 'C');

        $pdf->Ln(5);

        // Line separator
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());
        $pdf->Ln(8);

        // Invoice details
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(95, 6, 'Invoice Number:', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $order->order_number, 0, 1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(95, 6, 'Invoice Date:', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $order->created_at->format('d M Y, H:i'), 0, 1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(95, 6, 'Payment Date:', 0, 0);
        $pdf->SetFont('Arial', '', 11);
        $pdf->Cell(0, 6, $order->paid_at ? $order->paid_at->format('d M Y, H:i') : '-', 0, 1);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(95, 6, 'Status:', 0, 0);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(0, 6, 'PAID', 0, 1);
        $pdf->SetTextColor(0, 0, 0);

        $pdf->Ln(5);
    }

    /**
     * Add customer information
     */
    private function addCustomerInfo($pdf, Order $order)
    {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, 'CUSTOMER INFORMATION', 0, 1, 'L', true);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 6, 'Name', 0, 0);
        $pdf->Cell(0, 6, ': ' . $order->customer->full_name, 0, 1);

        $pdf->Cell(50, 6, 'Email', 0, 0);
        $pdf->Cell(0, 6, ': ' . $order->customer->email, 0, 1);

        $pdf->Cell(50, 6, 'Phone', 0, 0);
        $pdf->Cell(0, 6, ': ' . $order->customer->phone_number, 0, 1);

        $pdf->Cell(50, 6, 'Identity Type', 0, 0);
        $pdf->Cell(0, 6, ': ' . strtoupper($order->customer->identity_type), 0, 1);

        $pdf->Cell(50, 6, 'Identity Number', 0, 0);
        $pdf->Cell(0, 6, ': ' . $order->customer->identity_number, 0, 1);

        $pdf->Ln(5);
    }

    /**
     * Add event information
     */
    private function addEventInfo($pdf, Order $order)
    {
        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, 'EVENT INFORMATION', 0, 1, 'L', true);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 6, 'Event Name', 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, ': ' . $order->event->name, 0, 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(50, 6, 'Date', 0, 0);
        $pdf->Cell(0, 6, ': ' . \Carbon\Carbon::parse($order->event->event_date)->format('d M Y, H:i') . ' WIB', 0, 1);

        $pdf->Cell(50, 6, 'Location', 0, 0);
        $pdf->MultiCell(0, 6, ': ' . ($order->event->venue ?? '-'));

        $pdf->Ln(5);
    }

    /**
     * Add order items table
     */
    private function addOrderItems($pdf, Order $order)
    {
        $pdf->SetFillColor(52, 58, 64);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 10);

        // Table header
        $pdf->Cell(10, 8, 'No', 1, 0, 'C', true);
        $pdf->Cell(85, 8, 'Ticket Type', 1, 0, 'L', true);
        $pdf->Cell(25, 8, 'Quantity', 1, 0, 'C', true);
        $pdf->Cell(35, 8, 'Price', 1, 0, 'R', true);
        $pdf->Cell(35, 8, 'Subtotal', 1, 1, 'R', true);

        // Reset text color
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', 10);

        // Table rows
        $no = 1;
        foreach ($order->orderItems as $item) {
            $pdf->Cell(10, 7, $no++, 1, 0, 'C');
            $pdf->Cell(85, 7, $item->ticketType->name, 1, 0, 'L');
            $pdf->Cell(25, 7, $item->quantity, 1, 0, 'C');
            $pdf->Cell(35, 7, 'Rp ' . number_format($item->price, 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell(35, 7, 'Rp ' . number_format($item->subtotal, 0, ',', '.'), 1, 1, 'R');
        }

        // Total
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(120, 8, '', 0, 0);
        $pdf->Cell(35, 8, 'TOTAL', 1, 0, 'L');
        $pdf->SetTextColor(0, 128, 0);
        $pdf->Cell(35, 8, 'Rp ' . number_format($order->total_amount, 0, ',', '.'), 1, 1, 'R');
        $pdf->SetTextColor(0, 0, 0);

        $pdf->Ln(8);
    }

    /**
     * Add tickets with individual QR codes
     */
    private function addTicketsWithQRCodes($pdf, Order $order)
    {
        // Get all tickets from order items
        $allTickets = collect();
        foreach ($order->orderItems as $orderItem) {
            $tickets = $orderItem->tickets;
            foreach ($tickets as $ticket) {
                $allTickets->push([
                    'ticket' => $ticket,
                    'ticket_type' => $orderItem->ticketType->name,
                ]);
            }
        }

        if ($allTickets->isEmpty()) {
            // Add warning message
            $pdf->SetFillColor(255, 200, 200);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 8, 'WARNING: No tickets found for this order!', 1, 1, 'C', true);
            return;
        }

        // Add section header
        $pdf->SetFillColor(52, 58, 64);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(0, 8, 'YOUR TICKETS - EXCHANGE FOR WRISTBAND', 0, 1, 'C', true);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Ln(3);

        // Display each ticket with QR code
        $ticketNumber = 1;
        foreach ($allTickets as $ticketData) {
            $ticket = $ticketData['ticket'];
            $ticketType = $ticketData['ticket_type'];

            // Check if we need a new page
            if ($pdf->GetY() > 240) {
                $pdf->AddPage();
                $pdf->Ln(10);
            }

            // Generate QR code for this ticket
            $qrCodePath = $this->generateTicketQRCode($ticket);

            // Ticket container
            $startY = $pdf->GetY();

            // Draw border
            $pdf->SetDrawColor(200, 200, 200);
            $pdf->SetFillColor(255, 250, 240);
            $pdf->Rect(10, $startY, 190, 55, 'FD');

            // Ticket number badge
            $pdf->SetXY(15, $startY + 5);
            $pdf->SetFillColor(52, 58, 64);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(20, 6, "TICKET #$ticketNumber", 0, 0, 'C', true);

            // Add QR code image
            if ($qrCodePath && Storage::disk('public')->exists($qrCodePath)) {
                $fullPath = Storage::disk('public')->path($qrCodePath);
                if (file_exists($fullPath)) {
                    $pdf->Image($fullPath, 15, $startY + 13, 35, 35);
                } else {
                    // QR code file doesn't exist
                    $pdf->SetXY(15, $startY + 13);
                    $pdf->SetFont('Arial', '', 8);
                    $pdf->SetTextColor(255, 0, 0);
                    $pdf->MultiCell(35, 4, "QR Code\nNot Found", 1, 'C');
                }
            } else {
                // Failed to generate QR code
                $pdf->SetXY(15, $startY + 13);
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetTextColor(255, 0, 0);
                $pdf->MultiCell(35, 4, "QR Code\nGeneration\nFailed", 1, 'C');
            }
            $pdf->SetTextColor(0, 0, 0);

            // Ticket details
            $pdf->SetXY(55, $startY + 5);
            $pdf->SetFont('Arial', 'B', 11);
            $pdf->Cell(0, 6, $ticketType, 0, 1);

            $pdf->SetX(55);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(40, 5, 'Ticket Number:', 0, 0);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(0, 5, $ticket->ticket_number, 0, 1);

            $pdf->SetX(55);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(40, 5, 'Customer:', 0, 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(0, 5, $ticket->customer->full_name, 0, 1);

            $pdf->SetX(55);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(40, 5, 'Identity Number:', 0, 0);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(0, 5, $ticket->customer->identity_number, 0, 1);

            $pdf->SetX(55);
            $pdf->SetFont('Arial', '', 9);
            $pdf->Cell(40, 5, 'Status:', 0, 0);

            // Status badge
            $statusBadge = $ticket->getStatusBadge();
            $statusColors = [
                'success' => [0, 128, 0],
                'info' => [0, 112, 192],
                'danger' => [192, 0, 0],
                'secondary' => [108, 117, 125],
            ];
            $color = $statusColors[$statusBadge['color']] ?? [0, 0, 0];
            $pdf->SetTextColor($color[0], $color[1], $color[2]);
            $pdf->SetFont('Arial', 'B', 9);
            $pdf->Cell(0, 5, strtoupper($statusBadge['label']), 0, 1);
            $pdf->SetTextColor(0, 0, 0);

            // Instructions
            $pdf->SetXY(55, $startY + 35);
            $pdf->SetFont('Arial', 'I', 8);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->MultiCell(
                140,
                4,
                "Scan this QR code at the event entrance to exchange for wristband. " .
                    "Bring valid ID matching identity number above."
            );
            $pdf->SetTextColor(0, 0, 0);

            // Move to next ticket position
            $pdf->SetY($startY + 58);

            $ticketNumber++;
        }

        $pdf->Ln(5);
    }

    /**
     * Generate QR code for individual ticket
     */
    private function generateTicketQRCode($ticket): ?string
    {
        try {
            $qrData = json_encode([
                'ticket_number' => $ticket->ticket_number,
                'ticket_id' => $ticket->id,
                'qr_code' => $ticket->qr_code,
                'customer_id' => $ticket->customer_id,
                'identity_number' => $ticket->customer->identity_number,
                'event_id' => $ticket->event_id,
                'ticket_type_id' => $ticket->ticket_type_id,
            ]);

            // Generate QR code using SimpleSoftwareIO\QrCode
            $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                ->size(400)
                ->margin(1)
                ->generate($qrData);

            $path = "qrcodes/ticket_{$ticket->ticket_number}.png";
            Storage::disk('public')->put($path, $qrCode);

            // Update ticket with QR code path
            $ticket->update(['qr_code_path' => $path]);

            Log::info('QR Code generated', [
                'ticket_number' => $ticket->ticket_number,
                'path' => $path,
            ]);

            return $path;
        } catch (\Exception $e) {
            Log::error('Failed to generate QR code', [
                'ticket_number' => $ticket->ticket_number,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Add footer
     */
    private function addFooter($pdf, Order $order)
    {
        // Check if we need a new page for footer
        if ($pdf->GetY() > 230) {
            $pdf->AddPage();
            $pdf->Ln(10);
        }

        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 6, 'IMPORTANT NOTES:', 0, 1);

        $pdf->SetFont('Arial', '', 9);
        $pdf->MultiCell(
            0,
            4,
            "1. Each ticket has a unique QR code that must be scanned individually\n" .
                "2. Please bring a valid ID (KTP/SIM/Passport) matching the identity number on this invoice\n" .
                "3. Present this invoice and scan each QR code at the registration desk to receive wristbands\n" .
                "4. Each QR code can only be used once and is non-transferable\n" .
                "5. Wristband exchange is available from [TIME] to [TIME] on event day\n" .
                "6. Lost or damaged tickets cannot be replaced\n" .
                "7. This invoice is non-refundable\n" .
                "8. For questions, contact us at: unoviacreative@gmail.com or +62 821-4081-7545"
        );

        $pdf->Ln(5);

        // Footer line
        $pdf->SetY(-20);
        $pdf->SetDrawColor(200, 200, 200);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY());

        $pdf->SetY(-15);
        $pdf->SetFont('Arial', 'I', 8);
        $pdf->Cell(0, 5, 'Thank you for your purchase! See you at the event!', 0, 1, 'C');
        $pdf->Cell(0, 5, 'Generated on ' . now()->format('d M Y H:i'), 0, 1, 'C');
    }

    /**
     * Get invoice URL
     */
    public function getInvoiceUrl(Order $order): ?string
    {
        if (!$order->invoice_path) {
            return null;
        }

        return Storage::disk('public')->url($order->invoice_path);
    }

    /**
     * Download invoice
     */
    public function downloadInvoice(Order $order)
    {
        if (!$order->invoice_path || !Storage::disk('public')->exists($order->invoice_path)) {
            // Generate if not exists
            $this->generateInvoice($order);
        }

        return Storage::disk('public')->download(
            $order->invoice_path,
            "Invoice_{$order->order_number}.pdf"
        );
    }
}
