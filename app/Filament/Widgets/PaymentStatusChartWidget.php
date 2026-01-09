<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class PaymentStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Payment Status Distribution';
    protected static ?int $sort = 7;
    protected static ?string $maxHeight = '300px';
    protected static ?string $pollingInterval = '30s';

    protected function getData(): array
    {
        $user = auth()->user();
        
        // Build query based on user role
        $query = $user->isSuperAdmin() 
            ? Order::withoutGlobalScopes()
            : Order::query(); // HasTenant trait will auto-filter

        $statuses = [
            'paid' => $query->clone()->where('payment_status', 'paid')->count(),
            'pending' => $query->clone()->where('payment_status', 'pending')->count(),
            'failed' => $query->clone()->where('payment_status', 'failed')->count(),
            'expired' => $query->clone()->where('payment_status', 'expired')->count(),
            'refunded' => $query->clone()->where('payment_status', 'refunded')->count(),
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
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->belongsToTenant());
    }
}