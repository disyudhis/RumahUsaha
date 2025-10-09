{{-- resources/views/home.blade.php --}}
@extends('layouts.main')

@section('title', 'BIZHOUSE.ID - Platform UMKM')
@section('description', 'Menghubungkan pelaku UMKM dengan jejaring ekonomi sosial yg lebih luas')

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
    <livewire:main.list-product :show-hero="true" :show-all="false" :limit="8" :show-pagination="false" :show-header="true"
        header-title="PRODUK TERBARU" :show-view-all-button="true" view-all-route="main.products.index" />


    {{-- Business Profiles Section --}}
    <section id="list-umkm" class="mb-12 scroll-mt-20">

        <livewire:main.list-umkm :show-all="false" :limit="4" :show-pagination="false" :show-header="true"
            header-title="UMKM Terbaru" :show-view-all-button="true" />
    </section>

    {{-- News & Activities --}}
    <section id="berita-kegiatan" class="scroll-mt-20">
        <livewire:main.list-event :show-all="false" :limit="6" :show-hero="false" :show-search="false"
            :show-category-filter="false" :show-header="true" header-title="Berita & Kegiatan"
            header-subtitle="Informasi terkini seputar event, kolaborasi, dan pengembangan UMKM" />
    </section>

    {{-- Sponsorship & Partnership Section --}}
    <section id="sponsorship-partnership" class="scroll-mt-20">
        <div class="bg-gradient-to-br from-neutral-50 via-white to-accent-50 py-12 md:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {{-- Section Header --}}
                <div class="text-center mb-10">
                    <h2 class="text-3xl md:text-4xl font-bold text-neutral-800 mb-3 font-aleo">
                        Supported By
                    </h2>
                    <div class="mt-4 w-24 h-1 bg-gradient-to-r from-primary-400 to-primary-600 mx-auto rounded-full"></div>
                </div>

                {{-- Sponsor Grid --}}
                <div
                    class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-8 items-center justify-items-center bg-white rounded-2xl shadow-warm-lg p-8 md:p-12">
                    <a href="https://lppm.unisma.ac.id/#">
                        <img src="{{ asset('assets/sponsor-unisma.jpeg') }}" alt="LPPM Unisma"
                            class="max-w-60 max-h-32 object-contain mx-auto">
                    </a>

                    <a href="#">
                        <img src="{{ asset('assets/logo-dinova-long-01.png') }}" alt="Dinova"
                            class="max-w-60 max-h-32 object-contain mx-auto">
                    </a>

                    {{-- Tambah sponsor lain di sini --}}
                </div>
            </div>
        </div>
    </section>


    {{-- Join Community Section --}}
    <section id="join-community" class="text-center scroll-mt-20">
        <div class="bg-gradient-to-br from-fix-400 to-fix-100 p-8 md:p-12 text-white shadow-xl relative overflow-hidden">
            {{-- Decorative Elements --}}
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full transform translate-x-16 -translate-y-16">
            </div>
            <div
                class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-10 rounded-full transform -translate-x-12 translate-y-12">
            </div>

            <livewire:main.gabung />
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

            /* Smooth transitions for hover effects */
            .sponsor-showcase {
                transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            }

            /* Enhanced shadow on hover */
            .shadow-warm-lg:hover {
                box-shadow: 0 20px 35px -5px rgba(251, 146, 60, 0.15), 0 10px 15px -3px rgba(251, 146, 60, 0.1);
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .sponsor-showcase {
                    padding: 2rem 1rem;
                }
            }
        </style>
    @endpush
@endsection
