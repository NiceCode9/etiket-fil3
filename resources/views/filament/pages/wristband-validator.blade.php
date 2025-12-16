<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::card>
            <div class="space-y-4">
                <div>
                    <x-filament::input.wrapper>
                        <x-filament::input type="text" wire:model="qrCode" placeholder="Scan or enter QR Code"
                            wire:keydown.enter="scan" />
                    </x-filament::input.wrapper>
                </div>

                <div class="flex gap-2">
                    <x-filament::button wire:click="scan">
                        Scan QR Code
                    </x-filament::button>

                    <x-filament::button color="gray" wire:click="resetScan">
                        Reset
                    </x-filament::button>
                </div>
            </div>
        </x-filament::card>

        @if ($ticket)
            <x-filament::card>
                <div class="space-y-4">
                    <h3 class="text-lg font-bold">Ticket Information</h3>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Ticket Number</p>
                            <p class="font-semibold">{{ $ticket->ticket_number }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <x-filament::badge :color="$ticket->getStatusBadge()['color']">
                                {{ $ticket->getStatusBadge()['label'] }}
                            </x-filament::badge>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Customer Name</p>
                            <p class="font-semibold">{{ $ticket->customer->full_name }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Event</p>
                            <p class="font-semibold">{{ $ticket->event->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-gray-500">Ticket Type</p>
                            <p class="font-semibold">{{ $ticket->ticketType->name }}</p>
                        </div>

                        @if ($ticket->wristband_code)
                            <div class="col-span-2">
                                <p class="text-sm text-gray-500">Wristband Code</p>
                                <p class="text-2xl font-bold text-green-600">{{ $ticket->wristband_code }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </x-filament::card>
        @endif
    </div>
</x-filament-panels::page>
