<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Number;

class TopTenantsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Top 10 Tenants (Revenue)';
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $tenants = Tenant::withCount(['orders' => function ($query) {
            $query->where('payment_status', 'paid');
        }])->get();

        $revenueData = [];
        foreach ($tenants as $tenant) {
            $revenue = Order::withoutGlobalScopes()
                ->where('tenant_id', $tenant->id)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
            
            if ($revenue > 0) {
                $revenueData[] = [
                    'name' => $tenant->name,
                    'revenue' => (float) $revenue,
                ];
            }
        }

        // Sort by revenue descending and take top 10
        usort($revenueData, function ($a, $b) {
            return $b['revenue'] <=> $a['revenue'];
        });
        
        $topTenants = array_slice($revenueData, 0, 10);

        $labels = array_column($topTenants, 'name');
        $data = array_column($topTenants, 'revenue');

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp)',
                    'data' => $data,
                    'backgroundColor' => [
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
                    ],
                    'borderColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(34, 197, 94)',
                        'rgb(251, 191, 36)',
                        'rgb(239, 68, 68)',
                        'rgb(168, 85, 247)',
                        'rgb(236, 72, 153)',
                        'rgb(20, 184, 166)',
                        'rgb(249, 115, 22)',
                        'rgb(14, 165, 233)',
                        'rgb(139, 92, 246)',
                    ],
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

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }
}
