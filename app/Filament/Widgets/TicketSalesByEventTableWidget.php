<?php

namespace App\Filament\Widgets;

use App\Models\Event;
use App\Models\TicketType;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TicketSalesByEventTableWidget extends BaseWidget
{
    protected static ?int $sort = 8;
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = '30s';

    public function table(Table $table): Table
    {
        $user = auth()->user();

        // Build query based on user role
        $query = $user->isSuperAdmin()
            ? TicketType::withoutGlobalScopes()
            : TicketType::query(); // HasTenant trait will auto-filter through event

        return $table
            ->query(
                $query
                    ->with(['event', 'tickets'])
                    ->whereHas('event', function ($q) use ($user) {
                        if ($user->isSuperAdmin()) {
                            $q->withoutGlobalScopes();
                        }
                    })
                    ->withCount('tickets')
            )
            ->columns([
                Tables\Columns\TextColumn::make('event.name')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->description(fn (TicketType $record): string => 
                        $record->event->event_date->format('d M Y') . ' • ' . $record->event->venue
                    ),

                Tables\Columns\TextColumn::make('name')
                    ->label('Ticket Type')
                    ->searchable()
                    ->sortable()
                    ->description(fn (TicketType $record): string => $record->description ?? '-'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('IDR', locale: 'id')
                    ->sortable(),

                Tables\Columns\TextColumn::make('quota')
                    ->label('Total Quota')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('tickets_count')
                    ->label('Sold')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color(fn (int $state, TicketType $record): string => 
                        $state >= $record->quota ? 'danger' : ($state >= $record->quota * 0.8 ? 'warning' : 'success')
                    ),

                Tables\Columns\TextColumn::make('available_quota')
                    ->label('Available')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->color(fn (int $state): string => 
                        $state == 0 ? 'danger' : ($state <= 10 ? 'warning' : 'success')
                    ),

                Tables\Columns\ViewColumn::make('sold_percentage')
                    ->label('Progress')
                    ->view('filament.widgets.ticket-sales-progress')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderByRaw('(COALESCE((SELECT COUNT(*) FROM tickets WHERE tickets.ticket_type_id = ticket_types.id), 0) / ticket_types.quota) ' . $direction);
                    }),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->getStateUsing(function (TicketType $record): string {
                        if ($record->available_quota <= 0) {
                            return 'sold_out';
                        } elseif ($record->available_quota <= 10) {
                            return 'low_stock';
                        } elseif (!$record->is_active) {
                            return 'inactive';
                        } else {
                            return 'available';
                        }
                    })
                    ->colors([
                        'danger' => 'sold_out',
                        'warning' => 'low_stock',
                        'secondary' => 'inactive',
                        'success' => 'available',
                    ])
                    ->icons([
                        'heroicon-o-x-circle' => 'sold_out',
                        'heroicon-o-exclamation-triangle' => 'low_stock',
                        'heroicon-o-pause-circle' => 'inactive',
                        'heroicon-o-check-circle' => 'available',
                    ]),
            ])
            ->defaultSort('tickets_count', 'desc')
            ->defaultGroup('event.name')
            ->groups([
                Tables\Grouping\Group::make('event.name')
                    ->label('Event')
                    ->collapsible()
                    ->titlePrefixedWithLabel(false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event_id')
                    ->label('Event')
                    ->relationship('event', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'available' => 'Available',
                        'low_stock' => 'Low Stock',
                        'sold_out' => 'Sold Out',
                        'inactive' => 'Inactive',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'sold_out' => $query->where('available_quota', '<=', 0),
                            'low_stock' => $query->where('available_quota', '>', 0)
                                                 ->where('available_quota', '<=', 10),
                            'inactive' => $query->where('is_active', false),
                            'available' => $query->where('available_quota', '>', 10)
                                                 ->where('is_active', true),
                            default => $query,
                        };
                    }),
            ])
            ->heading($this->getHeading());
    }

    protected function getHeading(): string
    {
        $user = auth()->user();
        $suffix = $user->isSuperAdmin() ? ' (All Tenants)' : '';
        return 'Ticket Sales by Event' . $suffix;
    }

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && ($user->isSuperAdmin() || $user->belongsToTenant());
    }
}