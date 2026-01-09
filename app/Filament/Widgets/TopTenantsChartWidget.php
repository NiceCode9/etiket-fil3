<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use App\Models\Order;
use Filament\Widgets\ChartWidget;

class TopTenantsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Tenants (Revenue)';
    protected static ?int $sort = 6;
    protected static ?string $maxHeight = '400px';
    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        // Get all tenants with their revenue
        $tenants = Tenant::withoutGlobalScopes()
            ->with(['orders' => function ($query) {
                $query->where('payment_status', 'paid');
            }])
            ->get();

        $revenueData = [];
        foreach ($tenants as $tenant) {
            $revenue = (float) $tenant->orders->sum('total_amount');
            
            if ($revenue > 0) {
                $revenueData[] = [
                    'name' => $tenant->name,
                    'revenue' => $revenue,
                ];
            }
        }

        // Sort by revenue descending and take top 10
        usort($revenueData, function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });
        
        $topTenants = array_slice($revenueData, 0, 10);

        if (empty($topTenants)) {
            return $this->getEmptyData();
        }

        $labels = array_column($topTenants, 'name');
        $data = array_column($topTenants, 'revenue');

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp)',
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
                            return 'Revenue: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }",
                    ],
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
                        'callback' => "function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }",
                    ],
                ],
            ],
        ];
    }

    protected function getColors(int $count): array
    {
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
                    'label' => 'Revenue (Rp)',
                    'data' => [],
                ],
            ],
            'labels' => [],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }
}