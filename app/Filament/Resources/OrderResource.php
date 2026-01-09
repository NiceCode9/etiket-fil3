<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Tenant;
use App\Exports\OrdersExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {
        $user = Filament::auth()->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');
        $isTenantAdmin = $user && $user->hasRole('tenant_admin');

        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Tenant')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->visible(fn () => $isSuperAdmin)
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('order_number')
                    ->required()
                    ->disabled(fn () => $isTenantAdmin),

                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'full_name')
                    ->required()
                    ->disabled(fn () => $isTenantAdmin)
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'name', fn ($query) => 
                        $isSuperAdmin ? $query : $query->where('tenant_id', $user->tenant_id)
                    )
                    ->required()
                    ->disabled(fn () => $isTenantAdmin)
                    ->searchable()
                    ->preload(),

                Forms\Components\TextInput::make('total_amount')
                    ->required()
                    ->numeric()
                    ->disabled(fn () => $isTenantAdmin),

                Forms\Components\Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'expired' => 'Expired',
                        'cancelled' => 'Cancelled',
                        'refunded' => 'Refunded',
                    ])
                    ->required()
                    ->disabled(fn () => $isTenantAdmin),

                Forms\Components\TextInput::make('payment_method')
                    ->disabled(fn () => $isTenantAdmin),

                Forms\Components\TextInput::make('payment_channel')
                    ->disabled(fn () => $isTenantAdmin),

                Forms\Components\TextInput::make('transaction_id')
                    ->disabled(fn () => $isTenantAdmin),

                Forms\Components\DateTimePicker::make('paid_at')
                    ->disabled(fn () => $isTenantAdmin),

                Forms\Components\DateTimePicker::make('expired_at')
                    ->disabled(fn () => $isTenantAdmin),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => Filament::auth()->user()?->hasRole('super_admin') ?? false),

                Tables\Columns\TextColumn::make('customer.full_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('Event')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('payment_status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_method')
                    ->searchable(),
                Tables\Columns\TextColumn::make('payment_channel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('transaction_id')
                    ->searchable(),
                Tables\Columns\TextColumn::make('paid_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => !(Filament::auth()->user()?->hasRole('tenant_admin') ?? false)),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => !(Filament::auth()->user()?->hasRole('tenant_admin') ?? false)),
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Export Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function () {
                        $user = Auth::user();
                        $tenantId = $user->hasRole('super_admin') ? null : $user->tenant_id;
                        
                        return Excel::download(
                            new OrdersExport($tenantId),
                            'orders_' . date('Y-m-d_His') . '.xlsx'
                        );
                    }),
                
                Action::make('exportPdf')
                    ->label('Export PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('danger')
                    ->action(function () {
                        $user = Auth::user();
                        $tenantId = $user->hasRole('super_admin') ? null : $user->tenant_id;
                        
                        return Excel::download(
                            new OrdersExport($tenantId),
                            'orders_' . date('Y-m-d_His') . '.pdf',
                            \Maatwebsite\Excel\Excel::DOMPDF
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
