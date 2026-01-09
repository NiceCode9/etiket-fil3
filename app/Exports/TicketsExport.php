<?php

namespace App\Exports;

use App\Models\Ticket;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\Auth;

class TicketsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $tenantId;

    public function __construct($tenantId = null)
    {
        $this->tenantId = $tenantId;
    }

    public function query()
    {
        $query = Ticket::withoutGlobalScopes()
            ->with(['customer', 'event', 'ticketType', 'tenant'])
            ->latest();

        if ($this->tenantId) {
            $query->where('tenant_id', $this->tenantId);
        } elseif (Auth::user() && Auth::user()->tenant_id && !Auth::user()->hasRole('super_admin')) {
            $query->where('tenant_id', Auth::user()->tenant_id);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'Ticket Number',
            'Tenant',
            'Customer Name',
            'Customer Email',
            'Event',
            'Ticket Type',
            'Status',
            'QR Code',
            'Wristband Code',
            'Scanned For Wristband At',
            'Wristband Validated At',
            'Created At',
        ];
    }

    public function map($ticket): array
    {
        return [
            $ticket->ticket_number,
            $ticket->tenant->name ?? '-',
            $ticket->customer->full_name ?? '-',
            $ticket->customer->email ?? '-',
            $ticket->event->name ?? '-',
            $ticket->ticketType->name ?? '-',
            ucfirst($ticket->status),
            $ticket->qr_code,
            $ticket->wristband_code ?? '-',
            $ticket->scanned_for_wristband_at ? $ticket->scanned_for_wristband_at->format('d/m/Y H:i') : '-',
            $ticket->wristband_validated_at ? $ticket->wristband_validated_at->format('d/m/Y H:i') : '-',
            $ticket->created_at->format('d/m/Y H:i'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 20,
            'C' => 25,
            'D' => 30,
            'E' => 30,
            'F' => 20,
            'G' => 15,
            'H' => 40,
            'I' => 20,
            'J' => 25,
            'K' => 25,
            'L' => 20,
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
