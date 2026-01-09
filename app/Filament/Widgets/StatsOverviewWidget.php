<?php

namespace App\Filament\Widgets;

use App\Models\Tenant;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Event;
use App\Models\Scan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Number;
use Filament\Facades\Filament;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s'; // Update every 30 seconds for realtime

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        
        // Total Tenants - bypass scope for super admin
        $totalTenants = Tenant::withoutGlobalScopes()->count();
        $activeTenants = Tenant::withoutGlobalScopes()->where('is_active', true)->count();
        
        // Revenue Stats - bypass scope for super admin
        $revenueToday = Order::withoutGlobalScopes()
            ->where('payment_status', 'paid')
            ->where('paid_at', '>=', $today)
            ->sum('total_amount');
        
        $revenueThisMonth = Order::withoutGlobalScopes()
            ->where('payment_status', 'paid')
            ->where('paid_at', '>=', $thisMonth)
            ->sum('total_amount');
        
        $revenueAllTime = Order::withoutGlobalScopes()
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        
        // Orders Stats - bypass scope for super admin
        $ordersToday = Order::withoutGlobalScopes()
            ->where('created_at', '>=', $today)
            ->count();
        $ordersThisMonth = Order::withoutGlobalScopes()
            ->where('created_at', '>=', $thisMonth)
            ->count();
        $ordersAllTime = Order::withoutGlobalScopes()->count();
        
        // Tickets Stats - bypass scope for super admin
        $ticketsSold = Ticket::withoutGlobalScopes()->count();
        $ticketsUsed = Ticket::withoutGlobalScopes()->where('status', 'used')->count();
        $ticketsActive = Ticket::withoutGlobalScopes()->where('status', 'active')->count();
        
        // Events Stats - bypass scope for super admin
        $totalEvents = Event::withoutGlobalScopes()->count();
        $activeEvents = Event::withoutGlobalScopes()
            ->where('status', 'published')
            ->where('event_date', '>=', now())
            ->count();
        
        // Users Stats
        $totalUsers = User::count();
        $tenantUsers = User::whereNotNull('tenant_id')->count();
        
        // Scans Stats - bypass scope for super admin
        $scansToday = Scan::withoutGlobalScopes()
            ->where('scanned_at', '>=', $today)
            ->count();
        $scansThisMonth = Scan::withoutGlobalScopes()
            ->where('scanned_at', '>=', $thisMonth)
            ->count();

        return [
            Stat::make('Total Tenants', $totalTenants)
                ->description($activeTenants . ' aktif')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart([7, 3, 4, 5, 6, 3, 5])
                ->url('/admin/tenants'),
            
            Stat::make('Revenue Hari Ini', Number::currency($revenueToday, 'IDR', 'id'))
                ->description(Number::currency($revenueThisMonth, 'IDR', 'id') . ' bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
            
            Stat::make('Total Revenue', Number::currency($revenueAllTime, 'IDR', 'id'))
                ->description($ordersAllTime . ' total orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->url('/admin/orders'),
            
            Stat::make('Orders Hari Ini', $ordersToday)
                ->description($ordersThisMonth . ' bulan ini')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
            
            Stat::make('Tickets Terjual', Number::format($ticketsSold))
                ->description($ticketsUsed . ' digunakan, ' . $ticketsActive . ' aktif')
                ->descriptionIcon('heroicon-m-ticket')
                ->color('warning')
                ->url('/admin/tickets'),
            
            Stat::make('Total Events', $totalEvents)
                ->description($activeEvents . ' event aktif')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('primary')
                ->url('/admin/events'),
            
            Stat::make('Total Users', $totalUsers)
                ->description($tenantUsers . ' tenant users')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->url('/admin/users'),
            
            Stat::make('Scans Hari Ini', $scansToday)
                ->description($scansThisMonth . ' bulan ini')
                ->descriptionIcon('heroicon-m-qr-code')
                ->color('success')
                ->chart([7, 3, 4, 5, 6, 3, 5]),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasRole('super_admin') ?? false;
    }
}
