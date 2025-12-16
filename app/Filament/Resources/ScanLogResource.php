<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ScanLogResource\Pages;
use App\Filament\Resources\ScanLogResource\RelationManagers;
use App\Models\ScanLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScanLogResource extends Resource
{
    protected static ?string $model = ScanLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('ticket_id')
                    ->relationship('ticket', 'id')
                    ->required(),
                Forms\Components\TextInput::make('scan_type')
                    ->required(),
                Forms\Components\TextInput::make('scanned_by')
                    ->required()
                    ->numeric(),
                Forms\Components\DateTimePicker::make('scanned_at')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\Textarea::make('notes')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('ip_address'),
                Forms\Components\Textarea::make('user_agent')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ticket.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scan_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('scanned_by')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scanned_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
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
            'index' => Pages\ListScanLogs::route('/'),
            'create' => Pages\CreateScanLog::route('/create'),
            'edit' => Pages\EditScanLog::route('/{record}/edit'),
        ];
    }
}
