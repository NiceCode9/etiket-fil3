<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class WristbandValidator extends Page
{
    protected static string $view = 'filament.pages.wristband-validator';
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Scanning';
    protected static ?int $navigationSort = 1;

    public $qrCode = '';
    public $ticket = null;

    public function scan()
    {
        try {
            $this->ticket = Ticket::where('qr_code', $this->qrCode)
                ->with(['customer', 'event', 'ticketType'])
                ->firstOrFail();

            if (!$this->ticket->canScanForWristband()) {
                Notification::make()
                    ->title('Ticket Already Scanned')
                    ->danger()
                    ->send();
                return;
            }

            // Generate wristband code
            $wristbandCode = 'WB-' . strtoupper(substr(uniqid(), -10));

            $this->ticket->update([
                'status' => 'scanned_for_wristband',
                'wristband_code' => $wristbandCode,
                'scanned_for_wristband_at' => now(),
                'scanned_for_wristband_by' => auth()->id(),
            ]);

            // Log scan
            $this->ticket->scanLogs()->create([
                'scan_type' => 'qr_for_wristband',
                'scanned_by' => auth()->id(),
                'scanned_at' => now(),
                'status' => 'success',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            Notification::make()
                ->title('QR Code Scanned Successfully')
                ->success()
                ->send();

            $this->ticket->refresh();
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
        $this->reset(['qrCode', 'ticket']);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'qr_scanner']);
    }
}
