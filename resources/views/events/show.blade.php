@extends('layouts.app')

@section('title', $event->title)

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="container mx-auto px-4 max-w-7xl">

            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-center justify-center">
                    <div class="flex items-center">
                        <div class="flex items-center text-brand-yellow relative">
                            <div class="rounded-full h-12 w-12 flex items-center justify-center border-2 border-brand-yellow bg-brand-yellow text-black font-bold"
                                id="step-1-circle">
                                1
                            </div>
                            <div
                                class="absolute top-14 left-1/2 transform -translate-x-1/2 whitespace-nowrap text-sm font-semibold">
                                Pilih Tiket
                            </div>
                        </div>
                        <div class="w-32 h-1 bg-gray-300" id="progress-line"></div>
                        <div class="flex items-center text-gray-400 relative">
                            <div class="rounded-full h-12 w-12 flex items-center justify-center border-2 border-gray-300 bg-white font-bold"
                                id="step-2-circle">
                                2
                            </div>
                            <div class="absolute top-14 left-1/2 transform -translate-x-1/2 whitespace-nowrap text-sm">
                                Data Pembeli
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Display Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded max-w-4xl mx-auto">
                    <div class="flex items-center mb-2">
                        <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                        <p class="font-bold text-red-700">Terdapat kesalahan:</p>
                    </div>
                    <ul class="list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded max-w-4xl mx-auto">
                    <div class="flex items-center">
                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                        <p class="text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- STEP 1: Ticket Selection -->
            <div id="step-1" class="grid grid-cols-1 lg:grid-cols-3 gap-8">

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
                                        <p class="font-semibold">{{ $event->event_date->format('d F Y') }}</p>
                                        @if ($event->event_end_date)
                                            <p class="text-xs text-gray-500">s/d
                                                {{ $event->event_end_date->format('d F Y') }}</p>
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

                        @if ($event->ticketTypes->count() > 0)
                            @foreach ($event->ticketTypes as $ticketType)
                                <div
                                    class="border border-gray-200 rounded-xl p-4 mb-4 hover:border-brand-yellow transition">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-slate-900">{{ $ticketType->name }}</h4>
                                            @if ($ticketType->description)
                                                <p class="text-xs text-gray-500 mt-1">{{ $ticketType->description }}</p>
                                            @endif

                                            @if ($ticketType->war_ticket)
                                                <span
                                                    class="inline-block mt-2 bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded">
                                                    <i class="fas fa-fire mr-1"></i> WAR TICKET!
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-right ml-4">
                                            @if ($ticketType->war_ticket)
                                                <p class="text-xs text-gray-400 line-through">
                                                    Rp {{ number_format($ticketType->price, 0, ',', '.') }}
                                                </p>
                                                <p class="font-bold text-lg text-red-600">
                                                    Rp {{ number_format($ticketType->current_price, 0, ',', '.') }}
                                                </p>
                                            @else
                                                <p class="font-bold text-lg text-slate-900">
                                                    Rp {{ number_format($ticketType->current_price, 0, ',', '.') }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    @php
                                        $availableQuota = $ticketType->war_ticket
                                            ? $ticketType->war_ticket->war_available_quota
                                            : $ticketType->available_quota;
                                    @endphp
                                    @if ($availableQuota !== null)
                                        <p class="text-xs text-gray-500 mb-3">
                                            <i class="fas fa-users mr-1"></i>
                                            Tersisa: <strong>{{ $availableQuota }}</strong> tiket
                                        </p>
                                    @endif

                                    <div class="flex items-center justify-between bg-gray-50 rounded-lg p-2">
                                        <button type="button" onclick="updateQuantity({{ $ticketType->id }}, -1)"
                                            class="w-10 h-10 rounded-lg bg-white border border-gray-300 hover:bg-gray-100 transition font-bold">
                                            <i class="fas fa-minus text-sm"></i>
                                        </button>
                                        <span class="font-bold text-xl" id="qty-{{ $ticketType->id }}">0</span>
                                        <button type="button" onclick="updateQuantity({{ $ticketType->id }}, 1)"
                                            class="w-10 h-10 rounded-lg bg-white border border-gray-300 hover:bg-gray-100 transition font-bold">
                                            <i class="fas fa-plus text-sm"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach

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

                            <button type="button" onclick="goToStep2()" id="btn-next-step"
                                class="w-full bg-gray-300 text-gray-500 font-bold py-4 rounded-xl cursor-not-allowed transition"
                                disabled>
                                <i class="fas fa-arrow-right mr-2"></i> Lanjut ke Data Pembeli
                            </button>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-ticket-alt text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500">Tiket belum tersedia</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- STEP 2: Customer Data Form -->
            <div id="step-2" class="hidden">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- LEFT: Form -->
                    <div class="lg:col-span-2">
                        <div class="bg-white rounded-2xl shadow-lg p-8">
                            <div class="flex items-center justify-between mb-6">
                                <h2 class="text-2xl font-bold text-slate-900">
                                    <i class="fas fa-user-circle text-brand-yellow mr-2"></i> Data Pembeli
                                </h2>
                                <button type="button" onclick="goToStep1()"
                                    class="text-sm text-blue-600 hover:text-blue-800 font-semibold">
                                    <i class="fas fa-arrow-left mr-1"></i> Kembali
                                </button>
                            </div>

                            <form action="{{ route('checkout.process', $event) }}" method="POST" id="checkout-form">
                                @csrf

                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                                        Nama Lengkap <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="full_name" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow"
                                        placeholder="Masukkan nama lengkap"
                                        value="{{ old('full_name', Auth::user()->name ?? '') }}">
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                                        Email <span class="text-red-500">*</span>
                                    </label>
                                    <input type="email" name="email" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow"
                                        placeholder="email@example.com"
                                        value="{{ old('email', Auth::user()->email ?? '') }}">
                                    <p class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-info-circle mr-1"></i>Tiket akan dikirim ke email ini
                                    </p>
                                </div>

                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                                        Nomor Telepon <span class="text-red-500">*</span>
                                    </label>
                                    <input type="tel" name="phone_number" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow"
                                        placeholder="08xxxxxxxxxx" value="{{ old('phone_number') }}">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                    <div>
                                        <label class="block text-sm font-semibold text-slate-900 mb-2">
                                            Jenis Identitas <span class="text-red-500">*</span>
                                        </label>
                                        <select name="identity_type" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow">
                                            <option value="">Pilih Identitas</option>
                                            <option value="ktp">KTP</option>
                                            <option value="sim">SIM</option>
                                            <option value="passport">Passport</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-slate-900 mb-2">
                                            Nomor Identitas <span class="text-red-500">*</span>
                                        </label>
                                        <input type="text" name="identity_number" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow"
                                            placeholder="Nomor identitas" value="{{ old('identity_number') }}">
                                    </div>
                                </div>

                                <!-- Hidden ticket data -->
                                <div id="hidden-ticket-inputs"></div>

                                <div class="mb-6">
                                    <label class="flex items-start cursor-pointer">
                                        <input type="checkbox" name="agree_terms" required
                                            class="mt-1 rounded text-brand-yellow focus:ring-brand-yellow">
                                        <span class="ml-3 text-sm text-gray-600">
                                            Saya setuju dengan <a href="#"
                                                class="text-blue-600 hover:underline">syarat dan ketentuan</a> yang berlaku
                                        </span>
                                    </label>
                                </div>

                                <button type="submit" id="pay-button"
                                    class="w-full bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg">
                                    <i class="fas fa-credit-card mr-2"></i> Lanjut ke Pembayaran
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- RIGHT: Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-28">
                            <h3 class="font-bold text-xl mb-6 text-slate-900">
                                <i class="fas fa-receipt text-brand-yellow mr-2"></i> Ringkasan Pesanan
                            </h3>

                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <h4 class="font-bold text-lg text-slate-900 mb-2">{{ $event->name }}</h4>
                                <p class="text-sm text-gray-600">
                                    <i class="far fa-calendar mr-1"></i>
                                    {{ $event->event_date->format('d F Y') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    {{ $event->venue }}
                                </p>
                            </div>

                            <div class="space-y-3 mb-6 pb-6 border-b border-gray-200" id="summary-items"></div>

                            <div class="flex justify-between items-center mb-6">
                                <span class="text-lg font-bold text-slate-900">Total Pembayaran</span>
                                <span class="text-2xl font-bold text-brand-yellow" id="summary-total">Rp 0</span>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <p class="text-xs text-gray-600 mb-2 font-semibold">
                                    <i class="fas fa-info-circle text-blue-500 mr-1"></i> Metode Pembayaran
                                </p>
                                <p class="text-xs text-gray-500">
                                    Pembayaran menggunakan Midtrans dengan berbagai pilihan metode
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const ticketPrices = {
            @foreach ($event->ticketTypes as $ticketType)
                {{ $ticketType->id }}: {{ $ticketType->current_price }},
            @endforeach
        };

        const ticketNames = {
            @foreach ($event->ticketTypes as $ticketType)
                {{ $ticketType->id }}: "{{ $ticketType->name }}",
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

            Object.keys(quantities).forEach(ticketId => {
                const qty = quantities[ticketId];
                document.getElementById('qty-' + ticketId).textContent = qty;
                totalTickets += qty;
                totalPrice += qty * ticketPrices[ticketId];
            });

            document.getElementById('total-tickets').textContent = totalTickets;
            document.getElementById('total-price').textContent = formatRupiah(totalPrice);

            const btn = document.getElementById('btn-next-step');
            if (totalTickets > 0) {
                btn.disabled = false;
                btn.className =
                    "w-full bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 rounded-xl transition transform hover:scale-105 shadow-lg";
            } else {
                btn.disabled = true;
                btn.className = "w-full bg-gray-300 text-gray-500 font-bold py-4 rounded-xl cursor-not-allowed";
            }
        }

        function goToStep2() {
            // Validate at least 1 ticket
            const totalTickets = Object.values(quantities).reduce((a, b) => a + b, 0);
            if (totalTickets === 0) {
                alert('Silakan pilih minimal 1 tiket');
                return;
            }

            // Update progress
            document.getElementById('step-1-circle').classList.remove('bg-brand-yellow', 'text-black');
            document.getElementById('step-1-circle').classList.add('bg-white', 'text-brand-yellow');
            document.getElementById('step-2-circle').classList.remove('bg-white', 'text-gray-400', 'border-gray-300');
            document.getElementById('step-2-circle').classList.add('bg-brand-yellow', 'text-black', 'border-brand-yellow');
            document.getElementById('progress-line').classList.remove('bg-gray-300');
            document.getElementById('progress-line').classList.add('bg-brand-yellow');

            // Hide step 1, show step 2
            document.getElementById('step-1').classList.add('hidden');
            document.getElementById('step-2').classList.remove('hidden');

            // Populate summary
            updateOrderSummary();

            // Add hidden inputs for tickets
            addHiddenInputs();

            // Scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function goToStep1() {
            // Update progress
            document.getElementById('step-1-circle').classList.add('bg-brand-yellow', 'text-black');
            document.getElementById('step-1-circle').classList.remove('bg-white', 'text-brand-yellow');
            document.getElementById('step-2-circle').classList.add('bg-white', 'text-gray-400', 'border-gray-300');
            document.getElementById('step-2-circle').classList.remove('bg-brand-yellow', 'text-black',
                'border-brand-yellow');
            document.getElementById('progress-line').classList.add('bg-gray-300');
            document.getElementById('progress-line').classList.remove('bg-brand-yellow');

            // Show step 1, hide step 2
            document.getElementById('step-1').classList.remove('hidden');
            document.getElementById('step-2').classList.add('hidden');

            // Scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function updateOrderSummary() {
            const summaryContainer = document.getElementById('summary-items');
            summaryContainer.innerHTML = '';

            let totalAmount = 0;
            let index = 0;

            Object.keys(quantities).forEach(ticketId => {
                const qty = quantities[ticketId];
                if (qty > 0) {
                    const price = ticketPrices[ticketId];
                    const subtotal = qty * price;
                    totalAmount += subtotal;

                    const itemHtml = `
                        <div class="flex justify-between text-sm">
                            <div>
                                <p class="text-gray-600 font-medium">${ticketNames[ticketId]}</p>
                                <p class="text-xs text-gray-500">${qty} x Rp ${formatRupiah(price)}</p>
                            </div>
                            <p class="font-semibold text-slate-900">Rp ${formatRupiah(subtotal)}</p>
                        </div>
                    `;
                    summaryContainer.innerHTML += itemHtml;
                }
            });

            // Service fee
            const serviceFee = 5000;
            totalAmount += serviceFee;

            summaryContainer.innerHTML += `
                <div class="flex justify-between text-sm">
                    <p class="text-gray-600">Biaya Layanan</p>
                    <p class="font-semibold text-slate-900">Rp ${formatRupiah(serviceFee)}</p>
                </div>
            `;

            document.getElementById('summary-total').textContent = 'Rp ' + formatRupiah(totalAmount);
        }

        function addHiddenInputs() {
            const container = document.getElementById('hidden-ticket-inputs');
            container.innerHTML = '';

            let index = 0;
            Object.keys(quantities).forEach(ticketId => {
                const qty = quantities[ticketId];
                if (qty > 0) {
                    container.innerHTML += `
                        <input type="hidden" name="items[${index}][ticket_type_id]" value="${ticketId}">
                        <input type="hidden" name="items[${index}][quantity]" value="${qty}">
                    `;
                    index++;
                }
            });
        }

        // Form submission
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('checkout-form');
            if (form) {
                form.addEventListener('submit', function() {
                    const btn = document.getElementById('pay-button');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
                });
            }
            updateUI();
        });
    </script>
@endpush
