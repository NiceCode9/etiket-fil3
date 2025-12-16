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
                            <img src="{{ $event->image_url ?? 'https://images.unsplash.com/photo-1533174072545-e8d4aa97edf9?auto=format&fit=crop&w=1200&q=80' }}"
                                class="w-full h-full object-cover" alt="{{ $event->title }}" id="detail-image">
                        </div>

                        <!-- Event Info -->
                        <div class="p-8">
                            <div class="flex items-center gap-2 mb-4">
                                <span
                                    class="bg-purple-100 text-purple-700 text-xs font-bold px-3 py-1 rounded-full">{{ ucfirst($event->category) }}</span>
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">
                                    <i class="fas fa-ticket-alt mr-1"></i> Tersedia
                                </span>
                            </div>

                            <h1 class="text-3xl font-bold text-slate-900 mb-4" id="detail-title">{{ $event->title }}</h1>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="flex items-center gap-3 text-gray-600">
                                    <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                                        <i class="far fa-calendar text-purple-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Tanggal</p>
                                        <p class="font-semibold" id="detail-date">
                                            {{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 text-gray-600">
                                    <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-blue-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Lokasi</p>
                                        <p class="font-semibold" id="detail-location">{{ $event->location }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-gray-200 pt-6">
                                <h3 class="font-bold text-lg mb-3">Tentang Event</h3>
                                <p class="text-gray-600 leading-relaxed">
                                    {{ $event->description ?? 'Deskripsi event akan segera hadir. Jangan lewatkan kesempatan untuk menghadiri event spektakuler ini!' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: Ticket Selection & Checkout -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28">
                        <h3 class="font-bold text-xl mb-6 text-slate-900">Pilih Tiket</h3>

                        <form action="{{ route('checkout.store') }}" method="POST" id="ticket-form">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->id }}">

                            <!-- Regular Ticket -->
                            <div class="border border-gray-200 rounded-xl p-4 mb-4 hover:border-brand-yellow transition">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-bold text-slate-900">Tiket Regular</h4>
                                        <p class="text-xs text-gray-500 mt-1">Akses umum ke venue</p>
                                    </div>
                                    <p class="font-bold text-lg text-slate-900" id="price-regular">
                                        Rp {{ number_format($event->price_regular, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-2">
                                    <button type="button" onclick="updateQuantity('regular', -1)"
                                        class="w-10 h-10 rounded-lg bg-white border border-gray-300 hover:bg-gray-100 transition font-bold text-lg">
                                        <i class="fas fa-minus text-sm"></i>
                                    </button>
                                    <span class="font-bold text-xl" id="qty-regular">0</span>
                                    <input type="hidden" name="qty_regular" id="input-qty-regular" value="0">
                                    <button type="button" onclick="updateQuantity('regular', 1)"
                                        class="w-10 h-10 rounded-lg bg-white border border-gray-300 hover:bg-gray-100 transition font-bold text-lg">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- VIP Ticket -->
                            <div class="border border-gray-200 rounded-xl p-4 mb-6 hover:border-brand-yellow transition">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-bold text-slate-900">Tiket VIP</h4>
                                        <p class="text-xs text-gray-500 mt-1">Akses VIP dengan fasilitas eksklusif</p>
                                    </div>
                                    <p class="font-bold text-lg text-slate-900" id="price-vip">
                                        Rp {{ number_format($event->price_vip, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="flex items-center justify-between bg-gray-50 rounded-lg p-2">
                                    <button type="button" onclick="updateQuantity('vip', -1)"
                                        class="w-10 h-10 rounded-lg bg-white border border-gray-300 hover:bg-gray-100 transition font-bold text-lg">
                                        <i class="fas fa-minus text-sm"></i>
                                    </button>
                                    <span class="font-bold text-xl" id="qty-vip">0</span>
                                    <input type="hidden" name="qty_vip" id="input-qty-vip" value="0">
                                    <button type="button" onclick="updateQuantity('vip', 1)"
                                        class="w-10 h-10 rounded-lg bg-white border border-gray-300 hover:bg-gray-100 transition font-bold text-lg">
                                        <i class="fas fa-plus text-sm"></i>
                                    </button>
                                </div>
                            </div>

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
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const priceRegular = {{ $event->price_regular }};
        const priceVip = {{ $event->price_vip }};
        let quantities = {
            regular: 0,
            vip: 0
        };

        function updateQuantity(type, change) {
            quantities[type] = Math.max(0, quantities[type] + change);
            updateUI();
        }

        function updateUI() {
            // Update displayed quantities
            document.getElementById('qty-regular').textContent = quantities.regular;
            document.getElementById('qty-vip').textContent = quantities.vip;

            // Update hidden inputs
            document.getElementById('input-qty-regular').value = quantities.regular;
            document.getElementById('input-qty-vip').value = quantities.vip;

            // Calculate totals
            const totalTickets = quantities.regular + quantities.vip;
            const totalPrice = (quantities.regular * priceRegular) + (quantities.vip * priceVip);

            // Update total display
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
