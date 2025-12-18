@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
    <style>
        .slider-container {
            position: relative;
            overflow: hidden;
        }

        .slider-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .slider-item {
            min-width: 100%;
            position: relative;
        }

        .slider-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to right, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7));
        }
    </style>
@endpush

@section('content')
    <!-- Hero Slider Section -->
    <section class="relative bg-gray-900 overflow-hidden slider-container" style="height: 600px;">
        <div class="slider-track" id="sliderTrack">
            <!-- Slide 1 -->
            <div class="slider-item">
                <img src="https://images.unsplash.com/photo-1540039155733-5bb30b53aa14?w=1920&h=600&fit=crop"
                    class="w-full h-full object-cover" alt="Event 1">
                <div class="slider-overlay"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-white z-10 px-4">
                    <span class="inline-block py-2 px-4 rounded-full bg-brand-yellow text-black text-sm font-bold mb-4">
                        Diskon Hingga 50%
                    </span>
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 text-center">
                        Khusus pembelian tiket awal
                    </h1>
                    <button
                        class="bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-8 rounded-lg transition">
                        Lihat Promo
                    </button>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="slider-item">
                <img src="https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=1920&h=600&fit=crop"
                    class="w-full h-full object-cover" alt="Event 2">
                <div class="slider-overlay"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-white z-10 px-4">
                    <span class="inline-block py-2 px-4 rounded-full bg-brand-yellow text-black text-sm font-bold mb-4">
                        🎉 Event Terbaru
                    </span>
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 text-center">
                        Konser Musik Spektakuler
                    </h1>
                    <button
                        class="bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-8 rounded-lg transition">
                        Beli Tiket
                    </button>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="slider-item">
                <img src="https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=1920&h=600&fit=crop"
                    class="w-full h-full object-cover" alt="Event 3">
                <div class="slider-overlay"></div>
                <div class="absolute inset-0 flex flex-col items-center justify-center text-white z-10 px-4">
                    <span class="inline-block py-2 px-4 rounded-full bg-brand-yellow text-black text-sm font-bold mb-4">
                        🎸 Festival 2025
                    </span>
                    <h1 class="text-4xl md:text-6xl font-bold mb-6 text-center">
                        Pengalaman Tak Terlupakan
                    </h1>
                    <button
                        class="bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-3 px-8 rounded-lg transition">
                        Jelajahi Event
                    </button>
                </div>
            </div>
        </div>

        <!-- Slider Navigation -->
        <button id="prevBtn"
            class="absolute left-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-brand-yellow text-black flex items-center justify-center hover:bg-yellow-400 transition">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button id="nextBtn"
            class="absolute right-4 top-1/2 -translate-y-1/2 z-20 w-12 h-12 rounded-full bg-brand-yellow text-black flex items-center justify-center hover:bg-yellow-400 transition">
            <i class="fas fa-chevron-right"></i>
        </button>

        <!-- Search Bar -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 z-20 w-full max-w-3xl px-4">
            <form action="{{ route('events.index') }}" method="GET"
                class="bg-white p-2 rounded-full shadow-2xl flex items-center">
                <div class="pl-4 text-gray-400"><i class="fas fa-search text-lg"></i></div>
                <input type="text" name="search"
                    class="w-full px-4 py-3 text-gray-700 focus:outline-none bg-transparent"
                    placeholder="Cari event, artis, atau lokasi...">
                <button type="submit"
                    class="bg-brand-yellow hover:bg-yellow-400 text-black px-8 py-3 rounded-full font-bold transition">
                    Cari
                </button>
            </form>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-12 bg-gray-50">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Happy Customers -->
                <div class="bg-white rounded-2xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-brand-yellow rounded-full flex items-center justify-center">
                        <i class="fas fa-users text-2xl text-gray-800"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">500K+</h3>
                    <p class="text-gray-600">Happy Customers</p>
                </div>

                <!-- Events Organized -->
                <div class="bg-white rounded-2xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-brand-yellow rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-2xl text-gray-800"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">10K+</h3>
                    <p class="text-gray-600">Events Organized</p>
                </div>

                <!-- User Rating -->
                <div class="bg-white rounded-2xl shadow-md p-8 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 bg-brand-yellow rounded-full flex items-center justify-center">
                        <i class="fas fa-star text-2xl text-gray-800"></i>
                    </div>
                    <h3 class="text-3xl font-bold text-gray-900 mb-2">4.9/5</h3>
                    <p class="text-gray-600">User Rating</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Events -->
    <section class="py-16 bg-white">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center mb-10">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900 mb-2">Featured Events</h2>
                    <p class="text-gray-600">Event terpopuler minggu ini</p>
                </div>
                <a href="{{ route('events.index') }}"
                    class="text-gray-900 font-semibold hover:text-brand-yellow transition flex items-center gap-2">
                    <i class="fas fa-chart-line"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @forelse($upcomingEvents->take(3) as $event)
                    <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition group">
                        <a href="{{ route('events.show', $event->slug) }}">
                            <div class="relative h-56 overflow-hidden">
                                @if ($event->poster_image)
                                    <img src="{{ asset('storage/' . $event->poster_image) }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                        alt="{{ $event->name }}">
                                @else
                                    <img src="https://images.unsplash.com/photo-1501281668745-f7f57925c3b4?w=600&h=400&fit=crop"
                                        class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                        alt="{{ $event->name }}">
                                @endif
                                <span
                                    class="absolute top-4 right-4 bg-brand-yellow text-black text-xs font-bold py-1 px-3 rounded-full">
                                    @if ($event->ticketTypes->count() > 0)
                                        {{ $event->ticketTypes->first()->type ?? 'Music' }}
                                    @else
                                        Event
                                    @endif
                                </span>
                            </div>
                            <div class="p-5">
                                <h3 class="font-bold text-xl mb-2 text-gray-900 group-hover:text-brand-yellow transition">
                                    {{ $event->name }}
                                </h3>
                                <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                                    <i class="far fa-calendar"></i>
                                    <span>{{ $event->event_date->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600 mb-3">
                                    <i class="far fa-clock"></i>
                                    <span>{{ $event->event_date->format('H:i') }} WIB</span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-gray-600 mb-4">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $event->venue }}</span>
                                </div>
                                <div class="border-t pt-3">
                                    <span class="text-xs text-gray-500">Mulai dari</span>
                                    <div class="flex justify-between items-center">
                                        <span class="font-bold text-lg text-gray-900">
                                            @if ($event->ticketTypes->count() > 0)
                                                Rp {{ number_format($event->ticketTypes->min('price'), 0, ',', '.') }}
                                            @else
                                                TBA
                                            @endif
                                        </span>
                                        <button
                                            class="bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-2 px-6 rounded-lg transition text-sm">
                                            Beli Tiket
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <i class="fas fa-calendar-times text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500 text-lg">Belum ada event featured saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16 bg-gray-50">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Kenapa Pilih ?</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- E-Ticket Instant -->
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-brand-yellow rounded-full flex items-center justify-center">
                        <i class="fas fa-ticket-alt text-3xl text-gray-800"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">E-Ticket Instant</h3>
                    <p class="text-gray-600">Dapatkan tiket langsung di email Anda setelah pembayaran</p>
                </div>

                <!-- 24/7 Support -->
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-brand-yellow rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-3xl text-gray-800"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">24/7 Support</h3>
                    <p class="text-gray-600">Customer service siap membantu kapan saja</p>
                </div>

                <!-- Terpercaya -->
                <div class="text-center">
                    <div class="w-20 h-20 mx-auto mb-6 bg-brand-yellow rounded-full flex items-center justify-center">
                        <i class="fas fa-shield-alt text-3xl text-gray-800"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3">Terpercaya</h3>
                    <p class="text-gray-600">Ribuan event berhasil diselenggarakan dengan aman</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gray-800 text-white">
        <div class="container mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-bold mb-4">Siap Mengadakan Event?</h2>
            <p class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">
                Mulai buat event Anda sendiri dan jangkau ribuan peserta
            </p>
            <button
                class="bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-4 px-10 rounded-lg transition transform hover:scale-105">
                Buat Event Sekarang
            </button>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Slider functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slider-item');
        const totalSlides = slides.length;
        const sliderTrack = document.getElementById('sliderTrack');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');

        function updateSlider() {
            sliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider();
        }

        function prevSlide() {
            currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
            updateSlider();
        }

        nextBtn.addEventListener('click', nextSlide);
        prevBtn.addEventListener('click', prevSlide);

        // Auto slide every 5 seconds
        setInterval(nextSlide, 5000);
    </script>
@endpush
