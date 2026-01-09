<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WarTicketResource\Pages;
use App\Filament\Resources\WarTicketResource\RelationManagers;
use App\Models\WarTicket;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WarTicketResource extends Resource
{
    protected static ?string $model = WarTicket::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ticket_type_id')
                    ->relationship('ticketType', 'name')
                    ->required(),
                Forms\Components\TextInput::make('war_price')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('war_quota')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('war_available_quota')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('start_time')
                    ->required(),
                Forms\Components\DateTimePicker::make('end_time')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticketType.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('war_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('war_quota')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('war_available_quota')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
                Tables\Actions\EditAction::make(),
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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $user = Filament::auth()->user();

        // Super admin dapat melihat semua data
        if ($user && $user->hasRole('super_admin')) {
            return $query;
        }

        // Tenant hanya dapat melihat war ticket untuk event di tenant-nya
        if ($user && $user->tenant_id) {
            $query->whereHas('ticketType.event', function (Builder $eventQuery) use ($user) {
                $eventQuery->where('tenant_id', $user->tenant_id);
            });
        }

        return $query;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWarTickets::route('/'),
            'create' => Pages\CreateWarTicket::route('/create'),
            'edit' => Pages\EditWarTicket::route('/{record}/edit'),
        ];
    }
}
