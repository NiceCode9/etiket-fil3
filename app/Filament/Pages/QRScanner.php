<?php

namespace App\Filament\Pages;

use App\Models\Ticket;
use App\Models\Scan;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class QRScanner extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationGroup = 'Scanning';
    protected static string $view = 'filament.pages.q-r-scanner';
    protected static ?int $navigationSort = 1;

    public $qrCode = '';
    public $ticket = null;

    public function scan()
    {
        try {
            $user = auth()->user();
            
            if (empty($this->qrCode)) {
                Notification::make()
                    ->title('QR Code Kosong')
                    ->body('Silakan scan atau masukkan QR Code')
                    ->warning()
                    ->send();
                return;
            }
            
            // Find ticket with tenant scoping
            $query = Ticket::where('qr_code', $this->qrCode)
                ->with(['customer', 'event', 'ticketType']);
            
            // Apply tenant scope for non-super-admin users
            if (!$user->hasRole('super_admin') && $user->tenant_id) {
                $query->where('tenant_id', $user->tenant_id);
            }
            
            $this->ticket = $query->firstOrFail();

            if (!$this->ticket->canScanForWristband()) {
                Notification::make()
                    ->title('Tiket Sudah Di-scan')
                    ->body('Tiket ini sudah pernah di-scan sebelumnya')
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
                'scanned_for_wristband_by' => $user->id,
            ]);

            // Create scan record
            Scan::create([
                'user_id' => $user->id,
                'ticket_id' => $this->ticket->id,
                'tenant_id' => $this->ticket->tenant_id,
                'scan_type' => 'qr_scan',
                'scanned_at' => now(),
            ]);

            Notification::make()
                ->title('QR Code Berhasil Di-scan')
                ->body('Kode Wristband: ' . $wristbandCode)
                ->success()
                ->send();

            $this->ticket->refresh();
            
            // Dispatch browser event to stop camera
            $this->dispatch('scan-success');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Notification::make()
                ->title('Tiket Tidak Ditemukan')
                ->body('QR Code tidak valid atau tidak ditemukan')
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
        $this->reset(['qrCode', 'ticket']);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->hasRole('qr_scanner'));
    }
}
