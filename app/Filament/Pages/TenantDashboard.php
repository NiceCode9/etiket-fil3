<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\TenantStatsWidget;
use App\Filament\Widgets\TenantOrdersChartWidget;
use App\Filament\Widgets\TenantRevenueChartWidget;
use App\Filament\Widgets\BestSellingTicketsChartWidget;

class TenantDashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Tenant Dashboard';
    protected static ?int $navigationSort = -1;

    public function getWidgets(): array
    {
        return [
            TenantStatsWidget::class,
            TenantOrdersChartWidget::class,
            TenantRevenueChartWidget::class,
            BestSellingTicketsChartWidget::class,
        ];
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        // Only tenant_admin can access this dashboard
        return $user && $user->hasRole('tenant_admin') && !$user->hasRole('super_admin');
    }
}
