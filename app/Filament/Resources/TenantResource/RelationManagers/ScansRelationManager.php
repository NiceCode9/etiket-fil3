<?php

namespace App\Filament\Resources\TenantResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ScansRelationManager extends RelationManager
{
    protected static string $relationship = 'scans';

    protected static ?string $title = 'Scans';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('ticket_id')
                    ->relationship('ticket', 'ticket_number')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('scan_type')
                    ->options([
                        'qr_scan' => 'QR Scan',
                        'wristband_validation' => 'Wristband Validation',
                    ])
                    ->required(),
                Forms\Components\DateTimePicker::make('scanned_at')
                    ->required()
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ticket.ticket_number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('scan_type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'qr_scan' => 'QR Scan',
                        'wristband_validation' => 'Wristband Validation',
                        default => 'Unknown',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'qr_scan' => 'info',
                        'wristband_validation' => 'success',
                        default => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('scanned_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('scan_type')
                    ->options([
                        'qr_scan' => 'QR Scan',
                        'wristband_validation' => 'Wristband Validation',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('scanned_at', 'desc');
    }
}
