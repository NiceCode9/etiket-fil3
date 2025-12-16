@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="container mx-auto px-4 max-w-6xl">

            <!-- Breadcrumb -->
            <div class="flex items-center gap-2 text-sm mb-8 text-gray-600">
                <a href="{{ route('events.index') }}" class="hover:text-brand-yellow transition">Events</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <a href="{{ route('events.show', $event->id) }}" class="hover:text-brand-yellow transition">
                    {{ $event->title }}
                </a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-slate-900 font-semibold">Checkout</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- LEFT: Form Pembeli -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-lg p-8">
                        <h2 class="text-2xl font-bold text-slate-900 mb-6">
                            <i class="fas fa-user-circle text-brand-yellow mr-2"></i> Data Pembeli
                        </h2>

                        <form action="{{ route('payment.process') }}" method="POST" id="checkout-form">
                            @csrf
                            <input type="hidden" name="event_id" value="{{ $event->id }}">
                            <input type="hidden" name="qty_regular" value="{{ $qtyRegular }}">
                            <input type="hidden" name="qty_vip" value="{{ $qtyVip }}">
                            <input type="hidden" name="total_amount" value="{{ $totalAmount }}">

                            <!-- Nama Lengkap -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="customer_name" id="customer-name" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow @error('customer_name') border-red-500 @enderror"
                                    placeholder="Masukkan nama lengkap"
                                    value="{{ old('customer_name', Auth::user()->name ?? '') }}">
                                @error('customer_name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="customer_email" id="customer-email" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow @error('customer_email') border-red-500 @enderror"
                                    placeholder="email@example.com"
                                    value="{{ old('customer_email', Auth::user()->email ?? '') }}">
                                @error('customer_email')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Nomor Telepon -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    Nomor Telepon <span class="text-red-500">*</span>
                                </label>
                                <input type="tel" name="customer_phone" id="customer-phone" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow @error('customer_phone') border-red-500 @enderror"
                                    placeholder="08xxxxxxxxxx" value="{{ old('customer_phone') }}">
                                @error('customer_phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Catatan (Optional) -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-slate-900 mb-2">
                                    Catatan (Opsional)
                                </label>
                                <textarea name="notes" rows="3"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-yellow"
                                    placeholder="Tambahkan catatan jika ada...">{{ old('notes') }}</textarea>
                            </div>

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
                            <h4 class="font-bold text-lg text-slate-900 mb-2" id="checkout-event-title">
                                {{ $event->title }}
                            </h4>
                            <p class="text-sm text-gray-600">
                                <i class="far fa-calendar mr-1"></i>
                                {{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}
                            </p>
                            <p class="text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt mr-1"></i>
                                {{ $event->location }}
                            </p>
                        </div>

                        <!-- Ticket Details -->
                        <div class="space-y-3 mb-6 pb-6 border-b border-gray-200">
                            @if ($qtyRegular > 0)
                                <div class="flex justify-between text-sm">
                                    <div>
                                        <p class="text-gray-600">Tiket Regular</p>
                                        <p class="text-xs text-gray-500">{{ $qtyRegular }} x Rp
                                            {{ number_format($event->price_regular, 0, ',', '.') }}</p>
                                    </div>
                                    <p class="font-semibold text-slate-900" id="summary-regular-total">
                                        Rp {{ number_format($qtyRegular * $event->price_regular, 0, ',', '.') }}
                                    </p>
                                </div>
                            @endif

                            @if ($qtyVip > 0)
                                <div class="flex justify-between text-sm">
                                    <div>
                                        <p class="text-gray-600">Tiket VIP</p>
                                        <p class="text-xs text-gray-500">{{ $qtyVip }} x Rp
                                            {{ number_format($event->price_vip, 0, ',', '.') }}</p>
                                    </div>
                                    <p class="font-semibold text-slate-900" id="summary-vip-total">
                                        Rp {{ number_format($qtyVip * $event->price_vip, 0, ',', '.') }}
                                    </p>
                                </div>
                            @endif

                            <div class="flex justify-between text-sm">
                                <p class="text-gray-600">Biaya Layanan</p>
                                <p class="font-semibold text-slate-900">Rp 5.000</p>
                            </div>
                        </div>

                        <!-- Total -->
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-lg font-bold text-slate-900">Total Pembayaran</span>
                            <span class="text-2xl font-bold text-brand-yellow" id="checkout-total-pay">
                                Rp {{ number_format($totalAmount, 0, ',', '.') }}
                            </span>
                        </div>

                        <!-- Payment Methods Info -->
                        <div class="bg-gray-50 rounded-xl p-4">
                            <p class="text-xs text-gray-600 mb-2 font-semibold">
                                <i class="fas fa-info-circle text-blue-500 mr-1"></i> Metode Pembayaran
                            </p>
                            <div class="flex flex-wrap gap-2">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/5/5e/Visa_Inc._logo.svg"
                                    alt="Visa" class="h-6">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg"
                                    alt="Mastercard" class="h-6">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/8/85/Logo_gopay.svg"
                                    alt="GoPay" class="h-6">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/e/eb/Logo_ovo_purple.svg"
                                    alt="OVO" class="h-6">
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
        // Form validation
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const btn = document.getElementById('pay-button');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Memproses...';
        });
    </script>
@endpush
