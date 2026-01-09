<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TenantRevenueChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue (30 Hari Terakhir)';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $user = auth()->user();
        
        if (!$user->tenant_id) {
            return $this->getEmptyData();
        }

        $tenantId = $user->tenant_id;
        $data = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $startOfDay = $date->copy()->startOfDay();
            $endOfDay = $date->copy()->endOfDay();
            
            $revenue = Order::where('tenant_id', $tenantId)
                ->where('payment_status', 'paid')
                ->whereBetween('paid_at', [$startOfDay, $endOfDay])
                ->sum('total_amount');
            
            $data[] = (float) $revenue;
            $labels[] = $date->format('d M');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Revenue (Rp)',
                    'data' => $data,
                    'backgroundColor' => 'rgba(34, 197, 94, 0.5)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
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
        $user = auth()->user();
        return $user && ($user->hasRole('tenant_admin') || $user->hasRole('super_admin'));
    }
}
