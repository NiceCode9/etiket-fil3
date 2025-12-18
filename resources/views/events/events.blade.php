@extends('layouts.app')

@section('title', 'Jelajah Event')

@section('content')
    <div class="pt-8 pb-20">
        <div class="container mx-auto px-4">

            <!-- Page Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Jelajah Event</h1>
                    <p class="text-gray-500 mt-1">Temukan hiburan terbaik di sekitarmu</p>
                </div>

                <!-- Search -->
                <form action="{{ route('events.index') }}" method="GET" class="relative w-full md:w-96">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="w-full pl-10 pr-4 py-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-brand-yellow"
                        placeholder="Cari konser, artis...">
                    <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                <!-- SIDEBAR: FILTER (Kiri) -->
                <aside class="lg:col-span-1">
                    <form action="{{ route('events.index') }}" method="GET" id="filter-form">
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-28">
                            <div class="flex justify-between items-center mb-6">
                                <h3 class="font-bold text-lg">
                                    <i class="fas fa-filter text-brand-yellow mr-2"></i> Filter
                                </h3>
                                <a href="{{ route('events.index') }}"
                                    class="text-xs text-blue-600 hover:underline">Reset</a>
                            </div>

                            <!-- Filter: Kategori -->
                            <div class="mb-6">
                                <h4 class="font-semibold text-sm mb-3">Kategori</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="category[]" value="konser"
                                            {{ in_array('konser', request('category', [])) ? 'checked' : '' }}
                                            class="rounded text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-2 text-gray-600 text-sm">Konser Musik</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="category[]" value="standup"
                                            {{ in_array('standup', request('category', [])) ? 'checked' : '' }}
                                            class="rounded text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-2 text-gray-600 text-sm">Stand Up Comedy</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="category[]" value="workshop"
                                            {{ in_array('workshop', request('category', [])) ? 'checked' : '' }}
                                            class="rounded text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-2 text-gray-600 text-sm">Seminar & Workshop</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" name="category[]" value="olahraga"
                                            {{ in_array('olahraga', request('category', [])) ? 'checked' : '' }}
                                            class="rounded text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-2 text-gray-600 text-sm">Olahraga</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Filter: Lokasi -->
                            <div class="mb-6">
                                <h4 class="font-semibold text-sm mb-3">Lokasi</h4>
                                <select name="location"
                                    class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:outline-none focus:border-brand-yellow"
                                    onchange="document.getElementById('filter-form').submit()">
                                    <option value="">Semua Lokasi</option>
                                    <option value="jakarta" {{ request('location') == 'jakarta' ? 'selected' : '' }}>Jakarta
                                    </option>
                                    <option value="bandung" {{ request('location') == 'bandung' ? 'selected' : '' }}>Bandung
                                    </option>
                                    <option value="surabaya" {{ request('location') == 'surabaya' ? 'selected' : '' }}>
                                        Surabaya</option>
                                    <option value="yogyakarta" {{ request('location') == 'yogyakarta' ? 'selected' : '' }}>
                                        Yogyakarta</option>
                                    <option value="bali" {{ request('location') == 'bali' ? 'selected' : '' }}>Bali
                                    </option>
                                </select>
                            </div>

                            <!-- Filter: Harga -->
                            <div class="mb-6">
                                <h4 class="font-semibold text-sm mb-3">Range Harga</h4>
                                <div class="space-y-2">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="price_range" value="all"
                                            {{ request('price_range', 'all') == 'all' ? 'checked' : '' }}
                                            class="text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-2 text-gray-600 text-sm">Semua Harga</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="price_range" value="0-100000"
                                            {{ request('price_range') == '0-100000' ? 'checked' : '' }}
                                            class="text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-2 text-gray-600 text-sm">
                                            < Rp 100.000</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="price_range" value="100000-300000"
                                            {{ request('price_range') == '100000-300000' ? 'checked' : '' }}
                                            class="text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-2 text-gray-600 text-sm">Rp 100.000 - Rp 300.000</span>
                                    </label>
                                    <label class="flex items-center cursor-pointer">
                                        <input type="radio" name="price_range" value="300000-999999999"
                                            {{ request('price_range') == '300000-999999999' ? 'checked' : '' }}
                                            class="text-brand-yellow focus:ring-brand-yellow"
                                            onchange="document.getElementById('filter-form').submit()">
                                        <span class="ml-2 text-gray-600 text-sm">> Rp 300.000</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Filter: Tanggal -->
                            <div class="mb-2">
                                <h4 class="font-semibold text-sm mb-3">Periode</h4>
                                <select name="period"
                                    class="w-full border border-gray-300 rounded-lg p-2 text-sm focus:outline-none focus:border-brand-yellow"
                                    onchange="document.getElementById('filter-form').submit()">
                                    <option value="">Semua Waktu</option>
                                    <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Hari Ini
                                    </option>
                                    <option value="week" {{ request('period') == 'week' ? 'selected' : '' }}>Minggu Ini
                                    </option>
                                    <option value="month" {{ request('period') == 'month' ? 'selected' : '' }}>Bulan Ini
                                    </option>
                                </select>
                            </div>
                        </div>
                    </form>
                </aside>

                <!-- MAIN: EVENT GRID (Kanan) -->
                <div class="lg:col-span-3">
                    <!-- Sort & Count -->
                    <div class="flex justify-between items-center mb-6">
                        <p class="text-gray-600 text-sm">Menampilkan <strong>{{ $events->count() }}</strong> dari
                            <strong>{{ $events->total() }}</strong> event
                        </p>
                        <form action="{{ route('events.index') }}" method="GET" id="sort-form">
                            @foreach (request()->except('sort') as $key => $value)
                                @if (is_array($value))
                                    @foreach ($value as $item)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <select name="sort"
                                class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:border-brand-yellow"
                                onchange="document.getElementById('sort-form').submit()">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru
                                </option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga
                                    Terendah</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga
                                    Tertinggi</option>
                                <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler
                                </option>
                            </select>
                        </form>
                    </div>

                    <!-- Events Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                        @forelse($events as $event)
                            <div
                                class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition group cursor-pointer">
                                <a href="{{ route('events.show', $event->slug) }}">
                                    <div class="relative h-48 overflow-hidden">
                                        <img src="{{ $event->image_url ?? 'https://images.unsplash.com/photo-1533174072545-e8d4aa97edf9?auto=format&fit=crop&w=800&q=80' }}"
                                            class="w-full h-full object-cover group-hover:scale-110 transition duration-500"
                                            alt="{{ $event->title }}">
                                        <span
                                            class="absolute top-3 right-3 bg-white/90 backdrop-blur text-xs font-bold px-2 py-1 rounded">
                                            {{ ucfirst($event->category) }}
                                        </span>
                                    </div>
                                    <div class="p-5">
                                        <p
                                            class="text-xs text-brand-yellow bg-slate-900 inline-block px-2 py-0.5 rounded mb-2">
                                            {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                                        </p>
                                        <h3 class="font-bold text-lg mb-1 group-hover:text-blue-600 transition">
                                            {{ $event->title }}
                                        </h3>
                                        <p class="text-gray-500 text-xs mb-4">
                                            <i class="fas fa-map-marker-alt mr-1"></i> {{ $event->location }}
                                        </p>
                                        <div class="border-t border-gray-100 pt-3 flex justify-between items-center">
                                            <span class="text-sm text-gray-500">Mulai dari</span>
                                            <span class="font-bold text-lg text-slate-900">
                                                Rp {{ number_format($event->price_regular, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500 text-lg mb-2">Tidak ada event ditemukan</p>
                                <p class="text-gray-400 text-sm">Coba gunakan filter atau kata kunci yang berbeda</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if ($events->hasPages())
                        <div class="flex justify-center">
                            {{ $events->links() }}
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
@endsection
