{{-- resources/views/home.blade.php --}}
@extends('layouts.main')

@section('title', 'RumahUsaha.id - Platform UMKM Perumahan')
@section('description', 'Platform digital untuk mempromosikan dan menghubungkan pelaku UMKM di lingkungan perumahan')

@push('styles')
    <style>
        /* Custom styles for home page */
        .hero-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
@endpush

@section('content')
    {{-- Hero Section --}}
    <section class="mb-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Section - Upload Business Photo --}}
            <div class="lg:col-span-1">
                <div
                    class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-300 transition-colors">
                    <div class="mb-4">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48"
                            aria-hidden="true">
                            <path
                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">📸 FOTO USAHA UNGGULAN</h3>
                    <p class="text-sm text-gray-500 mb-4">& SLOGAN KOMUNITAS</p>
                    <div class="text-sm text-gray-600">
                        Logo/Gambar Komunitas
                    </div>
                </div>
            </div>

            {{-- Right Section - Main Banner --}}
            <div class="lg:col-span-2">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-lg p-6 shadow-sm">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">SLIDER BANNER UTAMA</h2>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center text-green-600">
                            <span class="mr-2 text-lg" aria-hidden="true">✅</span>
                            <span class="text-sm">Info komunitas & kegiatan</span>
                        </div>
                        <div class="flex items-center text-green-600">
                            <span class="mr-2 text-lg" aria-hidden="true">✅</span>
                            <span class="text-sm">Foto produk best seller</span>
                        </div>
                        <div class="flex items-center text-green-600">
                            <span class="mr-2 text-lg" aria-hidden="true">✅</span>
                            <span class="text-sm">Testimoni pelanggan</span>
                        </div>
                    </div>
                    <div class="bg-white rounded-lg p-8 text-center text-gray-500 border border-gray-200">
                        <div class="space-y-2">
                            <div class="w-full h-32 bg-gray-100 rounded mb-4 flex items-center justify-center">
                                <span class="text-gray-400">Area Slider/Carousel Banner</span>
                            </div>
                            <div class="flex justify-center space-x-2">
                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                                <div class="w-2 h-2 bg-gray-300 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Product Section --}}
    <section id="list-product" class="mb-12 scroll-mt-20">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Produk Unggulan</h2>
            {{-- <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Lihat Semua →
            </a> --}}
        </div>
        <livewire:main.list-product />
    </section>

    {{-- Business Profiles Section --}}
    <section id="list-umkm" class="mb-12 scroll-mt-20">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Profil UMKM</h2>
            {{-- <a href="#" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                Lihat Semua →
            </a> --}}
        </div>
        <livewire:main.list-umkm />
    </section>

    {{-- News & Activities --}}
    <livewire:main.list-event />

    {{-- Join Community Section --}}
    <section id="join-community" class="text-center scroll-mt-20">
        <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-xl p-8 text-white shadow-xl">
            <h2 class="text-2xl font-bold mb-4 flex items-center justify-center">
                <span class="mr-3 text-2xl" aria-hidden="true">🤝</span>
                GABUNG SEBAGAI ANGGOTA
            </h2>
            <p class="text-blue-100 mb-6 max-w-2xl mx-auto leading-relaxed">
                Bergabunglah dengan komunitas UMKM dan kembangkan usaha Anda bersama kami!
                Dapatkan akses ke pelatihan, networking, dan peluang bisnis yang menguntungkan.
            </p>
            <button type="button"
                class="inline-flex items-center bg-white text-blue-700 px-8 py-3 rounded-lg hover:bg-gray-50 font-medium transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                onclick="alert('Formulir pendaftaran - implement as needed')">
                <span class="mr-2" aria-hidden="true">📝</span>
                Formulir Pendaftaran Digital
            </button>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        // Additional home page scripts if needed
        console.log('Home page loaded');
    </script>
@endpush
