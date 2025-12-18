@extends('layouts.app')

@section('title', 'Checkout - ' . $event->title)

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="container mx-auto px-4 max-w-6xl">

            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-sm mb-8 text-gray-600">
                <a href="{{ route('events.index') }}" class="hover:text-brand-yellow transition">Events</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="{{ route('events.show', $event->slug) }}" class="hover:text-brand-yellow transition">
                    {{ Str::limit($event->name, 30) }}
                </a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-slate-900 font-semibold">Checkout</span>
            </div>

            {{-- Display Errors --}}
            @if ($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
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

            {{-- Display Success Message --}}
            @if (session('error'))
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded">
                    <div class="flex items-center">
                        <i class="fas fa-times-circle text-red-500 mr-2"></i>
                        <p class="text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- LEFT: Form Pembeli -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-slate-900 mb-6">
                            <i class="fas fa-user-circle text-brand-yellow mr-2"></i> Data Pembeli
                        </h2>

                        <form action="{{ route('checkout.process', $event) }}" method="POST" id="checkout-form">
                            @csrf

                            <!-- Nama Lengkap -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="full_name" id="full_name" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow @error('full_name') border-red-500 @enderror"
                                    placeholder="Masukkan nama lengkap"
                                    value="{{ old('full_name', Auth::user()->name ?? '') }}">
                                @error('full_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="email" id="email" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow @error('email') border-red-500 @enderror"
                                    placeholder="email@example.com" value="{{ old('email', Auth::user()->email ?? '') }}">
                                @error('email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>Tiket akan dikirim ke email ini
                                </p>
                            </div>

                            <!-- Nomor Telepon -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" name="phone_number" id="phone_number" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow @error('phone_number') border-red-500 @enderror"
                                    placeholder="08xxxxxxxxxx" value="{{ old('phone_number') }}">
                                @error('phone_number')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Identity Type & Number -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                                        Jenis Identitas <span class="text-red-500">*</span>
                                    </label>
                                    <select name="identity_type" id="identity_type" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow @error('identity_type') border-red-500 @enderror">
                                        <option value="">Pilih Identitas</option>
                                        <option value="ktp" {{ old('identity_type') == 'ktp' ? 'selected' : '' }}>KTP
                                        </option>
                                        <option value="sim" {{ old('identity_type') == 'sim' ? 'selected' : '' }}>SIM
                                        </option>
                                        <option value="passport"
                                            {{ old('identity_type') == 'passport' ? 'selected' : '' }}>Passport</option>
                                        <option value="lainnya" {{ old('identity_type') == 'lainnya' ? 'selected' : '' }}>
                                            Lainnya</option>
                                    </select>
                                    @error('identity_type')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-900 mb-2">
                                        Nomor Identitas <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="identity_number" id="identity_number" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow @error('identity_number') border-red-500 @enderror"
                                        placeholder="Nomor identitas" value="{{ old('identity_number') }}">
                                    @error('identity_number')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Hidden Items Data (from URL params) -->
                            @if (request()->has('items'))
                                @foreach (request('items') as $key => $item)
                                    <input type="hidden" name="items[{{ $key }}][ticket_type_id]"
                                        value="{{ $item['ticket_type_id'] ?? '' }}">
                                    <input type="hidden" name="items[{{ $key }}][quantity]"
                                        value="{{ $item['quantity'] ?? 0 }}">
                                @endforeach
                            @endif

                            <!-- Terms & Conditions -->
                            <div class="mb-6">
                                <label class="flex items-start cursor-pointer">
                                    <input type="checkbox" name="agree_terms" required
                                        class="mt-1 rounded text-brand-yellow focus:ring-brand-yellow">
                                    <span class="ml-3 text-sm text-gray-600">
                                        Saya setuju dengan <a href="#" class="text-blue-600 hover:underline">syarat
                                            dan
                                            ketentuan</a> yang berlaku
                                    </span>
                                </label>
                            </div>

                            <!-- Submit Button -->
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

                        <!-- Event Info -->
                        <div class="mb-6 pb-6 border-b border-gray-200">
                            <h4 class="font-bold text-lg text-slate-900 mb-2">
                                {{ $event->name }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                <i class="far fa-calendar mr-1"></i>
                                {{ $event->event_date->format('d F Y') }}
                                @if ($event->event_end_date)
                                    - {{ $event->event_end_date->format('d F Y') }}
                                @endif
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $event->venue }}
                            </p>
                        </div>

                        <!-- Ticket Details -->
                        <div class="space-y-3 mb-6 pb-6 border-b border-gray-200" id="ticket-summary">
                            @if (request()->has('items'))
                                @php $totalAmount = 0; @endphp
                                @foreach (request('items', []) as $item)
                                    @php
                                        $ticketType = $event->ticketTypes->find($item['ticket_type_id'] ?? 0);
                                        $quantity = $item['quantity'] ?? 0;
                                    @endphp

                                    @if ($ticketType && $quantity > 0)
                                        @php
                                            $subtotal = $ticketType->current_price * $quantity;
                                            $totalAmount += $subtotal;
                                        @endphp
                                        <div class="flex justify-between text-sm">
                                            <div>
                                                <p class="text-gray-600 font-medium">{{ $ticketType->name }}</p>
                                                <p class="text-xs text-gray-500">
                                                    {{ $quantity }} x Rp
                                                    {{ number_format($ticketType->current_price, 0, ',', '.') }}
                                                </p>
                                            </div>
                                            <p class="font-semibold text-slate-900">
                                                Rp {{ number_format($subtotal, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    @endif
                                @endforeach

                                <!-- Service Fee -->
                                @php
                                    $serviceFee = 5000;
                                    $totalAmount += $serviceFee;
                                @endphp
                                <div class="flex justify-between text-sm">
                                    <p class="text-gray-600">Biaya Layanan</p>
                                    <p class="font-semibold text-slate-900">Rp
                                        {{ number_format($serviceFee, 0, ',', '.') }}</p>
                                </div>
                            @else
                                <p class="text-center text-gray-500 text-sm py-4">Tidak ada tiket dipilih</p>
                            @endif
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-lg font-bold text-slate-900">Total Pembayaran</span>
                            <span class="text-2xl font-bold text-brand-yellow">
                                @if (isset($totalAmount))
                                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                @else
                                    Rp 0
                                @endif
                            </span>
                        </div>

                        <!-- Payment Methods Info -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-600 mb-2 font-semibold">
                                <i class="fas fa-info-circle text-blue-500 mr-1"></i> Metode Pembayaran
                            </p>
                            <p class="text-xs text-gray-500">
                                Pembayaran menggunakan Midtrans dengan berbagai pilihan metode pembayaran
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Form validation
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const btn = document.getElementById('pay-button');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
        });
    </script>
@endpush
