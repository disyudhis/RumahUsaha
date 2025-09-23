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
    <link href="https://fonts.bunny.net/css?family=acme:400|aleo:500|inter:400,500,600,700&display=swap"
        rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="font-inter-400 bg-fix-300/15 antialiased overflow-x-hidden">
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
                            BIZHOUSE.ID
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
                                    [
                                        'route' => 'news.*',
                                        'label' => 'Berita & Kegiatan Komunitas',
                                        'anchor' => '#news-activities',
                                        'dropdown' => [
                                            'berita-terkini' => [
                                                'label' => 'Berita Terkini',
                                                'items' => [
                                                    [
                                                        'route' => 'news.info-komunitas',
                                                        'label' => 'Info Terbaru Komunitas',
                                                    ],
                                                    [
                                                        'route' => 'news.rilis-media',
                                                        'label' => 'Rilis Media & Publikasi',
                                                    ],
                                                    ['route' => 'news.pengumuman', 'label' => 'Pengumuman Penting'],
                                                ],
                                            ],
                                            'agenda-event' => [
                                                'label' => 'Agenda & Event',
                                                'items' => [
                                                    [
                                                        'route' => 'news.jadwal-kegiatan',
                                                        'label' => 'Jadwal Kegiatan Mendatang',
                                                    ],
                                                    [
                                                        'route' => 'news.workshop-pelatihan',
                                                        'label' => 'Workshop & Pelatihan',
                                                    ],
                                                    [
                                                        'route' => 'news.bazaar-pameran',
                                                        'label' => 'Bazaar & Pameran Produk',
                                                    ],
                                                    [
                                                        'route' => 'news.gathering-networking',
                                                        'label' => 'Gathering & Networking',
                                                    ],
                                                ],
                                            ],
                                            'liputan-kegiatan' => [
                                                'label' => 'Liputan Kegiatan',
                                                'items' => [
                                                    ['route' => 'news.laporan-kegiatan', 'label' => 'Laporan Kegiatan'],
                                                    ['route' => 'news.galeri-foto', 'label' => 'Galeri Foto'],
                                                    ['route' => 'news.galeri-video', 'label' => 'Galeri Video'],
                                                ],
                                            ],
                                            'profil-inspirasi' => [
                                                'label' => 'Profil & Inspirasi Anggota',
                                                'items' => [
                                                    ['route' => 'news.kisah-sukses', 'label' => 'Kisah Sukses UMKM'],
                                                    [
                                                        'route' => 'news.profil-unggulan',
                                                        'label' => 'Profil Anggota Unggulan',
                                                    ],
                                                    [
                                                        'route' => 'news.tips-praktik',
                                                        'label' => 'Tips & Praktik Terbaik',
                                                    ],
                                                ],
                                            ],
                                            'kolaborasi-kemitraan' => [
                                                'label' => 'Kolaborasi & Kemitraan',
                                                'items' => [
                                                    [
                                                        'route' => 'news.program-bersama',
                                                        'label' => 'Program Bersama Pemerintah/Swasta',
                                                    ],
                                                    [
                                                        'route' => 'news.kerja-sama',
                                                        'label' => 'Kerja Sama Komunitas Lain',
                                                    ],
                                                    [
                                                        'route' => 'news.csr-sponsor',
                                                        'label' => 'CSR & Dukungan Sponsor',
                                                    ],
                                                ],
                                            ],
                                            'arsip-berita' => [
                                                'label' => 'Arsip Berita',
                                                'items' => [
                                                    [
                                                        'route' => 'news.kategori',
                                                        'label' => 'Kategori (Pelatihan, Bazaar, Sosial, dll.)',
                                                    ],
                                                    [
                                                        'route' => 'news.arsip-tahun',
                                                        'label' => 'Berdasarkan Tahun/Bulan',
                                                    ],
                                                ],
                                            ],
                                        ],
                                    ],
                                    [
                                        'route' => 'formulir-pendaftaran.*',
                                        'label' => 'Gabung',
                                        'anchor' => '#join-community',
                                    ],
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
                                @if (isset($item['dropdown']))
                                    {{-- Menu dengan dropdown --}}
                                    <div class="relative group">
                                        <button
                                            class="@if (request()->routeIs($item['route'])) bg-primary-50 text-primary-700 border-primary-200 @else text-secondary-700 hover:text-primary-600 hover:bg-accent-50 border-transparent @endif px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border flex items-center"
                                            onmouseover="showDropdown('{{ $item['route'] }}')"
                                            onmouseout="hideDropdown('{{ $item['route'] }}')"
                                            @if ($item['route'] === request()->route()->getName()) aria-current="page" @endif>
                                            {{ $item['label'] }}
                                            <svg class="ml-1 w-4 h-4 transform group-hover:rotate-180 transition-transform duration-200"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>

                                        {{-- Mega Dropdown --}}
                                        <div id="dropdown-{{ $item['route'] }}"
                                            class="absolute left-0 mt-2 w-screen max-w-4xl bg-white rounded-xl shadow-warm-lg border border-accent-100 opacity-0 invisible transform translate-y-2 transition-all duration-300 group-hover:opacity-100 group-hover:visible group-hover:translate-y-0 z-50"
                                            onmouseenter="showDropdown('{{ $item['route'] }}')"
                                            onmouseleave="hideDropdown('{{ $item['route'] }}')">
                                            <div class="p-6">
                                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                                    @foreach ($item['dropdown'] as $sectionKey => $section)
                                                        <div class="space-y-3">
                                                            <h4
                                                                class="font-semibold text-secondary-800 text-sm border-b border-accent-100 pb-2 mb-3">
                                                                {{ $section['label'] }}
                                                            </h4>
                                                            <div class="space-y-2">
                                                                @foreach ($section['items'] as $subItem)
                                                                    <a href="#"
                                                                        class="block text-sm text-secondary-600 hover:text-primary-600 hover:bg-primary-50 px-3 py-2 rounded-lg transition-all duration-200 group/item">
                                                                        <div class="flex items-start">
                                                                            <svg class="w-3 h-3 mt-0.5 mr-2 text-primary-400 opacity-0 group-hover/item:opacity-100 transition-opacity"
                                                                                fill="currentColor" viewBox="0 0 20 20">
                                                                                <path fill-rule="evenodd"
                                                                                    d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                                                    clip-rule="evenodd" />
                                                                            </svg>
                                                                            <span>{{ $subItem['label'] }}</span>
                                                                        </div>
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>

                                                {{-- Footer dalam dropdown --}}
                                                <div class="border-t border-accent-100 mt-6 pt-4">
                                                    <div class="flex items-center justify-between">
                                                        <div class="text-sm text-secondary-500">
                                                            Temukan semua informasi terbaru tentang komunitas UMKM
                                                        </div>
                                                        <a href="#"
                                                            class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 transition-colors">
                                                            Lihat Semua
                                                            <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M9 5l7 7-7 7" />
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    {{-- Menu biasa --}}
                                    <a href="{{ $item['anchor'] ?? ($item['route'] !== 'contact' ? route('home') . $item['anchor'] : '#') }}"
                                        class="@if (request()->routeIs($item['route'])) bg-primary-50 text-primary-700 border-primary-200 @else text-secondary-700 hover:text-primary-600 hover:bg-accent-50 border-transparent @endif px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border"
                                        @if ($item['route'] === request()->route()->getName()) aria-current="page" @endif>
                                        {{ $item['label'] }}
                                    </a>
                                @endif
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
                            @if (isset($item['dropdown']))
                                {{-- Mobile dropdown menu --}}
                                <div class="space-y-1">
                                    <button
                                        class="@if (request()->routeIs($item['route'])) bg-primary-50 text-primary-700 @else text-secondary-700 hover:text-primary-600 hover:bg-accent-50 @endif w-full text-left px-3 py-2 rounded-lg text-base font-medium transition-colors flex items-center justify-between"
                                        onclick="toggleMobileDropdown('mobile-{{ $item['route'] }}')">
                                        {{ $item['label'] }}
                                        <svg class="w-5 h-5 transform transition-transform"
                                            id="icon-mobile-{{ $item['route'] }}" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div class="hidden pl-4 space-y-1" id="mobile-{{ $item['route'] }}">
                                        @foreach ($item['dropdown'] as $sectionKey => $section)
                                            <div class="py-2">
                                                <div
                                                    class="text-xs font-semibold text-secondary-500 uppercase tracking-wider mb-2 px-3">
                                                    {{ $section['label'] }}
                                                </div>
                                                @foreach ($section['items'] as $subItem)
                                                    <a href="#"
                                                        class="block px-3 py-1.5 text-sm text-secondary-600 hover:text-primary-600 hover:bg-primary-50 rounded transition-colors">
                                                        {{ $subItem['label'] }}
                                                    </a>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                {{-- Regular mobile menu item --}}
                                <a href="{{ $item['anchor'] ?? ($item['route'] !== 'contact' ? route('home') . $item['anchor'] : '#') }}"
                                    class="@if (request()->routeIs($item['route'])) bg-primary-50 text-primary-700 @else text-secondary-700 hover:text-primary-600 hover:bg-accent-50 @endif block px-3 py-2 rounded-lg text-base font-medium transition-colors"
                                    onclick="closeMobileMenu()">
                                    {{ $item['label'] }}
                                </a>
                            @endif
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
    <footer class="bg-gradient-to-br from-secondary-900 via-fix-100 to-secondary-800 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Brand Section -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 bg-gradient-to-r from-primary-500 to-fix-400 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold tracking-wide">BIZHOUSE.ID</h3>
                    </div>

                    <div class="space-y-4">
                        <p class="text-gray-300 text-sm leading-relaxed max-w-lg">
                            <span class="font-semibold text-white">BizHouse.id</span> adalah etalase digital khusus
                            untuk komunitas UMKM dan usaha rumahan, terutama di lingkungan perumahan. Platform ini
                            memudahkan pelaku usaha menampilkan profil bisnis dan produk dalam satu tempat yang mudah
                            dijangkau pembeli.
                        </p>

                        <div class="bg-secondary-800/50 p-4 rounded-lg border border-secondary-700">
                            <p class="text-gray-300 text-sm leading-relaxed">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary-600 text-white mr-2 mb-1">
                                    Visi Kami
                                </span>
                                Sebagai ruang promosi dan kolaborasi, BizHouse.id memperluas pasar, memperkuat jejaring
                                melalui pelatihan dan pemasaran bersama, serta menghadirkan manfaat ekonomi dan sosial
                                yang berkelanjutan.
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span
                                class="px-3 py-1 bg-primary-600/20 text-primary-300 rounded-full text-xs font-medium border border-primary-500/30">
                                #UMKM
                            </span>
                            <span
                                class="px-3 py-1 bg-accent-600/20 text-accent-300 rounded-full text-xs font-medium border border-accent-500/30">
                                #UsahaRumahan
                            </span>
                            <span
                                class="px-3 py-1 bg-fix-400/20 text-fix-200 rounded-full text-xs font-medium border border-fix-400/30">
                                #EkonomiDigital
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Menu Section -->
                <div class="space-y-6">
                    <h4 class="font-semibold text-lg text-white border-b border-secondary-700 pb-2">
                        Menu Utama
                    </h4>
                    <nav class="space-y-3" aria-label="Footer navigation">
                        <a href="#"
                            class="flex items-center text-gray-300 hover:text-white text-sm transition-all duration-200 hover:translate-x-1 group">
                            <svg class="w-4 h-4 mr-2 text-primary-400 group-hover:text-primary-300"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 4a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1V8zm8 0a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1V8z"
                                    clip-rule="evenodd" />
                            </svg>
                            Syarat & Ketentuan
                        </a>
                        <a href="#"
                            class="flex items-center text-gray-300 hover:text-white text-sm transition-all duration-200 hover:translate-x-1 group">
                            <svg class="w-4 h-4 mr-2 text-primary-400 group-hover:text-primary-300"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M3 6a3 3 0 013-3h10a1 1 0 01.8 1.6L14.25 8l2.55 3.4A1 1 0 0116 13H6a1 1 0 00-1 1v3a1 1 0 11-2 0V6z"
                                    clip-rule="evenodd" />
                            </svg>
                            Kebijakan Privasi
                        </a>
                    </nav>
                </div>

                <!-- Contact Section -->
                <div class="space-y-6">
                    <h4 class="font-semibold text-lg text-white border-b border-secondary-700 pb-2">
                        Hubungi Kami
                    </h4>
                    <div class="space-y-4">
                        <a href="#"
                            class="flex items-center text-gray-300 hover:text-white text-sm transition-all duration-200 hover:scale-105 group bg-secondary-800/50 p-3 rounded-lg border border-secondary-700 hover:border-primary-500">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-pink-500 to-rose-500 rounded-lg flex items-center justify-center mr-3 group-hover:shadow-lg transition-shadow">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8C4 18.39 5.61 20 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6C20 5.61 18.39 4 16.4 4H7.6m9.65 1.5a1.25 1.25 0 0 1 1.25 1.25A1.25 1.25 0 0 1 17.25 8 1.25 1.25 0 0 1 16 6.75a1.25 1.25 0 0 1 1.65-1.25M12 7a5 5 0 0 1 5 5 5 5 0 0 1-5 5 5 5 0 0 1-5-5 5 5 0 0 1 5-5m0 2a3 3 0 0 0-3 3 3 3 0 0 0 3 3 3 3 0 0 0 3-3 3 3 0 0 0-3-3z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">Instagram</div>
                                <div class="text-xs text-gray-400">-</div>
                            </div>
                        </a>

                        <a href="#"
                            class="flex items-center text-gray-300 hover:text-white text-sm transition-all duration-200 hover:scale-105 group bg-secondary-800/50 p-3 rounded-lg border border-secondary-700 hover:border-accent-500">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3 group-hover:shadow-lg transition-shadow">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">WhatsApp Grup</div>
                                <div class="text-xs text-gray-400">Komunitas UMKM</div>
                            </div>
                        </a>

                        <a href="#"
                            class="flex items-center text-gray-300 hover:text-white text-sm transition-all duration-200 hover:scale-105 group bg-secondary-800/50 p-3 rounded-lg border border-secondary-700 hover:border-fix-400">
                            <div
                                class="w-8 h-8 bg-gradient-to-r from-fix-400 to-primary-600 rounded-lg flex items-center justify-center mr-3 group-hover:shadow-lg transition-shadow">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium">Email</div>
                                <div class="text-xs text-gray-400">info@bizhouse.id</div>
                            </div>
                        </a>
                    </div>

                    <!-- Newsletter -->
                    <div class="bg-secondary-800/50 p-4 rounded-lg border border-secondary-700 mt-6">
                        <h5 class="font-medium text-white mb-2 text-sm">Newsletter</h5>
                        <div class="flex">
                            <input type="email" placeholder="Email Anda"
                                class="flex-1 px-3 py-2 bg-secondary-800 border border-secondary-700 rounded-l-md text-white text-xs focus:outline-none focus:border-primary-500">
                            <button
                                class="px-4 py-2 bg-primary-600 text-white rounded-r-md hover:bg-primary-700 transition-colors text-xs font-medium">
                                Subscribe
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="border-t border-gray-700 mt-12 pt-8">
            <div class="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="text-center md:text-left">
                    <p class="text-gray-400 text-sm">
                        © <span id="current-year"></span> BizHouse.id - Membangun Ekonomi Bersama
                    </p>
                    <p class="text-gray-500 text-xs mt-1">
                        Platform UMKM Terpercaya di Indonesia
                    </p>
                </div>

                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-xs text-gray-400">SSL Secured</span>
                    </div>
                </div>
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

        // Dropdown menu functions
        let dropdownTimeout;

        function showDropdown(menuId) {
            clearTimeout(dropdownTimeout);
            const dropdown = document.getElementById('dropdown-' + menuId);
            if (dropdown) {
                dropdown.classList.remove('opacity-0', 'invisible', 'translate-y-2');
                dropdown.classList.add('opacity-100', 'visible', 'translate-y-0');
            }
        }

        function hideDropdown(menuId) {
            dropdownTimeout = setTimeout(() => {
                const dropdown = document.getElementById('dropdown-' + menuId);
                if (dropdown) {
                    dropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
                    dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                }
            }, 150);
        }

        // Mobile dropdown toggle
        function toggleMobileDropdown(elementId) {
            const dropdown = document.getElementById(elementId);
            const icon = document.getElementById('icon-' + elementId);

            if (dropdown && icon) {
                dropdown.classList.toggle('hidden');
                icon.classList.toggle('rotate-180');
            }
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

            // Close all dropdowns
            const dropdowns = document.querySelectorAll('[id^="dropdown-"]');
            dropdowns.forEach(dropdown => {
                const rect = dropdown.getBoundingClientRect();
                const isInsideDropdown = event.clientX >= rect.left && event.clientX <= rect.right &&
                    event.clientY >= rect.top && event.clientY <= rect.bottom;

                if (!isInsideDropdown) {
                    const menuButton = dropdown.previousElementSibling;
                    if (menuButton && !menuButton.contains(event.target)) {
                        dropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
                        dropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                    }
                }
            });
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

        // Set current year in footer
        document.getElementById('current-year').textContent = new Date().getFullYear();
    </script>

    @stack('scripts')
</body>

</html>
