<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersTableWidget extends BaseWidget
{
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '15s';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        
        // Build base query
        $query = $user->isSuperAdmin() 
            ? Order::withoutGlobalScopes()
            : Order::query(); // HasTenant trait will auto-filter

        return $table
            ->query(
                $query
                    ->with(['customer', 'event', 'tenant'])
                    ->latest()
                    ->limit(10)
            )
            ->columns($this->getTableColumns())
            ->defaultSort('created_at', 'desc')
            ->heading('Recent Orders');
    }

    protected function getTableColumns(): array
    {
        $user = auth()->user();
        $columns = [
            Tables\Columns\TextColumn::make('order_number')
                ->label('Order Number')
                ->searchable()
                ->sortable(),
        ];

        // Only show tenant column for super admin
        if ($user->isSuperAdmin()) {
            $columns[] = Tables\Columns\TextColumn::make('tenant.name')
                ->label('Tenant')
                ->searchable()
                ->sortable()
                ->badge()
                ->color('primary');
        }

        $columns = array_merge($columns, [
            Tables\Columns\TextColumn::make('customer.full_name')
                ->label('Customer')
                ->searchable()
                ->sortable(),
            
            Tables\Columns\TextColumn::make('event.name')
                ->label('Event')
                ->searchable()
                ->sortable()
                ->limit(30),
            
            Tables\Columns\TextColumn::make('total_amount')
                ->label('Amount')
                ->money('IDR', locale: 'id')
                ->sortable(),
            
            Tables\Columns\BadgeColumn::make('payment_status')
                ->label('Status')
                ->colors([
                    'warning' => 'pending',
                    'success' => 'paid',
                    'danger' => 'failed',
                    'secondary' => 'expired',
                    'info' => 'refunded',
                ])
                ->sortable(),
            
            Tables\Columns\TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime('d M Y, H:i')
                ->sortable(),
        ]);

        return $columns;
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->belongsToTenant());
    }
}