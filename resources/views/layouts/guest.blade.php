<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .float-animation {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes gradient-shift {

            0%,
            100% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }
        }

        .gradient-animate {
            background-size: 200% 200%;
            animation: gradient-shift 15s ease infinite;
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fade-in-up 0.6s ease-out forwards;
        }

        .delay-1 {
            animation-delay: 0.1s;
            opacity: 0;
        }

        .delay-2 {
            animation-delay: 0.2s;
            opacity: 0;
        }

        .delay-3 {
            animation-delay: 0.3s;
            opacity: 0;
        }

        .delay-4 {
            animation-delay: 0.4s;
            opacity: 0;
        }
    </style>
</head>

<body class="font-inter antialiased">
    <div class="min-h-screen flex">
        <!-- Left Side - Decorative & Features -->
        <div
            class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-primary-400 via-primary-500 to-primary-600 gradient-animate relative overflow-hidden">
            <div
                class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KICA8ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPgogICAgPGcgZmlsbD0iI2ZmZmZmZiIgZmlsbC1vcGFjaXR5PSIwLjEiPgogICAgICA8cGF0aCBkPSJNMzYgMzRjMC0yLjIxLTEuNzktNC00LTRzLTQgMS43OS00IDQgMS43OSA0IDQgNCA0LTEuNzkgNC00em0wLTEwYzAtMi4yMS0xLjc5LTQtNC00cy00IDEuNzktNCA0IDEuNzkgNCA0IDQgNC0xLjc5IDQtNHptMC0xMGMwLTIuMjEtMS43OS00LTQtNHMtNCAxLjc5LTQgNCAxLjc5IDQgNCA0IDQtMS43OSA0LTR6bTAtMTBjMC0yLjIxLTEuNzktNC00LTRzLTQgMS43OS00IDQgMS43OSA0IDQgNCA0LTEuNzkgNC00eiIvPgogICAgPC9nPgogIDwvZz4KPC9zdmc+')] opacity-30">
            </div>

            <div class="relative z-10 flex flex-col justify-center w-full px-12 text-white">
                <div class="float-animation mb-8">
                    <svg class="w-20 h-20 mx-auto" viewBox="0 0 24 24" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M3 9L12 2L21 9V20C21 20.5304 20.7893 21.0391 20.4142 21.4142C20.0391 21.7893 19.5304 22 19 22H5C4.46957 22 3.96086 21.7893 3.58579 21.4142C3.21071 21.0391 3 20.5304 3 20V9Z"
                            fill="currentColor" opacity="0.9" />
                        <path d="M9 22V12H15V22" fill="currentColor" opacity="0.7" />
                    </svg>
                </div>

                <h1 class="text-4xl font-bold mb-3 text-center">BIZHOUSE.ID</h1>
                <p class="text-lg text-center text-primary-50 max-w-md mx-auto mb-12">
                    Kelola dan kembangkan bisnis UMKM Anda dengan mudah dalam satu platform terpadu
                </p>

                <!-- Features Section -->
                <div class="space-y-6 max-w-lg mx-auto">
                    <div class="text-center mb-6">
                        <h3 class="text-xl font-semibold mb-2">Fitur Dashboard UMKM</h3>
                        <p class="text-sm text-primary-100">Semua yang Anda butuhkan untuk mengelola bisnis</p>
                    </div>

                    <div class="space-y-4">
                        <!-- Feature 1 -->
                        <div
                            class="flex items-start space-x-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 fade-in-up delay-1">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold mb-1">Tambah Produk</h4>
                                <p class="text-sm text-primary-100">Unggah produk baru dengan foto, deskripsi, dan harga
                                </p>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div
                            class="flex items-start space-x-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 fade-in-up delay-2">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold mb-1">Edit Produk</h4>
                                <p class="text-sm text-primary-100">Perbarui informasi produk kapan saja</p>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div
                            class="flex items-start space-x-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 fade-in-up delay-3">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold mb-1">Hapus Produk</h4>
                                <p class="text-sm text-primary-100">Kelola katalog dengan menghapus produk yang tidak
                                    tersedia</p>
                            </div>
                        </div>

                        <!-- Feature 4 -->
                        <div
                            class="flex items-start space-x-4 bg-white/10 backdrop-blur-sm rounded-xl p-4 fade-in-up delay-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-semibold mb-1">Edit Profil UMKM</h4>
                                <p class="text-sm text-primary-100">Atur informasi bisnis, kontak, dan lokasi UMKM Anda
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div
            class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gradient-to-br from-neutral-50 to-accent-50">
            <div class="w-full max-w-md">
                <!-- Logo for mobile -->
                <div class="lg:hidden text-center mb-8">
                    <a href="/" wire:navigate class="inline-block">
                        <x-application-logo class="w-16 h-16 fill-current text-primary-500 mx-auto" />
                    </a>
                    <h2 class="text-2xl font-bold text-neutral-900 mt-4">Portal UMKM</h2>
                    <p class="text-neutral-600 mt-1">Kelola bisnis Anda dengan mudah</p>
                </div>

                <!-- Login Card -->
                <div class="bg-white rounded-2xl shadow-warm-xl p-8 border border-neutral-100">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-neutral-900 mb-2">Masuk ke Dashboard</h2>
                        <p class="text-neutral-600">Masukkan kredensial UMKM Anda untuk melanjutkan</p>
                    </div>

                    {{ $slot }}

                    <!-- Info Footer -->
                    <div class="mt-6 pt-6 border-t border-neutral-100">
                        <p class="text-xs text-center text-neutral-500">
                            Dengan masuk, Anda dapat mengelola produk dan profil UMKM Anda
                        </p>
                    </div>
                </div>

                <!-- Additional Info for Mobile -->
                <div class="lg:hidden mt-6 text-center">
                    <p class="text-sm text-neutral-600 mb-4">Fitur yang tersedia:</p>
                    <div class="flex flex-wrap justify-center gap-2">
                        <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-medium">
                            Tambah Produk
                        </span>
                        <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-medium">
                            Edit Produk
                        </span>
                        <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-medium">
                            Hapus Produk
                        </span>
                        <span class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-medium">
                            Edit Profil
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
