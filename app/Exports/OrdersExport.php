<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\Auth;

class OrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $tenantId;

    public function __construct($tenantId = null)
    {
        $this->tenantId = $tenantId;
    }

    public function query()
    {
        $query = Order::withoutGlobalScopes()
            ->with(['customer', 'event', 'tenant', 'orderItems.ticketType'])
            ->latest();

        // Filter by tenant if provided (for tenant admin)
        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        } elseif (Auth::user() && Auth::user()->tenant_id && !Auth::user()->hasRole('super_admin')) {
            // Auto-filter for tenant admin
            $query->where('tenant_id', Auth::user()->tenant_id);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Tenant',
            'Customer Name',
            'Customer Email',
            'Customer Phone',
            'Event',
            'Total Amount',
            'Payment Status',
            'Payment Method',
            'Payment Channel',
            'Transaction ID',
            'Paid At',
            'Created At',
        ];
    }

    public function map($order): array
    {
        return [
            $order->order_number,
            $order->tenant->name ?? '-',
            $order->customer->full_name ?? '-',
            $order->customer->email ?? '-',
            $order->customer->phone_number ?? '-',
            $order->event->name ?? '-',
            $order->total_amount,
            ucfirst($order->payment_status),
            $order->payment_method ?? '-',
            $order->payment_channel ?? '-',
            $order->transaction_id ?? '-',
            $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : '-',
            $order->created_at->format('d/m/Y H:i'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 25,
            'D' => 30,
            'E' => 20,
            'F' => 30,
            'G' => 15,
            'H' => 15,
            'I' => 15,
            'J' => 15,
            'K' => 25,
            'L' => 20,
            'M' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E3F2FD'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
