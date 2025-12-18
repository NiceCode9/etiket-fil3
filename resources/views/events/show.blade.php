@extends('layouts.app')

@section('title', $event->title)

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- LEFT: Event Detail -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <!-- Event Image -->
                        <div class="relative h-96 overflow-hidden">
                            <img src="{{ $event->poster_image ? asset('storage/' . $event->poster_image) : asset('images/default-event.jpg') }}"
                                class="w-full h-full object-cover" alt="{{ $event->name }}">
                        </div>

                        <!-- Event Info -->
                        <div class="p-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                                    <i class="fas fa-ticket-alt mr-1"></i> Tersedia
                                </span>
                                @if ($event->status === 'published')
                                    <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">
                                        Published
                                    </span>
                                @endif
                            </div>

                            <h1 class="text-3xl font-bold text-slate-900 mb-4">{{ $event->name }}</h1>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="flex items-center gap-3 text-gray-600">
                                    <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                                        <i class="far fa-calendar text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Tanggal</p>
                                        <p class="font-semibold">
                                            {{ $event->event_date->format('d F Y') }}
                                        </p>
                                        @if ($event->event_end_date)
                                            <p class="text-xs text-gray-500">
                                                s/d {{ $event->event_end_date->format('d F Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 text-gray-600">
                                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Lokasi</p>
                                        <p class="font-semibold">{{ $event->venue }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="font-bold text-lg mb-3">Tentang Event</h3>
                                <div class="text-gray-600 leading-relaxed prose max-w-none">
                                    {!! nl2br(e($event->description)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Ticket Selection -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28">
                        <h3 class="font-bold text-xl mb-6 text-slate-900">
                            <i class="fas fa-ticket-alt text-brand-yellow mr-2"></i> Pilih Tiket
                        </h3>

                        <form action="{{ route('checkout', $event) }}" method="GET" id="ticket-form">
                            @if ($event->ticketTypes->count() > 0)
                                @foreach ($event->ticketTypes as $ticketType)
                                    <div class="border border-gray-200 rounded-xl p-4 mb-4 hover:border-brand-yellow transition"
                                        data-ticket-id="{{ $ticketType->id }}">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="flex-1">
                                                <h4 class="font-bold text-slate-900">{{ $ticketType->name }}</h4>
                                                @if ($ticketType->description)
                                                    <p class="text-xs text-gray-500 mt-1">{{ $ticketType->description }}</p>
                                                @endif

                                                {{-- War Ticket Badge --}}
                                                @if ($ticketType->war_ticket)
                                                    <span
                                                        class="inline-block mt-2 bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded">
                                                        <i class="fas fa-fire mr-1"></i> WAR TICKET!
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="text-right ml-4">
                                                @if ($ticketType->war_ticket)
                                                    {{-- War Ticket Active --}}
                                                    <p class="text-xs text-gray-400 line-through">
                                                        Rp {{ number_format($ticketType->price, 0, ',', '.') }}
                                                    </p>
                                                    <p class="font-bold text-lg text-red-600">
                                                        Rp {{ number_format($ticketType->current_price, 0, ',', '.') }}
                                                    </p>
                                                    <p class="text-xs text-red-600 mt-1">
                                                        Hemat Rp
                                                        {{ number_format($ticketType->price - $ticketType->current_price, 0, ',', '.') }}
                                                    </p>
                                                @else
                                                    {{-- Normal Price --}}
                                                    <p class="font-bold text-lg text-slate-900">
                                                        Rp {{ number_format($ticketType->current_price, 0, ',', '.') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Stock Info --}}
                                        @php
                                            $availableQuota = $ticketType->war_ticket
                                                ? $ticketType->war_ticket->war_available_quota
                                                : $ticketType->available_quota;
                                        @endphp
                                        @if ($availableQuota !== null)
                                            <p class="text-xs text-gray-500 mb-3">
                                                <i class="fas fa-users mr-1"></i>
                                                Tersisa: <strong>{{ $availableQuota }}</strong> tiket
                                                @if ($ticketType->war_ticket)
                                                    <span class="text-red-600 font-semibold">(WAR)</span>
                                                @endif
                                            </p>
                                        @endif

                                        {{-- Quantity Selector --}}
                                        <div class="flex items-center justify-between bg-gray-50 rounded-lg p-2">
                                            <button type="button" onclick="updateQuantity({{ $ticketType->id }}, -1)"
                                                class="w-10 h-10 rounded-lg bg-white border border-gray-300 hover:bg-gray-100 transition font-bold text-lg">
                                                <i class="fas fa-minus text-sm"></i>
                                            </button>
                                            <span class="font-bold text-xl" id="qty-{{ $ticketType->id }}">0</span>
                                            <input type="hidden" name="items[{{ $ticketType->id }}][ticket_type_id]"
                                                value="{{ $ticketType->id }}">
                                            <input type="hidden" name="items[{{ $ticketType->id }}][quantity]"
                                                id="input-qty-{{ $ticketType->id }}" value="0">
                                            <button type="button" onclick="updateQuantity({{ $ticketType->id }}, 1)"
                                                class="w-10 h-10 rounded-lg bg-white border border-gray-300 hover:bg-gray-100 transition font-bold text-lg">
                                                <i class="fas fa-plus text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach

                                <!-- Total -->
                                <div class="border-t border-gray-200 pt-4 mb-6">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-gray-600">Total Tiket</span>
                                        <span class="font-semibold" id="total-tickets">0</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-slate-900">Total Bayar</span>
                                        <span class="text-2xl font-bold text-brand-yellow" id="total-price">Rp 0</span>
                                    </div>
                                </div>

                                <!-- Checkout Button -->
                                <button type="submit" id="btn-checkout"
                                    class="w-full bg-gray-300 text-gray-500 font-bold py-4 rounded-xl cursor-not-allowed transition"
                                    disabled>
                                    <i class="fas fa-shopping-cart mr-2"></i> Lanjut ke Checkout
                                </button>
                            @else
                                <div class="text-center py-8">
                                    <i class="fas fa-ticket-alt text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500">Tiket belum tersedia</p>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Ticket prices and quantities
        const ticketPrices = {
            @foreach ($event->ticketTypes as $ticketType)
                {{ $ticketType->id }}: {{ $ticketType->current_price }},
            @endforeach
        };

        let quantities = {
            @foreach ($event->ticketTypes as $ticketType)
                {{ $ticketType->id }}: 0,
            @endforeach
        };

        function updateQuantity(ticketId, change) {
            quantities[ticketId] = Math.max(0, quantities[ticketId] + change);
            updateUI();
        }

        function updateUI() {
            let totalTickets = 0;
            let totalPrice = 0;

            // Update each ticket type
            Object.keys(quantities).forEach(ticketId => {
                const qty = quantities[ticketId];
                document.getElementById('qty-' + ticketId).textContent = qty;
                document.getElementById('input-qty-' + ticketId).value = qty;

                totalTickets += qty;
                totalPrice += qty * ticketPrices[ticketId];
            });

            // Update totals
            document.getElementById('total-tickets').textContent = totalTickets;
            document.getElementById('total-price').textContent = formatRupiah(totalPrice);

            // Enable/disable checkout button
            const btn = document.getElementById('btn-checkout');
            if (totalTickets > 0) {
                btn.disabled = false;
                btn.className =
                    "w-full bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg";
            } else {
                btn.disabled = true;
                btn.className = "w-full bg-gray-300 text-gray-500 font-bold py-4 rounded-xl cursor-not-allowed";
            }
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            updateUI();
        });
    </script>
@endpush
