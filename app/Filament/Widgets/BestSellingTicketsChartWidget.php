<?php

namespace App\Filament\Widgets;

use App\Models\TicketType;
use Filament\Widgets\ChartWidget;

class BestSellingTicketsChartWidget extends ChartWidget
{
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '400px';
    protected static ?string $pollingInterval = '30s';

    public function getHeading(): string
    {
        $user = auth()->user();
        $suffix = $user->isSuperAdmin() ? ' (All Tenants)' : '';
        return 'Best Selling Ticket Types' . $suffix;
    }

    protected function getData(): array
    {
        $user = auth()->user();

        // Build query based on user role
        $query = TicketType::query()->with(['event', 'tickets']);

        if ($user->isSuperAdmin()) {
            // Super admin: get all ticket types from all tenants
            $query->whereHas('event', function ($q) {
                $q->withoutGlobalScopes();
            });
        }
        // For tenant admin: HasTenant trait will auto-filter through event relationship

        // Get ticket types with their sales count
        $ticketTypes = $query->withCount('tickets')->get();

        $salesData = [];
        foreach ($ticketTypes as $type) {
            if ($type->tickets_count > 0) {
                $salesData[] = [
                    'name' => $type->name,
                    'event' => $type->event->name ?? 'N/A',
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

        // Create labels with event name (truncated)
        $labels = array_map(function($item) {
            $eventName = mb_strlen($item['event']) > 20 
                ? mb_substr($item['event'], 0, 20) . '...' 
                : $item['event'];
            return $item['name'] . ' (' . $eventName . ')';
        }, $topTickets);
        
        $data = array_column($topTickets, 'count');

        return [
            'datasets' => [
                [
                    'label' => 'Tickets Sold',
                    'data' => $data,
                    'backgroundColor' => $this->getColors(count($data)),
                    'borderColor' => $this->getBorderColors(count($data)),
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
                    'callbacks' => [
                        'label' => "function(context) {
                            return 'Tickets Sold: ' + context.parsed.y;
                        }",
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                        'font' => [
                            'size' => 10,
                        ],
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

    protected function getColors(int $count): array
    {
        $colors = [
            'rgba(59, 130, 246, 0.8)',   // blue
            'rgba(34, 197, 94, 0.8)',    // green
            'rgba(251, 191, 36, 0.8)',   // yellow
            'rgba(239, 68, 68, 0.8)',    // red
            'rgba(168, 85, 247, 0.8)',   // purple
            'rgba(236, 72, 153, 0.8)',   // pink
            'rgba(20, 184, 166, 0.8)',   // teal
            'rgba(249, 115, 22, 0.8)',   // orange
            'rgba(14, 165, 233, 0.8)',   // sky
            'rgba(139, 92, 246, 0.8)',   // violet
        ];

        return array_slice($colors, 0, $count);
    }

    protected function getBorderColors(int $count): array
    {
        return array_map(function($color) {
            return str_replace('0.8', '1', $color);
        }, $this->getColors($count));
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
        return $user && ($user->isSuperAdmin() || $user->belongsToTenant());
    }
}