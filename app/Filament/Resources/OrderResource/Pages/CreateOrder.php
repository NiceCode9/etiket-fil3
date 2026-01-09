<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Event;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        
        // Auto-set tenant_id for tenant_admin users
        if ($user && $user->hasRole('tenant_admin') && $user->tenant_id) {
            $data['tenant_id'] = $user->tenant_id;
        } elseif (isset($data['event_id'])) {
            // Auto-set tenant_id from event
            $event = Event::find($data['event_id']);
            if ($event && $event->tenant_id) {
                $data['tenant_id'] = $event->tenant_id;
            }
        }

        return $data;
    }
}
