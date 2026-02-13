@extends('layouts.app')

@section('title', 'Menunggu Pembayaran')

@section('content')
    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="bg-white rounded-2xl shadow-lg p-8">

                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-brand-yellow rounded-full mb-4">
                        <i class="fas fa-money-bill-wave text-3xl text-black"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900 mb-3">Menunggu Pembayaran</h2>
                    <p class="text-gray-600">
                        Pesanan Anda telah dibuat. Silakan lakukan pembayaran untuk mengkonfirmasi pesanan.
                    </p>
                </div>

                <!-- Order Summary -->
                <div class="bg-gray-50 rounded-xl p-6 mb-6">
                    <h3 class="font-bold text-sm text-gray-600 mb-4">RINGKASAN PESANAN</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Event</span>
                            <span class="font-semibold text-slate-900">{{ $order->event->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nomor Pesanan</span>
                            <span class="font-mono text-xs text-slate-900">{{ $order->order_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Nama</span>
                            <span class="text-slate-900">{{ $order->customer->full_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email</span>
                            <span class="text-slate-900">{{ $order->customer->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Tiket</span>
                            <span class="text-slate-900">{{ $order->orderItems->sum('quantity') }} tiket</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2 mt-2 flex justify-between">
                            <span class="font-bold text-slate-900">Total Pembayaran</span>
                            <span class="font-bold text-lg text-brand-yellow">
                                Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Expiry Warning -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-clock text-yellow-600 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-yellow-800 font-semibold mb-1">Batas Waktu Pembayaran</p>
                            <p class="text-xs text-yellow-700">
                                Berlaku sampai: <strong>{{ $order->expired_at->format('d M Y, H:i') }} WIB</strong>
                            </p>
                            <p class="text-xs text-yellow-700 mt-1">
                                Pesanan akan otomatis dibatalkan jika tidak dibayar sebelum batas waktu
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Payment Instructions -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-blue-900 mb-4 flex items-center">
                        <i class="fas fa-university mr-2"></i>
                        Informasi Rekening Tujuan
                    </h3>

                    <div class="bg-white rounded-lg p-4 mb-4">
                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Nama Bank</p>
                                <p class="font-bold text-slate-900">Bank BCA</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Nomor Rekening</p>
                                <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                    <p class="font-bold text-lg text-slate-900" id="accountNumber">1234567890</p>
                                    <button onclick="copyToClipboard('1234567890', 'accountNumber')"
                                        class="text-brand-yellow hover:text-yellow-600 transition">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Atas Nama</p>
                                <p class="font-bold text-slate-900">PT Event Organizer Indonesia</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-1">Jumlah Transfer</p>
                                <div class="flex items-center justify-between bg-gray-50 p-3 rounded">
                                    <p class="font-bold text-xl text-brand-yellow" id="totalAmount">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </p>
                                    <button onclick="copyToClipboard('{{ $order->total_amount }}', 'totalAmount')"
                                        class="text-brand-yellow hover:text-yellow-600 transition">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                                <p class="text-xs text-red-600 mt-1">
                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                    Transfer sesuai nominal di atas agar verifikasi lebih cepat
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- How to Pay -->
                <div class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                    <h3 class="font-bold text-slate-900 mb-4 flex items-center">
                        <i class="fas fa-list-ol mr-2 text-brand-yellow"></i>
                        Cara Pembayaran
                    </h3>
                    <ol class="space-y-3 text-sm text-gray-700">
                        <li class="flex items-start">
                            <span
                                class="flex-shrink-0 w-6 h-6 bg-brand-yellow text-black rounded-full flex items-center justify-center font-bold text-xs mr-3 mt-0.5">1</span>
                            <span>Transfer sesuai nominal yang tertera ke rekening di atas</span>
                        </li>
                        <li class="flex items-start">
                            <span
                                class="flex-shrink-0 w-6 h-6 bg-brand-yellow text-black rounded-full flex items-center justify-center font-bold text-xs mr-3 mt-0.5">2</span>
                            <span>Screenshot atau simpan bukti transfer Anda</span>
                        </li>
                        <li class="flex items-start">
                            <span
                                class="flex-shrink-0 w-6 h-6 bg-brand-yellow text-black rounded-full flex items-center justify-center font-bold text-xs mr-3 mt-0.5">3</span>
                            <span>Kirim bukti transfer ke WhatsApp Admin dengan format:</span>
                        </li>
                    </ol>

                    <div class="bg-gray-50 p-4 rounded-lg mt-3 mb-4">
                        <p class="text-xs text-gray-600 mb-2 font-semibold">Format Pesan:</p>
                        <p class="text-sm text-gray-800 font-mono">
                            Konfirmasi Pembayaran<br>
                            Nomor Pesanan: {{ $order->order_number }}<br>
                            Nama: {{ $order->customer->full_name }}<br>
                            Total: Rp {{ number_format($order->total_amount, 0, ',', '.') }}<br>
                            <br>
                            [Lampirkan bukti transfer]
                        </p>
                    </div>

                    <a href="https://wa.me/6282140300286?text={{ urlencode('Konfirmasi Pembayaran' . "\n" . 'Nomor Pesanan: ' . $order->order_number . "\n" . 'Nama: ' . $order->customer->full_name . "\n" . 'Total: Rp ' . number_format($order->total_amount, 0, ',', '.')) }}"
                        target="_blank"
                        class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-6 rounded-xl transition transform hover:scale-105 flex items-center justify-center">
                        <i class="fab fa-whatsapp text-xl mr-2"></i>
                        Kirim Bukti via WhatsApp
                    </a>

                    <div class="mt-3 text-center">
                        <p class="text-xs text-gray-600">Nomor WhatsApp Admin:</p>
                        <p class="font-bold text-slate-900">+62 821-4030-0286</p>
                    </div>
                </div>

                <!-- Tracking Info -->
                <div class="bg-gradient-to-r from-purple-50 to-blue-50 border border-purple-200 rounded-lg p-6 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-search-location text-purple-600 text-xl mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-purple-900 font-semibold mb-2">Lacak Status Pesanan Anda</p>
                            <p class="text-xs text-purple-800 mb-3">
                                Anda dapat melacak status pesanan Anda kapan saja menggunakan nomor pesanan atau nomor
                                identitas yang digunakan saat pembelian.
                            </p>
                            <a href="{{ route('tracking.index') }}"
                                class="inline-flex items-center text-sm font-semibold text-purple-700 hover:text-purple-900 transition">
                                <i class="fas fa-arrow-right mr-2"></i>
                                Lacak Pesanan Sekarang
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Important Notes -->
                <div class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-orange-600 mt-1 mr-3"></i>
                        <div class="flex-1">
                            <p class="text-sm text-orange-800 font-semibold mb-2">Penting untuk Diperhatikan:</p>
                            <ul class="text-xs text-orange-700 space-y-1">
                                <li>• Pastikan transfer sesuai dengan nominal yang tertera</li>
                                <li>• Simpan bukti transfer sampai tiket Anda diverifikasi</li>
                                <li>• Verifikasi pembayaran biasanya memakan waktu 1-3 jam pada jam kerja</li>
                                <li>• Barcode tiket dapat didownload di tracking ticket</li>
                                <li>• Jika ada kendala, hubungi admin melalui WhatsApp</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="{{ route('tracking.index') }}"
                        class="flex-1 text-center bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-6 rounded-xl transition transform hover:scale-105">
                        <i class="fas fa-search mr-2"></i> Lacak Pesanan
                    </a>
                    <a href="{{ route('home') }}"
                        class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-xl transition">
                        <i class="fas fa-home mr-2"></i> Kembali ke Beranda
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function copyToClipboard(text, elementId) {
            // Create temporary input element
            const tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            // Show feedback
            const element = document.getElementById(elementId);
            const originalHTML = element.innerHTML;

            // Change icon to checkmark temporarily
            const button = element.parentElement.querySelector('button');
            const originalIcon = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check text-green-600"></i>';

            // Reset after 2 seconds
            setTimeout(() => {
                button.innerHTML = originalIcon;
            }, 2000);

            // Show toast notification
            showToast('Berhasil disalin!');
        }

        function showToast(message) {
            const toast = document.createElement('div');
            toast.className =
                'fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
            toast.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.classList.add('animate-fade-out');
                setTimeout(() => {
                    document.body.removeChild(toast);
                }, 300);
            }, 2000);
        }
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fade-out {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(10px);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        .animate-fade-out {
            animation: fade-out 0.3s ease-out;
        }
    </style>
@endpush
