<?php

namespace App\Exports;

use App\Models\Order;
use Codedge\Fpdf\Fpdf\Fpdf;
use Illuminate\Support\Facades\Auth;

class OrdersPdfExport extends Fpdf
{
    protected $orders;
    protected $tenantId;

    public function __construct($tenantId = null)
    {
        parent::__construct('L', 'mm', 'A4'); // Landscape orientation
        $this->tenantId = $tenantId;
        $this->loadData();
    }

    protected function loadData()
    {
        $query = Order::withoutGlobalScopes()
            ->with(['customer', 'event', 'tenant', 'orderItems.ticketType'])
            ->latest();

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        } elseif (Auth::user() && Auth::user()->tenant_id && !Auth::user()->hasRole('super_admin')) {
            $query->where('tenant_id', Auth::user()->tenant_id);
        }

        $this->orders = $query->limit(100)->get(); // Limit to 100 for PDF
    }

    public function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Orders Report', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Generated: ' . date('d/m/Y H:i:s'), 0, 1, 'C');
        $this->Ln(5);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

    public function generate()
    {
        $this->AliasNbPages();
        $this->AddPage();
        
        // Header row
        $this->SetFont('Arial', 'B', 9);
        $this->SetFillColor(200, 220, 255);
        $this->Cell(30, 7, 'Order Number', 1, 0, 'C', true);
        $this->Cell(25, 7, 'Customer', 1, 0, 'C', true);
        $this->Cell(40, 7, 'Event', 1, 0, 'C', true);
        $this->Cell(25, 7, 'Amount', 1, 0, 'C', true);
        $this->Cell(20, 7, 'Status', 1, 0, 'C', true);
        $this->Cell(30, 7, 'Payment Method', 1, 0, 'C', true);
        $this->Cell(30, 7, 'Paid At', 1, 0, 'C', true);
        $this->Cell(30, 7, 'Created At', 1, 1, 'C', true);

        // Data rows
        $this->SetFont('Arial', '', 8);
        foreach ($this->orders as $order) {
            $this->Cell(30, 6, substr($order->order_number, 0, 15), 1, 0, 'L');
            $this->Cell(25, 6, substr($order->customer->full_name ?? '-', 0, 20), 1, 0, 'L');
            $this->Cell(40, 6, substr($order->event->name ?? '-', 0, 30), 1, 0, 'L');
            $this->Cell(25, 6, 'Rp ' . number_format($order->total_amount, 0, ',', '.'), 1, 0, 'R');
            $this->Cell(20, 6, ucfirst($order->payment_status), 1, 0, 'C');
            $this->Cell(30, 6, $order->payment_method ?? '-', 1, 0, 'L');
            $this->Cell(30, 6, $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : '-', 1, 0, 'C');
            $this->Cell(30, 6, $order->created_at->format('d/m/Y H:i'), 1, 1, 'C');
        }

        return $this->Output('S'); // Return as string
    }
}
