<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use App\Models\Tenant;
use App\Exports\EventsExport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Facades\Filament;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        $user = Filament::auth()->user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');
        $isTenantAdmin = $user && $user->hasRole('tenant_admin');

        return $form
            ->schema([
                Forms\Components\Section::make('Event Information')
                    ->schema([
                        // Tenant selection only for super_admin
                        Forms\Components\Select::make('tenant_id')
                            ->label('Tenant')
                            ->relationship('tenant', 'name')
                            ->required()
                            ->visible(fn () => $isSuperAdmin)
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                            ]),

                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn () => $isTenantAdmin)
                            ->live(onBlur: true)
                            ->afterStateUpdated(
                                fn(string $state, Forms\Set $set) =>
                                $set('slug', Str::slug($state))
                            ),

                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->disabled(fn () => $isTenantAdmin),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull()
                            ->disabled(fn () => $isTenantAdmin),

                        Forms\Components\TextInput::make('venue')
                            ->required()
                            ->maxLength(255)
                            ->disabled(fn () => $isTenantAdmin),

                        Forms\Components\DateTimePicker::make('event_date')
                            ->required()
                            ->native(false)
                            ->disabled(fn () => $isTenantAdmin),

                        Forms\Components\DateTimePicker::make('event_end_date')
                            ->native(false)
                            ->disabled(fn () => $isTenantAdmin),

                        Forms\Components\FileUpload::make('poster_image')
                            ->image()
                            ->directory('events/posters')
                            ->imageEditor()
                            ->columnSpanFull()
                            ->disabled(fn () => $isTenantAdmin),

                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'cancelled' => 'Cancelled',
                                'completed' => 'Completed',
                            ])
                            ->required()
                            ->default('draft')
                            ->disabled(fn () => $isTenantAdmin),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('poster_image')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('venue')
                    ->searchable(),

                Tables\Columns\TextColumn::make('event_date')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'success' => 'published',
                        'danger' => 'cancelled',
                        'info' => 'completed',
                    ]),

                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Tenant')
                    ->sortable()
                    ->searchable()
                    ->visible(fn () => Filament::auth()->user()?->hasRole('super_admin') ?? false),

                Tables\Columns\TextColumn::make('ticketTypes_count')
                    ->counts('ticketTypes')
                    ->label('Ticket Types'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'cancelled' => 'Cancelled',
                        'completed' => 'Completed',
                    ]),
                Tables\Filters\TrashedFilter::make(),
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
                            new EventsExport($tenantId),
                            'events_' . date('Y-m-d_His') . '.xlsx'
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
                            new EventsExport($tenantId),
                            'events_' . date('Y-m-d_His') . '.pdf',
                            \Maatwebsite\Excel\Excel::DOMPDF
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('event_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // Tambahkan RelationManager nanti
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
