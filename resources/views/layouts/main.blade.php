{{-- resources/views/layouts/main.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'RumahUsaha.id - Platform UMKM Perumahan')</title>
    <meta name="description" content="@yield('description', 'Platform digital untuk mempromosikan dan menghubungkan pelaku UMKM di lingkungan perumahan')">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=acme:400|aleo:500|inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-inter-400 bg-fix-300/15 antialiased">
    {{-- Skip to content for accessibility --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-600 text-white px-4 py-2 rounded-lg shadow-warm">
        Skip to main content
    </a>

    {{-- Header Navigation --}}
    @auth
        <livewire:layout.main-navigation />
    @else
        <header class="bg-white shadow-warm sticky top-0 z-50 border-b border-accent-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    {{-- Logo --}}
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}"
                            class="flex items-center text-xl font-bold text-secondary-800 hover:text-primary-600 transition-colors">
                            <div class="w-8 h-8 bg-fix-400 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                </svg>
                            </div>
                            RUMAHUSAHA.ID
                        </a>
                    </div>

                    <div class="flex items-center space-x-4">
                        {{-- Desktop Navigation --}}
                        <nav class="hidden md:flex space-x-1" aria-label="Main navigation">
                            @php
                                $generalNav = [
                                    ['route' => 'home', 'label' => 'Home', 'anchor' => null],
                                    ['route' => 'business.*', 'label' => 'Profil Usaha', 'anchor' => '#list-umkm'],
                                    ['route' => 'products.*', 'label' => 'Produk', 'anchor' => '#list-product'],
                                    ['route' => 'news.*', 'label' => 'Berita', 'anchor' => '#news-activities'],
                                    ['route' => 'formulir-pendaftaran.*', 'label' => 'Gabung', 'anchor' => '#join-community'],
                                    // ['route' => 'contact', 'label' => 'Kontak', 'anchor' => null],
                                ];

                                $admin_nav = [
                                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'anchor' => null],
                                    ['route' => 'admin.users', 'label' => 'Kelola Users', 'anchor' => null],
                                    ['route' => 'admin.products', 'label' => 'Kelola Produk', 'anchor' => null],
                                ];

                                $umkm_owner_nav = [
                                    ['route' => 'umkm.dashboard', 'label' => 'Dashboard UMKM', 'anchor' => null],
                                    ['route' => 'umkm.products', 'label' => 'Produk Saya', 'anchor' => null],
                                    ['route' => 'umkm.profile', 'label' => 'Profil Bisnis', 'anchor' => null],
                                ];

                                $navItems = match (auth()->user()->user_type ?? 'guest') {
                                    'admin' => $admin_nav,
                                    'pemilik_umkm' => $umkm_owner_nav,
                                    default => $generalNav,
                                };
                            @endphp

                            @foreach ($navItems as $item)
                                <a href="{{ $item['anchor'] ?? ($item['route'] !== 'contact' ? route('home') . $item['anchor'] : '#') }}"
                                    class="@if (request()->routeIs($item['route'])) bg-primary-50 text-primary-700 border-primary-200 @else text-secondary-700 hover:text-primary-600 hover:bg-accent-50 border-transparent @endif px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border"
                                    @if ($item['route'] === request()->route()->getName()) aria-current="page" @endif>
                                    {{ $item['label'] }}
                                </a>
                            @endforeach
                        </nav>

                        {{-- User Menu for Dashboard Users --}}
                        @auth
                            @if (in_array(auth()->user()->user_type, ['admin', 'pemilik_umkm']))
                                <div class="relative hidden md:block">
                                    <button type="button"
                                        class="flex items-center text-sm font-medium text-secondary-700 hover:text-secondary-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 rounded-lg px-3 py-2"
                                        onclick="toggleUserMenu()" aria-expanded="false" aria-haspopup="true">
                                        <div
                                            class="w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mr-2">
                                            <span class="text-sm font-semibold">
                                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                                        <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    {{-- Dropdown Menu --}}
                                    <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-warm-lg py-1 ring-1 ring-accent-200 ring-opacity-50"
                                        id="user-menu">
                                        <div class="border-t border-accent-100"></div>
                                        <button wire:click="logout"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Logout
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endauth

                        {{-- Mobile menu button --}}
                        <button type="button"
                            class="md:hidden p-2 rounded-lg text-secondary-400 hover:text-secondary-500 hover:bg-accent-50 transition-colors"
                            onclick="toggleMobileMenu()" aria-expanded="false" aria-controls="mobile-menu"
                            aria-label="Toggle navigation menu">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Mobile Navigation Menu --}}
                <div class="md:hidden hidden" id="mobile-menu">
                    <div class="px-2 pt-2 pb-3 space-y-1 border-t border-accent-200">
                        @foreach ($navItems as $item)
                            <a href="{{ $item['anchor'] ?? ($item['route'] !== 'contact' ? route('home') . $item['anchor'] : '#') }}"
                                class="@if (request()->routeIs($item['route'])) bg-primary-50 text-primary-700 @else text-secondary-700 hover:text-primary-600 hover:bg-accent-50 @endif block px-3 py-2 rounded-lg text-base font-medium transition-colors"
                                onclick="closeMobileMenu()">
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        {{-- Mobile User Menu --}}
                        @auth
                            @if (in_array(auth()->user()->user_type, ['admin', 'pemilik_umkm']))
                                <div class="border-t border-accent-200 pt-4 mt-4">
                                    <div class="px-3 py-2">
                                        <div class="flex items-center mb-3">
                                            <div
                                                class="w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-sm font-semibold">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-base font-medium text-secondary-800">
                                                    {{ auth()->user()->name }}</div>
                                                <div class="text-sm text-secondary-500">
                                                    {{ ucfirst(str_replace('_', ' ', auth()->user()->user_type)) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <button wire:click="logout"
                                        class="w-full text-left px-3 py-2 text-base text-red-600 hover:bg-red-50 flex items-center rounded-lg">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Logout
                                    </button>
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>
        </header>
    @endauth

    {{-- Main Content --}}
    <main id="main-content" class="min-h-screen">
        <div>
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-fix-100 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-fix-400 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold">RUMAHUSAHA.ID</h3>
                    </div>
                    <p class="text-white text-sm leading-relaxed max-w-md">
                        Platform digital untuk mempromosikan dan menghubungkan pelaku UMKM di lingkungan perumahan.
                        Membangun ekonomi bersama melalui kolaborasi dan inovasi.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Menu</h4>
                    <nav class="space-y-2" aria-label="Footer navigation">
                        <a href="#"
                            class="block text-white hover:text-white text-sm transition-colors">Tentang
                            Kami</a>
                        <a href="#"
                            class="block text-white hover:text-white text-sm transition-colors">Kontak</a>
                        <a href="#"
                            class="block text-white hover:text-white text-sm transition-colors">Syarat &
                            Ketentuan</a>
                        <a href="#"
                            class="block text-white hover:text-white text-sm transition-colors">Kebijakan
                            Privasi</a>
                    </nav>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Hubungi Kami</h4>
                    <div class="space-y-2">
                        <a href="#"
                            class="flex items-center text-white hover:text-white text-sm transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.65-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z" />
                            </svg>
                            Instagram
                        </a>
                        <a href="#"
                            class="flex items-center text-white hover:text-white text-sm transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                            </svg>
                            WhatsApp Grup
                        </a>
                        <a href="mailto:info@rumahusaha.id"
                            class="flex items-center text-white hover:text-white text-sm transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Email
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-white mt-8 pt-8 text-center">
                <p class="text-white text-sm">
                    © {{ date('Y') }} RumahUsaha.id - Membangun Ekonomi Bersama. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    {{-- Scripts --}}
    <script>
        // Simple mobile menu toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const button = menu.previousElementSibling.querySelector('button');
            const isOpen = !menu.classList.contains('hidden');

            if (isOpen) {
                menu.classList.add('hidden');
                button.setAttribute('aria-expanded', 'false');
            } else {
                menu.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');
            }
        }

        function closeMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const button = menu.previousElementSibling.querySelector('button');
            menu.classList.add('hidden');
            button.setAttribute('aria-expanded', 'false');
        }

        // User menu toggle
        function toggleUserMenu() {
            const menu = document.getElementById('user-menu');
            menu.classList.toggle('hidden');
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(event) {
            const mobileMenu = document.getElementById('mobile-menu');
            const mobileButton = document.querySelector('[aria-controls="mobile-menu"]');
            const userMenu = document.getElementById('user-menu');
            const userButton = document.querySelector('[onclick="toggleUserMenu()"]');

            // Close mobile menu
            if (mobileMenu && mobileButton && !mobileMenu.contains(event.target) && !mobileButton.contains(event
                    .target)) {
                closeMobileMenu();
            }

            // Close user menu
            if (userMenu && userButton && !userMenu.contains(event.target) && !userButton.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    closeMobileMenu();
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
