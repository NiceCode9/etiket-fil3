<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Filament\Resources\TicketResource\RelationManagers;
use App\Models\Ticket;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('ticket_number')
                    ->required(),
                Forms\Components\Select::make('order_item_id')
                    ->relationship('orderItem', 'id')
                    ->required(),
                Forms\Components\Select::make('customer_id')
                    ->relationship('customer', 'id')
                    ->required(),
                Forms\Components\Select::make('event_id')
                    ->relationship('event', 'name')
                    ->required(),
                Forms\Components\Select::make('ticket_type_id')
                    ->relationship('ticketType', 'name')
                    ->required(),
                Forms\Components\TextInput::make('qr_code')
                    ->required(),
                Forms\Components\TextInput::make('qr_code_path'),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\TextInput::make('wristband_code'),
                Forms\Components\DateTimePicker::make('scanned_for_wristband_at'),
                Forms\Components\TextInput::make('scanned_for_wristband_by')
                    ->numeric(),
                Forms\Components\DateTimePicker::make('wristband_validated_at'),
                Forms\Components\TextInput::make('wristband_validated_by')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('orderItem.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('event.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ticketType.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('qr_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('qr_code_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('wristband_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scanned_for_wristband_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scanned_for_wristband_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('wristband_validated_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('wristband_validated_by')
                    ->numeric()
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}
