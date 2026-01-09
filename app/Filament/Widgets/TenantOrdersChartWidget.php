<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class TenantOrdersChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Orders (30 Hari Terakhir)';
    protected static ?int $sort = 2;
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
            
            $count = Order::where('tenant_id', $tenantId)
                ->whereBetween('created_at', [$startOfDay, $endOfDay])
                ->count();
            
            $data[] = $count;
            $labels[] = $date->format('d M');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.5)',
                    'borderColor' => 'rgb(59, 130, 246)',
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
                ],
            ],
            'scales' => [
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
                    'label' => 'Orders',
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
