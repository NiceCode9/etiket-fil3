<?php

namespace App\Filament\Resources\WarTicketResource\Pages;

use App\Filament\Resources\WarTicketResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWarTicket extends EditRecord
{
    protected static string $resource = WarTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
