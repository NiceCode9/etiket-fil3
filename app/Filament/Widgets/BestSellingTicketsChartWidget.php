<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use App\Models\TicketType;
use Filament\Widgets\ChartWidget;

class BestSellingTicketsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Best Selling Ticket Types';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '400px';
    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $user = auth()->user();
        
        if (!$user->tenant_id) {
            return $this->getEmptyData();
        }

        $tenantId = $user->tenant_id;

        // Get ticket types with their sales count for this tenant
        $ticketTypes = TicketType::whereHas('event', function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        })->withCount(['tickets' => function ($query) use ($tenantId) {
            $query->where('tenant_id', $tenantId);
        }])->get();

        $salesData = [];
        foreach ($ticketTypes as $type) {
            if ($type->tickets_count > 0) {
                $salesData[] = [
                    'name' => $type->name,
                    'count' => $type->tickets_count,
                ];
            }
        }

        // Sort by count descending and take top 10
        usort($salesData, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });
        
        $topTickets = array_slice($salesData, 0, 10);

        if (empty($topTickets)) {
            return $this->getEmptyData();
        }

        $labels = array_column($topTickets, 'name');
        $data = array_column($topTickets, 'count');

        $colors = [
            'rgba(59, 130, 246, 0.8)',
            'rgba(34, 197, 94, 0.8)',
            'rgba(251, 191, 36, 0.8)',
            'rgba(239, 68, 68, 0.8)',
            'rgba(168, 85, 247, 0.8)',
            'rgba(236, 72, 153, 0.8)',
            'rgba(20, 184, 166, 0.8)',
            'rgba(249, 115, 22, 0.8)',
            'rgba(14, 165, 233, 0.8)',
            'rgba(139, 92, 246, 0.8)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Tickets Sold',
                    'data' => $data,
                    'backgroundColor' => array_slice($colors, 0, count($data)),
                    'borderColor' => array_map(function($color) {
                        return str_replace('0.8', '1', $color);
                    }, array_slice($colors, 0, count($data))),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                    ],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }

    protected function getEmptyData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Tickets Sold',
                    'data' => [],
                ],
            ],
            'labels' => [],
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('tenant_admin') || $user->hasRole('super_admin'));
    }
}
