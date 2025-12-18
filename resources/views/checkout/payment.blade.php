@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen flex items-center justify-center">
        <div class="container mx-auto px-4 max-w-2xl">
            <div class="bg-white rounded-2xl shadow-lg p-8 text-center">

                <!-- Loading State (Initially shown) -->
                <div id="loading-state">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-brand-yellow rounded-full mb-4">
                            <i class="fas fa-credit-card text-3xl text-black"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-3">Memproses Pembayaran</h2>
                    <p class="text-gray-600 mb-8">Mohon tunggu, kami sedang menyiapkan halaman pembayaran Anda...</p>

                    <!-- Spinner -->
                    <div class="flex justify-center mb-8">
                        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-brand-yellow"></div>
                    </div>
                </div>

                <!-- Success State (Hidden initially, shown after payment) -->
                <div id="success-state" class="hidden">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                            <i class="fas fa-check-circle text-4xl text-green-600"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-3">Pembayaran Berhasil!</h2>
                    <p class="text-gray-600 mb-8">
                        Terima kasih atas pembelian Anda. Tiket telah dikirim ke email Anda.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('home') }}"
                            class="inline-block bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-8 rounded-xl transition transform hover:scale-105">
                            <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                        </a>
                        <a href="{{ route('order.show', $order->order_number) }}"
                            class="inline-block bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-8 rounded-xl transition">
                            <i class="fas fa-ticket-alt mr-2"></i> Lihat Pesanan
                        </a>
                    </div>
                </div>

                <!-- Pending State (Hidden initially) -->
                <div id="pending-state" class="hidden">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 rounded-full mb-4">
                            <i class="fas fa-clock text-4xl text-yellow-600"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-3">Menunggu Pembayaran</h2>
                    <p class="text-gray-600 mb-4">
                        Pembayaran Anda sedang diproses. Silakan selesaikan pembayaran sesuai instruksi yang diberikan.
                    </p>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                        <p class="text-sm text-yellow-800">
                            <i class="fas fa-info-circle mr-2"></i>
                            Pesanan akan otomatis dibatalkan jika tidak dibayar dalam <strong>24 jam</strong>
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('order.show', $order->order_number) }}"
                            class="inline-block bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-8 rounded-xl transition transform hover:scale-105">
                            <i class="fas fa-receipt mr-2"></i> Lihat Pesanan
                        </a>
                        <a href="{{ route('home') }}"
                            class="inline-block bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-8 rounded-xl transition">
                            <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>

                <!-- Error State (Hidden initially) -->
                <div id="error-state" class="hidden">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-4">
                            <i class="fas fa-times-circle text-4xl text-red-600"></i>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-3">Pembayaran Gagal</h2>
                    <p class="text-gray-600 mb-8" id="error-message">
                        Maaf, terjadi kesalahan saat memproses pembayaran Anda. Silakan coba lagi.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <button onclick="retryPayment()"
                            class="inline-block bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-8 rounded-xl transition transform hover:scale-105">
                            <i class="fas fa-redo mr-2"></i> Coba Lagi
                        </button>
                        <a href="{{ route('home') }}"
                            class="inline-block bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-8 rounded-xl transition">
                            <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                        </a>
                    </div>
                </div>

                <!-- Order Summary (Always visible) -->
                <div class="mt-8 pt-8 border-t border-gray-200">
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h3 class="font-bold text-sm text-gray-600 mb-4">RINGKASAN PESANAN</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Event</span>
                                <span class="font-semibold text-slate-900">{{ $order->event->name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Order Number</span>
                                <span class="font-mono text-xs text-slate-900">{{ $order->order_number }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nama</span>
                                <span class="text-slate-900">{{ $order->customer->full_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Total Tiket</span>
                                <span class="text-slate-900">{{ $order->orderItems->sum('quantity') }} tiket</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between">
                                <span class="font-bold text-slate-900">Total Bayar</span>
                                <span class="font-bold text-lg text-brand-yellow">
                                    Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Expiry Warning --}}
                    @if ($order->payment_status === 'pending')
                        <div class="mt-4 text-xs text-gray-500">
                            <i class="fas fa-clock mr-1"></i>
                            Berlaku sampai: <strong>{{ $order->expired_at->format('d M Y, H:i') }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        // Midtrans Snap Token dari Backend
        const snapToken = @json($snapToken);
        const orderNumber = @json($order->order_number);

        // Auto-trigger Midtrans Snap popup
        window.onload = function() {
            if (snapToken) {
                // Trigger Midtrans Snap
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        console.log('Payment success:', result);
                        showSuccessState();
                        updateOrderStatus('paid');
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        showPendingState();
                        updateOrderStatus('pending');
                    },
                    onError: function(result) {
                        console.log('Payment error:', result);
                        showErrorState('Terjadi kesalahan saat memproses pembayaran.');
                    },
                    onClose: function() {
                        console.log('Payment popup closed');
                        // User closed the popup without completing payment
                        showErrorState(
                            'Pembayaran dibatalkan. Silakan coba lagi atau hubungi customer service.');
                    }
                });
            } else {
                showErrorState('Token pembayaran tidak valid. Silakan coba lagi.');
            }
        };

        function showSuccessState() {
            document.getElementById('loading-state').classList.add('hidden');
            document.getElementById('pending-state').classList.add('hidden');
            document.getElementById('error-state').classList.add('hidden');
            document.getElementById('success-state').classList.remove('hidden');
        }

        function showPendingState() {
            document.getElementById('loading-state').classList.add('hidden');
            document.getElementById('error-state').classList.add('hidden');
            document.getElementById('success-state').classList.add('hidden');
            document.getElementById('pending-state').classList.remove('hidden');
        }

        function showErrorState(message) {
            document.getElementById('loading-state').classList.add('hidden');
            document.getElementById('pending-state').classList.add('hidden');
            document.getElementById('success-state').classList.add('hidden');
            document.getElementById('error-state').classList.remove('hidden');
            document.getElementById('error-message').textContent = message;
        }

        function retryPayment() {
            // Reload Midtrans Snap
            showLoadingState();
            if (snapToken) {
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        showSuccessState();
                        updateOrderStatus('paid');
                    },
                    onPending: function(result) {
                        showPendingState();
                        updateOrderStatus('pending');
                    },
                    onError: function(result) {
                        showErrorState('Terjadi kesalahan saat memproses pembayaran.');
                    },
                    onClose: function() {
                        showErrorState('Pembayaran dibatalkan.');
                    }
                });
            }
        }

        function showLoadingState() {
            document.getElementById('loading-state').classList.remove('hidden');
            document.getElementById('pending-state').classList.add('hidden');
            document.getElementById('success-state').classList.add('hidden');
            document.getElementById('error-state').classList.add('hidden');
        }

        function updateOrderStatus(status) {
            // This function would typically make an AJAX call to update order status
            // But since Midtrans has webhooks, this is optional
            console.log('Order status updated to:', status);
        }
    </script>
@endpush
