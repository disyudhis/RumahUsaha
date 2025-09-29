{{-- resources/views/home.blade.php --}}
@extends('layouts.main')

@section('title', 'BIZHOUSE.ID - Platform UMKM Perumahan')
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
<livewire:main.list-product :show-hero="true" :show-all="false" :limit="8" :show-pagination="false" :show-header="true"
    header-title="PRODUK TERBARU" :show-view-all-button="true" view-all-route="main.products.index" />


{{-- Business Profiles Section --}}
<section id="list-umkm" class="mb-12 scroll-mt-20">

    <livewire:main.list-umkm :show-all="false" :limit="4" :show-pagination="false" :show-header="true"
        header-title="UMKM Terbaru" :show-view-all-button="true" />
</section>

{{-- News & Activities --}}
<section id="berita-kegiatan" class="mb-12 scroll-mt-20">

    <livewire:main.list-event />
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
</style>
@endpush
@endsection