<?php

namespace App\Exports;

use App\Models\Event;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Facades\Auth;

class EventsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $tenantId;

    public function __construct($tenantId = null)
    {
        $this->tenantId = $tenantId;
    }

    public function query()
    {
        $query = Event::withoutGlobalScopes()
            ->with(['tenant', 'ticketTypes'])
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
            'Event Name',
            'Tenant',
            'Slug',
            'Venue',
            'Event Date',
            'Event End Date',
            'Status',
            'Ticket Types Count',
            'Created At',
        ];
    }

    public function map($event): array
    {
        return [
            $event->name,
            $event->tenant->name ?? '-',
            $event->slug,
            $event->venue,
            $event->event_date->format('d/m/Y H:i'),
            $event->event_end_date ? $event->event_end_date->format('d/m/Y H:i') : '-',
            ucfirst($event->status),
            $event->ticketTypes->count(),
            $event->created_at->format('d/m/Y H:i'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 20,
            'C' => 25,
            'D' => 30,
            'E' => 20,
            'F' => 20,
            'G' => 15,
            'H' => 18,
            'I' => 20,
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
