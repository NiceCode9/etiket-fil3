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

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $user = auth()->user();
        
        // Super admin sees all stats, tenant admin sees tenant-specific stats
        if ($user->isSuperAdmin()) {
            return $this->getSuperAdminStats();
        }
        
        if ($user->belongsToTenant()) {
            return $this->getTenantStats($user->tenant_id);
        }
        
        return [];
    }

    protected function getSuperAdminStats(): array
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        
        // Total Tenants
        $totalTenants = Tenant::withoutGlobalScopes()->count();
        $activeTenants = Tenant::withoutGlobalScopes()->where('is_active', true)->count();
        
        // Revenue Stats
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
        
        // Orders Stats
        $ordersToday = Order::withoutGlobalScopes()
            ->where('created_at', '>=', $today)
            ->count();
        $ordersThisMonth = Order::withoutGlobalScopes()
            ->where('created_at', '>=', $thisMonth)
            ->count();
        $ordersAllTime = Order::withoutGlobalScopes()->count();
        
        // Tickets Stats
        $ticketsSold = Ticket::withoutGlobalScopes()->count();
        $ticketsUsed = Ticket::withoutGlobalScopes()->where('status', 'used')->count();
        $ticketsActive = Ticket::withoutGlobalScopes()->where('status', 'active')->count();
        
        // Events Stats
        $totalEvents = Event::withoutGlobalScopes()->count();
        $activeEvents = Event::withoutGlobalScopes()
            ->where('status', 'published')
            ->where('event_date', '>=', now())
            ->count();
        
        // Users Stats
        $totalUsers = User::count();
        $tenantUsers = User::whereNotNull('tenant_id')->count();
        
        // Scans Stats
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

    protected function getTenantStats(int $tenantId): array
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        
        // Revenue Stats - automatically filtered by HasTenant trait
        $revenueToday = Order::where('payment_status', 'paid')
            ->where('paid_at', '>=', $today)
            ->sum('total_amount');
        
        $revenueThisMonth = Order::where('payment_status', 'paid')
            ->where('paid_at', '>=', $thisMonth)
            ->sum('total_amount');
        
        $revenueAllTime = Order::where('payment_status', 'paid')
            ->sum('total_amount');
        
        // Orders Stats - automatically filtered by HasTenant trait
        $ordersToday = Order::where('created_at', '>=', $today)->count();
        $ordersThisMonth = Order::where('created_at', '>=', $thisMonth)->count();
        $ordersAllTime = Order::count();
        
        // Tickets Stats - automatically filtered by HasTenant trait
        $ticketsSold = Ticket::count();
        $ticketsUsed = Ticket::where('status', 'used')->count();
        $ticketsActive = Ticket::where('status', 'active')->count();
        
        // Events Stats - automatically filtered by HasTenant trait
        $totalEvents = Event::count();
        $activeEvents = Event::where('status', 'published')
            ->where('event_date', '>=', now())
            ->count();
        
        // Scans Stats - automatically filtered by HasTenant trait
        $scansToday = Scan::where('scanned_at', '>=', $today)->count();
        $scansThisMonth = Scan::where('scanned_at', '>=', $thisMonth)->count();

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
        return $user && ($user->isSuperAdmin() || $user->belongsToTenant());
    }
}