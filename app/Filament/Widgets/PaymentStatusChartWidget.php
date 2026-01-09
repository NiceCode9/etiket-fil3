<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class PaymentStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Payment Status Distribution';
    protected static ?int $sort = 6;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $statuses = [
            'paid' => Order::withoutGlobalScopes()->where('payment_status', 'paid')->count(),
            'pending' => Order::withoutGlobalScopes()->where('payment_status', 'pending')->count(),
            'failed' => Order::withoutGlobalScopes()->where('payment_status', 'failed')->count(),
            'expired' => Order::withoutGlobalScopes()->where('payment_status', 'expired')->count(),
            'refunded' => Order::withoutGlobalScopes()->where('payment_status', 'refunded')->count(),
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Orders',
                    'data' => array_values($statuses),
                    'backgroundColor' => [
                        'rgba(34, 197, 94, 0.8)',   // paid - green
                        'rgba(251, 191, 36, 0.8)',  // pending - yellow
                        'rgba(239, 68, 68, 0.8)',   // failed - red
                        'rgba(107, 114, 128, 0.8)', // expired - gray
                        'rgba(59, 130, 246, 0.8)',  // refunded - blue
                    ],
                    'borderColor' => [
                        'rgb(34, 197, 94)',
                        'rgb(251, 191, 36)',
                        'rgb(239, 68, 68)',
                        'rgb(107, 114, 128)',
                        'rgb(59, 130, 246)',
                    ],
                ],
            ],
            'labels' => array_keys($statuses),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
                'tooltip' => [
                    'enabled' => true,
                ],
            ],
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }
}
