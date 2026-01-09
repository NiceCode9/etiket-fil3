<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\TicketType;
use Filament\Widgets\ChartWidget;

class TicketSalesByEventChartWidget extends ChartWidget
{
    protected static ?int $sort = 9;
    protected static ?string $maxHeight = '400px';
    protected static ?string $pollingInterval = '30s';

    public ?string $filter = null;

    public function getHeading(): string
    {
        $user = auth()->user();
        $suffix = $user->isSuperAdmin() ? ' (All Tenants)' : '';
        
        if ($this->filter) {
            $event = $this->getEvent();
            $eventName = $event ? ' - ' . $event->name : '';
            return 'Ticket Sales by Type' . $eventName . $suffix;
        }
        
        return 'Ticket Sales by Type - Select Event' . $suffix;
    }

    protected function getFilters(): ?array
    {
        $user = auth()->user();
        
        // Build event query based on user role
        $eventQuery = $user->isSuperAdmin()
            ? Event::withoutGlobalScopes()
            : Event::query(); // HasTenant trait will auto-filter

        $events = $eventQuery
            ->orderBy('event_date', 'desc')
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        // Add placeholder option
        return [null => 'Select an event...'] + $events;
    }

    protected function getData(): array
    {
        if (!$this->filter) {
            return $this->getEmptyDataWithMessage();
        }

        $event = $this->getEvent();
        
        if (!$event) {
            return $this->getEmptyData();
        }

        // Get ticket types for selected event
        $user = auth()->user();
        
        $query = TicketType::where('event_id', $this->filter);
        
        if (!$user->isSuperAdmin()) {
            // For tenant admin, ensure we only get their tenant's ticket types
            $query->whereHas('event', function($q) {
                // HasTenant trait will auto-filter
            });
        }
        
        $ticketTypes = $query
            ->withCount('tickets')
            ->orderBy('tickets_count', 'asc') // Dari terendah ke tertinggi
            ->get();

        if ($ticketTypes->isEmpty()) {
            return $this->getEmptyData();
        }

        $labels = $ticketTypes->pluck('name')->toArray();
        $soldData = $ticketTypes->pluck('tickets_count')->toArray();
        $quotaData = $ticketTypes->pluck('quota')->toArray();
        $availableData = $ticketTypes->map(fn($t) => $t->available_quota)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Tickets Sold',
                    'data' => $soldData,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.7)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Available',
                    'data' => $availableData,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.7)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 2,
                ],
                [
                    'label' => 'Total Quota',
                    'data' => $quotaData,
                    'backgroundColor' => 'rgba(107, 114, 128, 0.3)',
                    'borderColor' => 'rgb(107, 114, 128)',
                    'borderWidth' => 2,
                    'type' => 'line',
                    'fill' => false,
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
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
                'tooltip' => [
                    'enabled' => true,
                    'callbacks' => [
                        'label' => "function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed.y + ' tickets';
                            return label;
                        }",
                        'afterLabel' => "function(context) {
                            if (context.datasetIndex === 0) {
                                const total = context.chart.data.datasets[2].data[context.dataIndex];
                                const sold = context.parsed.y;
                                const percentage = total > 0 ? ((sold / total) * 100).toFixed(1) : 0;
                                return 'Sold: ' + percentage + '%';
                            }
                            return '';
                        }",
                    ],
                ],
            ],
            'scales' => [
                'x' => [
                    'stacked' => false,
                    'ticks' => [
                        'maxRotation' => 45,
                        'minRotation' => 45,
                    ],
                ],
                'y' => [
                    'stacked' => false,
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                        'callback' => "function(value) {
                            return value;
                        }",
                    ],
                ],
            ],
        ];
    }

    protected function getEvent(): ?Event
    {
        if (!$this->filter) {
            return null;
        }

        $user = auth()->user();
        
        $query = $user->isSuperAdmin()
            ? Event::withoutGlobalScopes()
            : Event::query();
            
        return $query->find($this->filter);
    }

    protected function getEmptyData(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'No Data Available',
                    'data' => [],
                    'backgroundColor' => [],
                ],
            ],
            'labels' => [],
        ];
    }

    protected function getEmptyDataWithMessage(): array
    {
        return [
            'datasets' => [
                [
                    'label' => 'Please select an event from dropdown above',
                    'data' => [0],
                    'backgroundColor' => 'rgba(107, 114, 128, 0.3)',
                ],
            ],
            'labels' => ['No Event Selected'],
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->belongsToTenant());
    }
}