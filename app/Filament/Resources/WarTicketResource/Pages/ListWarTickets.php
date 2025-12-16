<?php

namespace App\Filament\Resources\WarTicketResource\Pages;

use App\Filament\Resources\WarTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWarTickets extends ListRecords
{
    protected static string $resource = WarTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
