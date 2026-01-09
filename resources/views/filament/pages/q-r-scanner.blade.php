<x-filament-panels::page>
    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    @endpush

    <div class="space-y-4">
        <!-- Camera Scanner Section -->
        <x-filament::card>
            <div class="space-y-4">
                <div class="text-center">
                    <h2 class="text-xl font-bold">QR Code Scanner</h2>
                    <p class="text-sm text-gray-600">Arahkan kamera ke QR Code tiket</p>
                </div>

                <!-- Camera Container -->
                <div class="relative w-full max-w-md mx-auto">
                    <div id="qr-reader" class="w-full bg-black rounded-lg overflow-hidden" style="min-height: 300px; max-height: 500px;">
                        <div id="qr-reader-results" class="hidden"></div>
                    </div>
                    <!-- Scanning Indicator -->
                    <div id="scanning-indicator" class="hidden absolute top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-4 py-2 rounded-full text-sm font-semibold">
                        <span class="animate-pulse">🔍 Memindai...</span>
                    </div>
                </div>

                <!-- Manual Input Fallback -->
                <div class="border-t pt-4">
                    <p class="text-sm text-gray-600 mb-2 text-center">Atau masukkan QR Code secara manual:</p>
                    <x-filament::input.wrapper>
                        <x-filament::input 
                            type="text" 
                            wire:model="qrCode" 
                            placeholder="Masukkan QR Code"
                            wire:keydown.enter="scan" 
                            id="manual-qr-input"
                        />
                    </x-filament::input.wrapper>
                    <div class="flex gap-2 mt-2 justify-center">
                        <x-filament::button wire:click="scan" size="sm">
                            Scan Manual
                        </x-filament::button>
                        <x-filament::button color="gray" wire:click="resetScan" size="sm">
                            Reset
                        </x-filament::button>
                    </div>
                </div>

                <!-- Camera Controls -->
                <div class="flex gap-2 justify-center">
                    <x-filament::button 
                        color="success" 
                        id="start-camera-btn"
                        onclick="startQRScanner()"
                    >
                        <x-heroicon-o-camera class="w-4 h-4 mr-2" />
                        Mulai Kamera
                    </x-filament::button>
                    <x-filament::button 
                        color="danger" 
                        id="stop-camera-btn"
                        onclick="stopQRScanner()"
                        class="hidden"
                    >
                        <x-heroicon-o-x-mark class="w-4 h-4 mr-2" />
                        Stop Kamera
                    </x-filament::button>
                </div>
            </div>
        </x-filament::card>

        <!-- Ticket Information -->
        @if ($ticket)
            <x-filament::card>
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-center">Informasi Tiket</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Nomor Tiket</p>
                            <p class="font-semibold text-lg">{{ $ticket->ticket_number }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <x-filament::badge :color="$ticket->getStatusBadge()['color']" class="text-sm">
                                {{ $ticket->getStatusBadge()['label'] }}
                            </x-filament::badge>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Nama Customer</p>
                            <p class="font-semibold">{{ $ticket->customer->full_name }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Event</p>
                            <p class="font-semibold">{{ $ticket->event->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Tipe Tiket</p>
                            <p class="font-semibold">{{ $ticket->ticketType->name }}</p>
                        </div>

                        @if ($ticket->wristband_code)
                            <div class="col-span-1 md:col-span-2">
                                <p class="text-sm text-gray-500 mb-2">Kode Wristband</p>
                                <p class="text-3xl font-bold text-green-600 text-center">{{ $ticket->wristband_code }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </x-filament::card>
        @endif
    </div>

    <script>
        let html5QrcodeScanner = null;
        let isScanning = false;

        function startQRScanner() {
            if (isScanning) return;

            const startBtn = document.getElementById('start-camera-btn');
            const stopBtn = document.getElementById('stop-camera-btn');
            const readerDiv = document.getElementById('qr-reader');

            startBtn.classList.add('hidden');
            stopBtn.classList.remove('hidden');

            html5QrcodeScanner = new Html5Qrcode("qr-reader");
            
            // Show scanning indicator
            document.getElementById('scanning-indicator').classList.remove('hidden');

            html5QrcodeScanner.start(
                { facingMode: "environment" }, // Use back camera
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0,
                    disableFlip: false
                },
                (decodedText, decodedResult) => {
                    // Success callback
                    document.getElementById('scanning-indicator').classList.add('hidden');
                    handleQRCode(decodedText);
                },
                (errorMessage) => {
                    // Error callback - ignore
                }
            ).catch((err) => {
                console.error("Unable to start scanning", err);
                document.getElementById('scanning-indicator').classList.add('hidden');
                alert("Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.");
                stopQRScanner();
            });

            isScanning = true;
        }

        function stopQRScanner() {
            if (html5QrcodeScanner) {
                html5QrcodeScanner.stop().then(() => {
                    html5QrcodeScanner.clear();
                    html5QrcodeScanner = null;
                }).catch((err) => {
                    console.error("Error stopping scanner", err);
                });
            }

            const startBtn = document.getElementById('start-camera-btn');
            const stopBtn = document.getElementById('stop-camera-btn');
            const indicator = document.getElementById('scanning-indicator');
            
            startBtn.classList.remove('hidden');
            stopBtn.classList.add('hidden');
            if (indicator) indicator.classList.add('hidden');
            isScanning = false;
        }

        function handleQRCode(qrCode) {
            // Stop scanning first
            stopQRScanner();
            
            // Set the QR code value
            @this.set('qrCode', qrCode);
            
            // Automatically trigger scan
            @this.call('scan');
        }

        // Listen for scan success event
        window.addEventListener('scan-success', () => {
            // Camera already stopped, just reset after delay
            setTimeout(() => {
                @this.call('resetScan');
            }, 3000);
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            stopQRScanner();
        });

        // Auto-start camera on mobile devices
        if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
            window.addEventListener('load', () => {
                setTimeout(() => {
                    startQRScanner();
                }, 500);
            });
        }
    </script>
</x-filament-panels::page>
