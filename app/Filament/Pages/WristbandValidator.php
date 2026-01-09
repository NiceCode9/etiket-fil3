<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use App\Models\Scan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class WristbandValidator extends Page
{
    protected static string $view = 'filament.pages.wristband-validator';
    protected static ?string $navigationIcon = 'heroicon-o-check-badge';
    protected static ?string $navigationGroup = 'Scanning';
    protected static ?int $navigationSort = 2;

    public $wristbandCode = '';
    public $ticket = null;

    public function validateWristband()
    {
        try {
            $user = auth()->user();
            
            if (empty($this->wristbandCode)) {
                Notification::make()
                    ->title('Kode Wristband Kosong')
                    ->body('Silakan scan atau masukkan kode wristband')
                    ->warning()
                    ->send();
                return;
            }
            
            // Find ticket by wristband code with tenant scoping
            $query = Ticket::where('wristband_code', $this->wristbandCode)
                ->with(['customer', 'event', 'ticketType']);
            
            // Apply tenant scope for non-super-admin users
            if (!$user->hasRole('super_admin') && $user->tenant_id) {
                $query->where('tenant_id', $user->tenant_id);
            }
            
            $this->ticket = $query->firstOrFail();

            if (!$this->ticket->canValidateWristband()) {
                Notification::make()
                    ->title('Wristband Tidak Dapat Divalidasi')
                    ->body('Status tiket: ' . $this->ticket->status)
                    ->danger()
                    ->send();
                return;
            }

            // Update ticket status
            $this->ticket->update([
                'status' => 'used',
                'wristband_validated_at' => now(),
                'wristband_validated_by' => $user->id,
            ]);

            // Create scan record
            Scan::create([
                'user_id' => $user->id,
                'ticket_id' => $this->ticket->id,
                'tenant_id' => $this->ticket->tenant_id,
                'scan_type' => 'wristband_validation',
                'scanned_at' => now(),
            ]);

            Notification::make()
                ->title('Wristband Berhasil Divalidasi')
                ->body('Tiket: ' . $this->ticket->ticket_number)
                ->success()
                ->send();

            $this->ticket->refresh();
            
            // Dispatch browser event to stop camera
            $this->dispatch('validate-success');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Notification::make()
                ->title('Tiket Tidak Ditemukan')
                ->body('Kode wristband tidak valid atau tidak ditemukan')
                ->danger()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function resetScan()
    {
        $this->reset(['wristbandCode', 'ticket']);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->hasRole('wristband_validator'));
    }
}
