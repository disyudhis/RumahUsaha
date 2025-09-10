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
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-inter bg-gray-50 antialiased">
    {{-- Skip to content for accessibility --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-blue-600 text-white px-4 py-2 rounded">
        Skip to main content
    </a>

    {{-- Header Navigation --}}
    @auth
        <livewire:layout.main-navigation />
    @else
        <header class="bg-white shadow-sm sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    {{-- Logo --}}
                    <div class="flex-shrink-0">
                        <a href="{{ route('home') }}"
                            class="text-xl font-bold text-gray-900 hover:text-blue-600 transition-colors">
                            RumahUsaha.id
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
                                    ['route' => 'membership.*', 'label' => 'Gabung', 'anchor' => '#join-community'],
                                    ['route' => 'contact', 'label' => 'Kontak', 'anchor' => null],
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
                                    class="@if (request()->routeIs($item['route'])) bg-blue-50 text-blue-700 @else text-gray-700 hover:text-blue-600 hover:bg-gray-50 @endif px-3 py-2 rounded-md text-sm font-medium transition-all duration-200"
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
                                        class="flex items-center text-sm font-medium text-gray-700 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 rounded-full"
                                        onclick="toggleUserMenu()" aria-expanded="false" aria-haspopup="true">
                                        <div
                                            class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-2">
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
                                    <div class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5"
                                        id="user-menu">
                                        {{-- <div class="px-4 py-2 text-xs text-gray-500 border-b">
                                            {{ ucfirst(str_replace('_', ' ', auth()->user()->user_type)) }}
                                        </div>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <span class="mr-2">👤</span>
                                            Profil Saya
                                        </a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <span class="mr-2">⚙️</span>
                                            Pengaturan
                                        </a> --}}
                                        <div class="border-t border-gray-100"></div>
                                        <button wire:click="logout"
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                            <span class="mr-2">🚪</span>
                                            Logout
                                        </button>
                                    </div>
                                </div>
                            @endif
                        @endauth

                        {{-- Mobile menu button --}}
                        <button type="button"
                            class="md:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 transition-colors"
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
                    <div class="px-2 pt-2 pb-3 space-y-1 border-t border-gray-200">
                        @foreach ($navItems as $item)
                            <a href="{{ $item['anchor'] ?? ($item['route'] !== 'contact' ? route('home') . $item['anchor'] : '#') }}"
                                class="@if (request()->routeIs($item['route'])) bg-blue-50 text-blue-700 @else text-gray-700 hover:text-blue-600 hover:bg-gray-50 @endif block px-3 py-2 rounded-md text-base font-medium transition-colors"
                                onclick="closeMobileMenu()">
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        {{-- Mobile User Menu --}}
                        @auth
                            @if (in_array(auth()->user()->user_type, ['admin', 'pemilik_umkm']))
                                <div class="border-t border-gray-200 pt-4 mt-4">
                                    <div class="px-3 py-2">
                                        <div class="flex items-center mb-3">
                                            <div
                                                class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mr-3">
                                                <span class="text-sm font-semibold">
                                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <div class="text-base font-medium text-gray-800">{{ auth()->user()->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ ucfirst(str_replace('_', ' ', auth()->user()->user_type)) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- <a href="#"
                                        class="block px-3 py-2 text-base text-gray-700 hover:text-blue-600 hover:bg-gray-50"
                                        onclick="closeMobileMenu()">
                                        <span class="mr-2">👤</span>
                                        Profil Saya
                                    </a>
                                    <a href="#"
                                        class="block px-3 py-2 text-base text-gray-700 hover:text-blue-600 hover:bg-gray-50"
                                        onclick="closeMobileMenu()">
                                        <span class="mr-2">⚙️</span>
                                        Pengaturan
                                    </a> --}}
                                    <button wire:click="logout"
                                        class="w-full text-left px-3 py-2 text-base text-red-600 hover:bg-red-50">
                                        <span class="mr-2">🚪</span>
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
        @if (isset($hero) && $hero)
            {{-- Hero Section --}}
            <section class="bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    @yield('hero')
                </div>
            </section>
        @endif

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold mb-4">RumahUsaha.id</h3>
                    <p class="text-gray-300 text-sm leading-relaxed max-w-md">
                        Platform digital untuk mempromosikan dan menghubungkan pelaku UMKM di lingkungan perumahan.
                        Membangun ekonomi bersama melalui kolaborasi dan inovasi.
                    </p>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Menu</h4>
                    <nav class="space-y-2" aria-label="Footer navigation">
                        <a href="#" class="block text-gray-300 hover:text-white text-sm transition-colors">Tentang
                            Kami</a>
                        <a href="#"
                            class="block text-gray-300 hover:text-white text-sm transition-colors">Kontak</a>
                        <a href="#" class="block text-gray-300 hover:text-white text-sm transition-colors">Syarat
                            & Ketentuan</a>
                        <a href="#"
                            class="block text-gray-300 hover:text-white text-sm transition-colors">Kebijakan
                            Privasi</a>
                    </nav>
                </div>

                <div>
                    <h4 class="font-semibold mb-4">Hubungi Kami</h4>
                    <div class="space-y-2">
                        <a href="#"
                            class="flex items-center text-gray-300 hover:text-white text-sm transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12.017 11.215c1.287-2.013 2.4-3.744 3.378-5.168l-.618-.379C13.666 7.064 12.4 8.852 11.017 10.935c-1.33-2.04-2.564-3.8-3.665-5.227l-.618.379c.978 1.424 2.091 3.155 3.378 5.168C8.849 9.251 7.583 7.463 6.472 5.668l-.618.379C6.832 7.371 7.945 9.102 9.232 11.115c-.978 1.529-1.848 2.847-2.618 3.972h1.236c.678-1.018 1.466-2.224 2.384-3.635 1.01 1.579 2.003 2.982 2.967 4.228h1.236c-.97-1.125-1.84-2.443-2.618-3.972l.198-.493z" />
                            </svg>
                            Instagram
                        </a>
                        <a href="#"
                            class="flex items-center text-gray-300 hover:text-white text-sm transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                            </svg>
                            WhatsApp Grup
                        </a>
                        <a href="mailto:info@rumahusaha.id"
                            class="flex items-center text-gray-300 hover:text-white text-sm transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Email
                        </a>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-300 text-sm">
                    © {{ date('Y') }} RumahUsaha.id - Membangun Ekonomi Bersama. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    {{-- Scripts --}}
    <script>
        // Simple mobile menu toggle without Alpine.js
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
