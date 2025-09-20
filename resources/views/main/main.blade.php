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
    {{-- Hero Section & Product Section Combined --}}
    <section>
        <livewire:main.list-product />
    </section>

    {{-- Business Profiles Section --}}
    <section id="list-umkm" class="mb-12 scroll-mt-20">

        <livewire:main.list-umkm />
    </section>

    {{-- News & Activities --}}
    <section id="berita-kegiatan" class="mb-12 scroll-mt-20">

        <livewire:main.list-event />
    </section>

    {{-- Join Community Section --}}
    <section id="join-community" class="text-center scroll-mt-20">
        <div
            class="bg-gradient-to-br from-fix-400 to-fix-100 p-8 md:p-12 text-white shadow-xl relative overflow-hidden">
            {{-- Decorative Elements --}}
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full transform translate-x-16 -translate-y-16">
            </div>
            <div
                class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-10 rounded-full transform -translate-x-12 translate-y-12">
            </div>

            <div class="relative z-10">
                {{-- Main Header --}}
                <div class="mb-8">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-6 backdrop-blur-sm">
                        <span class="text-3xl" aria-hidden="true">🤝</span>
                    </div>
                    <h2 class="text-3xl md:text-4xl font-aleo font-bold mb-4">
                        GABUNG SEBAGAI ANGGOTA
                    </h2>
                    <p class="text-lg text-primary-100 mb-8 max-w-3xl mx-auto leading-relaxed">
                        Bergabunglah dengan komunitas UMKM dan kembangkan usaha Anda bersama kami!
                        Platform digital untuk mempromosikan dan menghubungkan pelaku UMKM di lingkungan perumahan.
                    </p>
                </div>

                {{-- Website Purpose & Description --}}
                <div class="bg-white bg-opacity-10 rounded-2xl p-6 md:p-8 mb-8 backdrop-blur-sm">
                    <h3 class="text-2xl font-bold text-white mb-6">Tujuan Website</h3>
                    <div class="grid md:grid-cols-3 gap-6 text-left">
                        <div class="bg-white bg-opacity-15 rounded-xl p-6">
                            <div class="w-12 h-12 bg-accent-500 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                            </div>
                            <p class="text-white text-sm leading-relaxed">
                                <strong>Profil Usaha & Produk:</strong> Menyediakan profil usaha dan produk masing-masing
                                anggota komunitas.
                            </p>
                        </div>

                        <div class="bg-white bg-opacity-15 rounded-xl p-6">
                            <div class="w-12 h-12 bg-secondary-600 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M7 4V2C7 1.45 7.45 1 8 1S9 1.45 9 2V4H15V2C15 1.45 15.45 1 16 1S17 1.45 17 2V4H20C21.1 4 22 4.9 22 6V20C22 21.1 21.1 22 20 22H4C2.9 22 2 21.1 2 20V6C2 4.9 2.9 4 4 4H7Z" />
                                </svg>
                            </div>
                            <p class="text-white text-sm leading-relaxed">
                                <strong>Media Informasi & Promosi:</strong> Memperluas jangkauan pasar lokal hingga
                                nasional.
                            </p>
                        </div>

                        <div class="bg-white bg-opacity-15 rounded-xl p-6">
                            <div class="w-12 h-12 bg-primary-400 rounded-lg flex items-center justify-center mb-4">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H17l-2.99 4.01V16h2v6h4zm-7.5-10.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5S11 9.17 11 10s.67 1.5 1.5 1.5z" />
                                </svg>
                            </div>
                            <p class="text-white text-sm leading-relaxed">
                                <strong>Jejaring & Kolaborasi:</strong> Memperkuat jejaring antar pelaku usaha melalui
                                pelatihan dan pemasaran bersama.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Benefits Section --}}
                <div class="bg-white bg-opacity-10 rounded-2xl p-6 md:p-8 mb-8 backdrop-blur-sm">
                    <h3 class="text-2xl font-bold text-white mb-6">Manfaat Bergabung</h3>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-accent-400 to-accent-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-white mb-2">Etalase Digital</h4>
                            <p class="text-primary-100 text-sm leading-relaxed">
                                Etalase digital usaha rumahan yang mudah diakses oleh masyarakat umum
                            </p>
                        </div>

                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-secondary-500 to-secondary-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-white mb-2">Eksposur Produk</h4>
                            <p class="text-primary-100 text-sm leading-relaxed">
                                Meningkatkan eksposur produk lokal dari skala perumahan ke pasar yang lebih luas
                            </p>
                        </div>

                        <div class="text-center">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-primary-400 to-primary-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-white mb-2">Kolaborasi & Pemberdayaan</h4>
                            <p class="text-primary-100 text-sm leading-relaxed">
                                Mendorong semangat kolaborasi dan pemberdayaan ekonomi lokal
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Call to Action Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                    <a href="{{ route('formulir-pendaftaran.index') }}" type="button"
                        class="inline-flex items-center bg-white text-primary-700 px-8 py-4 rounded-lg hover:bg-gray-50 font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Formulir Pendaftaran Digital
                    </a>

                    {{-- <button type="button"
                        class="inline-flex items-center border-2 border-white text-white px-8 py-4 rounded-lg hover:bg-white hover:text-primary-700 font-medium transition-all duration-300"
                        onclick="alert('Informasi lebih lanjut akan segera tersedia!')">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Pelajari Lebih Lanjut
                    </button> --}}
                </div>
            </div>
        </div>
    </section>
    @push('styles')
        <style>
            /* Additional styles for improved join section */
            .hero-gradient {
                background: linear-gradient(135deg, #ea580c 0%, #c2410c 50%, #9a3412 100%);
            }

            .card-hover {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .card-hover:hover {
                transform: translateY(-2px);
            }

            /* Backdrop blur fallback for older browsers */
            .backdrop-blur-sm {
                backdrop-filter: blur(4px);
                -webkit-backdrop-filter: blur(4px);
            }

            /* Smooth animations for buttons */
            button {
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            button:hover {
                transform: translateY(-1px);
            }
        </style>
    @endpush
@endsection
