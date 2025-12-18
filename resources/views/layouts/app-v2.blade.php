<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Meta CSRF Token (Untuk Laravel) -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Untix') }} - @yield('title', 'E-Ticketing Platform')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <!-- Midtrans Snap (Sandbox) -->
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
        data-client-key="{{ config('midtrans.client_key') }}"></script>

    @stack('styles')
    @stack('head-scripts')
</head>

<body class="font-sans bg-gray-50 text-slate-800 flex flex-col min-h-screen">

    <!-- =========================================================================
         NAVBAR
    ========================================================================== -->
    <nav class="fixed w-full z-50 glass-nav border-b border-gray-800 transition-all duration-300" id="navbar">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="bg-brand-yellow text-black font-bold text-2xl px-2 py-1 rounded skew-x-[-10deg]">
                            UNTIX
                        </div>
                    </div>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="{{ route('home') }}"
                        class="text-gray-300 hover:text-brand-yellow transition font-medium {{ request()->routeIs('home') ? 'text-brand-yellow' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('events.index') }}"
                        class="text-gray-300 hover:text-brand-yellow transition font-medium {{ request()->routeIs('events.*') ? 'text-brand-yellow' : '' }}">
                        Jelajah Event
                    </a>
                </div>

                <!-- Auth Buttons -->
                <div class="hidden md:flex items-center space-x-4">
                    @auth
                        <div class="flex items-center space-x-4">
                            <span class="text-gray-300">Halo, {{ Auth::user()->name }}</span>
                            <form method="POST" action="{{ route('filament.admin.auth.logout') }}" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-6 rounded-full transition transform hover:scale-105">
                                    Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('filament.admin.auth.login') }}"
                            class="bg-brand-yellow hover:bg-yellow-400 text-black font-bold py-2 px-6 rounded-full transition transform hover:scale-105 shadow-lg shadow-yellow-500/20">
                            Masuk
                        </a>
                    @endauth
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-white hover:text-brand-yellow focus:outline-none">
                        <i class="fas fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div id="mobile-menu" class="hidden md:hidden bg-slate-900 border-t border-gray-800">
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="{{ route('home') }}"
                    class="block px-3 py-2 text-base font-medium text-white hover:text-brand-yellow {{ request()->routeIs('home') ? 'text-brand-yellow' : '' }}">
                    Beranda
                </a>
                <a href="{{ route('events.index') }}"
                    class="block px-3 py-2 text-base font-medium text-gray-300 hover:text-brand-yellow {{ request()->routeIs('events.*') ? 'text-brand-yellow' : '' }}">
                    Jelajah Event
                </a>
                @auth
                    <div class="border-t border-gray-700 pt-2 mt-2">
                        <span class="block px-3 py-2 text-sm text-gray-300">{{ Auth::user()->name }}</span>
                        <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                            @csrf
                            <button type="submit"
                                class="block w-full text-left px-3 py-2 text-base font-medium text-red-400 hover:text-red-300">
                                Keluar
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('filament.admin.auth.login') }}"
                        class="block px-3 py-2 text-base font-medium text-brand-yellow hover:text-yellow-300">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </nav>
    <!-- ================= END NAVBAR ================= -->


    <!-- =========================================================================
         MAIN CONTENT
    ========================================================================== -->
    <main class="flex-grow pt-20">
        @yield('content')
    </main>
    <!-- ================= END MAIN CONTENT ================= -->


    <!-- =========================================================================
         FOOTER
    ========================================================================== -->
    <footer class="bg-slate-900 text-gray-400 py-12 border-t border-gray-800">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
                <!-- Brand -->
                <div>
                    <div
                        class="bg-brand-yellow text-black font-bold text-xl px-2 py-1 rounded skew-x-[-10deg] inline-block mb-4">
                        UNTIX
                    </div>
                    <p class="text-sm">Platform ticketing event terpercaya di Indonesia.</p>
                </div>

                <!-- Links -->
                <div>
                    <h5 class="text-white font-bold mb-4">Links</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-brand-yellow transition">Tentang Kami</a></li>
                        <li><a href="{{ route('events.index') }}" class="hover:text-brand-yellow transition">Jelajah
                                Event</a></li>
                        <li><a href="#" class="hover:text-brand-yellow transition">Cara Pesan</a></li>
                    </ul>
                </div>

                <!-- Bantuan -->
                <div>
                    <h5 class="text-white font-bold mb-4">Bantuan</h5>
                    <ul class="space-y-2 text-sm">
                        <li><a href="#" class="hover:text-brand-yellow transition">FAQ</a></li>
                        <li><a href="#" class="hover:text-brand-yellow transition">Kontak</a></li>
                        <li><a href="#" class="hover:text-brand-yellow transition">Syarat & Ketentuan</a></li>
                    </ul>
                </div>

                <!-- Social -->
                <div>
                    <h5 class="text-white font-bold mb-4">Social Media</h5>
                    <div class="flex gap-4">
                        <a href="#"
                            class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-yellow hover:text-black transition">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#"
                            class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-yellow hover:text-black transition">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#"
                            class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center hover:bg-brand-yellow hover:text-black transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-800 pt-8 text-center text-sm">
                &copy; {{ date('Y') }} Untix. All rights reserved.
            </div>
        </div>
    </footer>
    <!-- ================= END FOOTER ================= -->


    <!-- Base Scripts -->
    <script>
        // Mobile Menu Toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });

        // Helper: Format Rupiah
        function formatRupiah(n) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(n);
        }
    </script>

    @stack('scripts')
</body>

</html>
