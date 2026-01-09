<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\OrdersChartWidget;
use App\Filament\Widgets\RevenueChartWidget;
use App\Filament\Widgets\TopTenantsChartWidget;
use App\Filament\Widgets\RecentOrdersTableWidget;
use App\Filament\Widgets\PaymentStatusChartWidget;

class SuperAdminDashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Super Admin Dashboard';
    protected static ?int $navigationSort = -1;

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            OrdersChartWidget::class,
            RevenueChartWidget::class,
            TopTenantsChartWidget::class,
            PaymentStatusChartWidget::class,
            RecentOrdersTableWidget::class,
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }
}
