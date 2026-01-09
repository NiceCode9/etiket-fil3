<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        
        // Auto-set tenant_id for tenant_admin users
        if ($user && $user->hasRole('tenant_admin') && $user->tenant_id) {
            $data['tenant_id'] = $user->tenant_id;
        }

        return $data;
    }
}
