<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\Scan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;

class TenantStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = auth()->user();
        
        // Only show stats for tenant users
        if (!$user->tenant_id) {
            return [];
        }

        $tenantId = $user->tenant_id;
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        
        // Revenue Stats
        $revenueToday = Order::where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->where('paid_at', '>=', $today)
            ->sum('total_amount');
        
        $revenueThisMonth = Order::where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->where('paid_at', '>=', $thisMonth)
            ->sum('total_amount');
        
        $revenueAllTime = Order::where('tenant_id', $tenantId)
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        // Orders Stats
        $ordersToday = Order::where('tenant_id', $tenantId)
            ->where('created_at', '>=', $today)
            ->count();
        $ordersThisMonth = Order::where('tenant_id', $tenantId)
            ->where('created_at', '>=', $thisMonth)
            ->count();
        $ordersAllTime = Order::where('tenant_id', $tenantId)->count();
        
        // Tickets Stats
        $ticketsSold = Ticket::where('tenant_id', $tenantId)->count();
        $ticketsUsed = Ticket::where('tenant_id', $tenantId)
            ->where('status', 'used')
            ->count();
        $ticketsActive = Ticket::where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->count();
        
        // Events Stats
        $totalEvents = Event::where('tenant_id', $tenantId)->count();
        $activeEvents = Event::where('tenant_id', $tenantId)
            ->where('status', 'published')
            ->where('event_date', '>=', now())
            ->count();
        
        // Scans Stats
        $scansToday = Scan::where('tenant_id', $tenantId)
            ->where('scanned_at', '>=', $today)
            ->count();
        $scansThisMonth = Scan::where('tenant_id', $tenantId)
            ->where('scanned_at', '>=', $thisMonth)
            ->count();

        return [
            Stat::make('Revenue Hari Ini', Number::currency($revenueToday, 'IDR', 'id'))
                ->description(Number::currency($revenueThisMonth, 'IDR', 'id') . ' bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
            
            Stat::make('Total Revenue', Number::currency($revenueAllTime, 'IDR', 'id'))
                ->description($ordersAllTime . ' total orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            
            Stat::make('Orders Hari Ini', $ordersToday)
                ->description($ordersThisMonth . ' bulan ini')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
            
            Stat::make('Tickets Terjual', Number::format($ticketsSold))
                ->description($ticketsUsed . ' digunakan, ' . $ticketsActive . ' aktif')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning'),
            
            Stat::make('Total Events', $totalEvents)
                ->description($activeEvents . ' event aktif')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary'),
            
            Stat::make('Scans Hari Ini', $scansToday)
                ->description($scansThisMonth . ' bulan ini')
                ->descriptionIcon('heroicon-m-qr-code')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('tenant_admin') || $user->hasRole('super_admin'));
    }
}
