<?php

// resources/views/livewire/main/list-product.blade.php
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;
use Livewire\Attributes\Url;

new class extends Component {
    use WithPagination;

    #[Url]
    public $search = '';

    #[Url]
    public $category = '';

    // Configurable properties
    public $showHero = true;
    public $showAll = false;
    public $limit = 8;
    public $showPagination = false;
    public $showHeader = true;
    public $headerTitle = 'PRODUK TERBARU';
    public $showViewAllButton = false;
    public $viewAllRoute = 'main.products.index';
    public $perPage = 12;

    public $categories = [
        'kuliner' => '🍽️ Kuliner',
        'fashion' => '👗 Fashion',
        'jasa' => '🛠️ Jasa dan Layanan',
        'kerajinan' => '🎨 Kerajinan dan Seni',
        'kecantikan' => '💄 Kecantikan dan Perawatan Diri',
        'kesehatan' => '🌿 Kesehatan dan Herbal',
        'pariwisata' => '🏛️ Pariwisata dan Kearifan Lokal',
        'pertanian' => '🌾 Komoditas Pertanian dan Peternakan, perkebunan dan perikanan',
        'digital' => '💻 Otomotif, Produk Digital, dan Elektronik',
        'edukasi' => '📚 Edukasi dan Pelatihan',
        'lainnya' => '⭐ Lainnya',
    ];

    // Array untuk gambar hero carousel dengan class positioning
    public $heroImages = [
        [
            'path' => 'assets/bg.jpg',
            'class' => 'hero-img-top',
            'alt' => 'UMKM Hero 1',
        ],
        [
            'path' => 'assets/bg2.jpg',
            'class' => 'hero-img-center',
            'alt' => 'UMKM Hero 2',
        ],
        [
            'path' => 'assets/bg3.jpg',
            'class' => 'hero-img-center',
            'alt' => 'UMKM Hero 3',
        ],
        [
            'path' => 'assets/bg4.jpg',
            'class' => 'hero-img-center',
            'alt' => 'UMKM Hero 4',
        ],
    ];

    public function updatedSearch()
    {
        // Reset category when searching
        $this->reset('category');
        if ($this->showPagination) {
            $this->resetPage();
        }
    }

    public function setCategory($cat)
    {
        $this->category = $cat;
        $this->reset('search');
        if ($this->showPagination) {
            $this->resetPage();
        }
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category']);
        if ($this->showPagination) {
            $this->resetPage();
        }
    }

    public function with()
    {
        $query = Product::with('umkmProfile')->where('is_active', true);

        // Apply search filter
        if ($this->search) {
            $query->search($this->search);
        }

        // Apply category filter
        if ($this->category) {
            $query->category($this->category);
        }

        // Order by latest
        $query->latest();

        // Get products based on configuration
        if ($this->showAll && $this->showPagination) {
            $products = $query->paginate($this->perPage);
        } elseif ($this->showAll) {
            $products = $query->get();
        } else {
            $products = $query->limit($this->limit)->get();
        }

        // Get total count for statistics
        $productsCount = Product::with('umkmProfile')->where('is_active', true)->when($this->search, fn($q) => $q->search($this->search))->when($this->category, fn($q) => $q->category($this->category))->count();

        return [
            'products' => $products,
            'productsCount' => $productsCount,
        ];
    }

    public function showDetailProduct($productId)
    {
        return $this->redirect(route('main.products.show', ['id' => $productId]), navigate: true);
    }

    public function mount($showHero = true, $showAll = false, $limit = 8, $showPagination = false, $showHeader = true, $headerTitle = 'PRODUK TERBARU', $showViewAllButton = false, $viewAllRoute = 'main.products.index', $perPage = 12)
    {
        $this->showHero = $showHero;
        $this->showAll = $showAll;
        $this->limit = $limit;
        $this->showPagination = $showPagination;
        $this->showHeader = $showHeader;
        $this->headerTitle = $headerTitle;
        $this->showViewAllButton = $showViewAllButton;
        $this->viewAllRoute = $viewAllRoute;
        $this->perPage = $perPage;
    }
}; ?>

<div>
    {{-- Hero Section with Image Carousel (Only show if enabled) --}}
    @if ($showHero)
        <div class="relative mb-12">
            <div class="container mx-auto">
                <!-- Hero Section dengan Background Image Carousel -->
                <div class="min-h-[400px] lg:min-h-[700px] overflow-hidden">

                    <!-- Image Carousel -->
                    <div class="hero-carousel absolute inset-0">
                        @foreach ($heroImages as $index => $imageData)
                            <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                                data-slide="{{ $index }}">
                                <img src="{{ asset($imageData['path']) }}" alt="{{ $imageData['alt'] }}"
                                    class="w-full h-full {{ $imageData['class'] }}">
                            </div>
                        @endforeach
                    </div>

                    <!-- Carousel Navigation Dots -->
                    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 flex space-x-3">
                        @foreach ($heroImages as $index => $imageData)
                            <button
                                class="carousel-dot w-3 h-3 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}"
                                data-slide="{{ $index }}"></button>
                        @endforeach
                    </div>

                    <!-- Gradient Overlay -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-fix-200 via-fix-200/60 to-transparent z-10">
                    </div>

                    <!-- Content -->
                    <div class="relative z-20 h-full flex items-center">
                        <div class="w-full lg:w-1/2 p-8 lg:p-16">
                            <h1
                                class="text-4xl font-aleo lg:text-5xl xl:text-6xl font-bold text-white mb-6 leading-tight drop-shadow-lg">
                                Dari Rumah,<br>
                                <span class="text-fix-300">Untuk Negeri</span>
                            </h1>
                            <p class="text-lg lg:text-xl font-acme text-white/90 mb-8 max-w-lg drop-shadow">
                                Etalase Digital UMKM Indonesia
                            </p>

                            <!-- Enhanced Search Form -->
                            <div class="max-w-lg">
                                <div
                                    class="flex shadow-warm-lg rounded-2xl overflow-hidden bg-white/95 backdrop-blur-sm">
                                    <div class="flex-1 relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input type="text" wire:model.live.debounce.300ms="search"
                                            placeholder="Cari produk, UMKM, atau kategori..."
                                            class="w-full pl-12 pr-4 py-4 border-0 focus:ring-0 focus:outline-none text-neutral-700 placeholder-neutral-400 text-base bg-transparent">
                                    </div>
                                    <button
                                        class="bg-fix-100 hover:bg-fix-100/90 text-white px-8 py-4 font-semibold text-base transition-all duration-200 flex items-center gap-2">
                                        <span>Cari</span>
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="px-4 pt-4 sm:px-6 lg:px-8 mx-auto overflox-y-hidden">
        {{-- Enhanced Search Bar for non-hero sections --}}
        @if (!$showHero)
            <div class="mb-8">
                <div class="max-w-2xl mx-auto">
                    <div class="flex shadow-warm-lg rounded-2xl overflow-hidden bg-white">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-neutral-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="Cari produk, UMKM, atau kategori..."
                                class="w-full pl-12 pr-4 py-4 border-0 focus:ring-2 focus:ring-primary-400 focus:outline-none text-neutral-700 placeholder-neutral-400 text-base">
                        </div>
                        <button
                            class="bg-primary-500 hover:bg-primary-600 text-white px-8 py-4 font-semibold text-base transition-all duration-200">
                            Cari
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Simplified Category Filter Buttons --}}
        <div class="flex flex-wrap justify-center gap-2 sm:gap-3 mb-8">
            <button wire:click="clearFilters"
                class="px-4 sm:px-6 py-3 rounded-full text-sm sm:text-base font-medium transition-all duration-200 {{ !$category ? 'bg-primary-500 text-white shadow-warm' : 'bg-white text-neutral-600 hover:bg-primary-50 hover:text-primary-600 border border-neutral-200 hover:border-primary-200' }}">
                🏠 Semua
            </button>
            @foreach ($categories as $key => $label)
                <button wire:click="setCategory('{{ $key }}')"
                    class="px-4 sm:px-6 py-3 rounded-full text-sm sm:text-base font-medium transition-all duration-200 whitespace-nowrap {{ $category === $key ? 'bg-primary-500 text-white shadow-warm' : 'bg-white text-neutral-600 hover:bg-primary-50 hover:text-primary-600 border border-neutral-200 hover:border-primary-200' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Active Filters with improved design --}}
        @if ($search || $category)
            <div class="mb-8">
                <div class="flex flex-wrap items-center gap-3">
                    <span class="text-sm font-medium text-neutral-600">Filter aktif:</span>
                    @if ($search)
                        <div
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm bg-accent-100 text-accent-800 border border-accent-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            "{{ $search }}"
                            <button wire:click="$set('search', '')"
                                class="ml-2 hover:text-accent-900 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                    @if ($category)
                        <div
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm bg-primary-100 text-primary-800 border border-primary-200">
                            {{ $categories[$category] ?? $category }}
                            <button wire:click="$set('category', '')"
                                class="ml-2 hover:text-primary-900 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                    @if ($search || $category)
                        <button wire:click="clearFilters"
                            class="text-sm text-neutral-500 hover:text-neutral-700 underline transition-colors">
                            Hapus semua filter
                        </button>
                    @endif
                </div>
            </div>
        @endif

        {{-- Products Section --}}
        <div class="mb-12">
            {{-- Header --}}
            @if ($showHeader)
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-bold text-neutral-900 mb-2">{{ $headerTitle }}</h2>
                        @if ($search || $category)
                            <p class="text-neutral-600">
                                @if ($search && $category)
                                    Hasil untuk "{{ $search }}" di {{ $categories[$category] ?? $category }}
                                @elseif ($search)
                                    Hasil pencarian untuk "{{ $search }}"
                                @else
                                    Produk di kategori {{ $categories[$category] ?? $category }}
                                @endif
                            </p>
                        @endif
                    </div>
                    @if ($showViewAllButton && !$showAll)
                        <a href="{{ route($viewAllRoute) }}"
                            class="text-primary-600 hover:text-primary-700 text-sm font-medium flex items-center gap-1 group transition-colors">
                            Lihat Semua
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @endif
                </div>
            @endif

            {{-- Enhanced Products Grid --}}
            @if ($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
                    @foreach ($products as $product)
                        <div
                            class="overflow-hidden shadow-warm hover:shadow-warm-lg transition-all duration-300 group border border-neutral-100">
                            {{-- Product Image --}}
                            <div
                                class="relative aspect-square bg-gradient-to-br from-neutral-50 to-neutral-100 overflow-hidden">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="flex items-center justify-center h-full bg-hero-gradient">
                                        <div class="text-center">
                                            <div class="text-5xl mb-3 opacity-40">📸</div>
                                            <span class="text-neutral-400 text-sm font-medium">Foto Produk</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Category Badge --}}
                                <div class="absolute top-3 left-3">
                                    <span
                                        class="bg-white/95 backdrop-blur-sm text-neutral-700 text-xs px-3 py-1.5 rounded-full font-medium shadow-sm border border-white/20">
                                        {{ $categories[$product->category] ?? $product->category }}
                                    </span>
                                </div>

                                {{-- Favorite Button --}}
                                <div class="absolute top-3 right-3">
                                    <button
                                        class="w-8 h-8 bg-white/95 backdrop-blur-sm rounded-full flex items-center justify-center text-neutral-400 hover:text-primary-500 transition-colors shadow-sm border border-white/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Product Info --}}
                            <div class="p-5">
                                <h3
                                    class="font-bold text-neutral-900 mb-2 line-clamp-2 text-base sm:text-lg leading-tight">
                                    {{ $product->name }}</h3>

                                {{-- Business Name --}}
                                @if ($product->umkmProfile)
                                    <p class="text-sm text-neutral-500 mb-3 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                        {{ $product->umkmProfile->business_name }}
                                    </p>
                                @endif

                                {{-- Price --}}
                                <div class="mb-4">
                                    <span class="text-xl font-bold text-primary-600">
                                        Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>

                                {{-- Action Button --}}
                                <button wire:click='showDetailProduct({{ $product->id }})'
                                    class="w-full bg-primary-500 hover:bg-primary-600 text-white font-medium py-3 px-4 rounded-xl transition-all duration-200 group flex items-center justify-center gap-2">
                                    <span>Detail Produk</span>
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if ($showPagination && method_exists($products, 'links'))
                    <div class="mb-8">
                        {{ $products->links() }}
                    </div>
                @endif

                {{-- Show All Products Button --}}
                @if (!$showAll && !$showPagination && $products->count() >= $limit)
                    <div class="text-center">
                        <a href="{{ route($viewAllRoute) }}"
                            class="inline-flex items-center gap-2 bg-white hover:bg-neutral-50 text-neutral-700 font-medium px-8 py-4 rounded-xl border border-neutral-200 transition-all duration-200 shadow-sm hover:shadow-warm group">
                            <span>Lihat Produk Lainnya</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            @else
                {{-- Enhanced Empty State --}}
                <div class="text-center py-20">
                    <div class="max-w-md mx-auto">
                        <div class="text-8xl mb-6 opacity-20">
                            @if ($search)
                                🔍
                            @elseif($category)
                                {{ $categories[$category] ? explode(' ', $categories[$category])[0] : '📦' }}
                            @else
                                📦
                            @endif
                        </div>
                        <h3 class="text-2xl font-bold text-neutral-900 mb-4">
                            @if ($search)
                                Produk tidak ditemukan
                            @elseif($category)
                                Belum ada produk di kategori ini
                            @else
                                Belum ada produk tersedia
                            @endif
                        </h3>
                        <p class="text-neutral-600 mb-8 leading-relaxed">
                            @if ($search)
                                Tidak ada produk yang cocok dengan pencarian "<strong>{{ $search }}</strong>".
                                Coba kata kunci lain atau lihat semua produk.
                            @elseif($category)
                                Kategori <strong>{{ $categories[$category] ?? $category }}</strong> belum memiliki
                                produk. Coba kategori lain atau lihat semua produk.
                            @else
                                Produk dari UMKM akan muncul di sini. Pantau terus untuk update terbaru!
                            @endif
                        </p>
                        @if ($search || $category)
                            <button wire:click="clearFilters"
                                class="inline-flex items-center gap-2 bg-primary-500 hover:bg-primary-600 text-white font-semibold px-8 py-4 rounded-xl transition-all duration-200 shadow-warm group">
                                <span>Lihat Semua Produk</span>
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Products Count Info --}}
            @if ($products->count() > 0)
                <div class="text-center mt-8 pt-6 border-t border-neutral-200">
                    <p class="text-sm text-neutral-500">
                        @if ($showPagination && method_exists($products, 'total'))
                            Menampilkan <span class="font-medium">{{ $products->firstItem() }}</span> - <span
                                class="font-medium">{{ $products->lastItem() }}</span> dari <span
                                class="font-medium">{{ $products->total() }}</span> produk
                        @elseif ($search || $category)
                            Menampilkan <span class="font-medium">{{ $products->count() }}</span> dari <span
                                class="font-medium">{{ $productsCount }}</span> produk
                            @if ($search)
                                untuk "<span class="font-medium">{{ $search }}</span>"
                            @endif
                            @if ($category)
                                di kategori <span class="font-medium">{{ $categories[$category] ?? $category }}</span>
                            @endif
                        @else
                            @if (!$showAll)
                                Menampilkan <span class="font-medium">{{ $products->count() }}</span> produk terbaru
                                dari <span class="font-medium">{{ $productsCount }}</span> total produk
                            @else
                                Menampilkan <span class="font-medium">{{ $products->count() }}</span> produk
                            @endif
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Enhanced Loading State --}}
        <div wire:loading
            class="fixed inset-0 bg-neutral-900/20 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 flex items-center shadow-warm-xl max-w-sm mx-4">
                <svg class="animate-spin h-6 w-6 text-primary-500 mr-4" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <div>
                    <div class="text-neutral-900 font-medium">Memuat produk...</div>
                    <div class="text-neutral-500 text-sm">Mohon tunggu sebentar</div>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($showHero)
    @push('styles')
        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .aspect-square {
                aspect-ratio: 1 / 1;
            }

            .carousel-slide {
                transition: opacity 1s ease-in-out;
            }

            .carousel-dot {
                cursor: pointer;
            }

            .carousel-dot:hover {
                bg-white;
            }

            /* Hero Image Positioning Classes */
            .hero-img-center {
                object-position: center center;
                object-fit: cover;
            }

            .hero-img-top {
                object-position: center top;
                object-fit: cover;
            }

            .hero-img-bottom {
                object-position: center bottom;
            }

            .hero-img-left {
                object-position: left center;
            }

            .hero-img-right {
                object-position: right center;
            }

            .hero-img-top-left {
                object-position: left top;
            }

            .hero-img-top-right {
                object-position: right top;
            }

            .hero-img-bottom-left {
                object-position: left bottom;
            }

            .hero-img-bottom-right {
                object-position: right bottom;
            }

            /* Untuk gambar portrait yang ingin di-crop dengan fokus wajah/objek utama */
            .hero-img-face-focus {
                object-position: center 25%;
            }

            /* Untuk gambar landscape yang terlalu lebar */
            .hero-img-crop-sides {
                object-position: center center;
                object-fit: cover;
            }

            /* Untuk gambar yang ingin dipertahankan aspect ratio tanpa crop */
            .hero-img-contain {
                object-fit: contain;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            /* Untuk gambar dengan overlay gradasi khusus */
            .hero-img-with-overlay {
                position: relative;
            }

            .hero-img-with-overlay::after {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: linear-gradient(45deg, rgba(0, 0, 0, 0.3) 0%, rgba(0, 0, 0, 0.1) 100%);
                pointer-events: none;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const slides = document.querySelectorAll('.carousel-slide');
                const dots = document.querySelectorAll('.carousel-dot');
                let currentSlide = 0;
                let isTransitioning = false;

                if (slides.length === 0) return;

                function showSlide(index) {
                    if (isTransitioning) return;

                    isTransitioning = true;

                    // Hide all slides
                    slides.forEach((slide, i) => {
                        slide.style.opacity = i === index ? '1' : '0';
                    });

                    // Update dots
                    dots.forEach((dot, i) => {
                        if (i === index) {
                            dot.classList.remove('bg-white/50');
                            dot.classList.add('bg-white');
                        } else {
                            dot.classList.remove('bg-white');
                            dot.classList.add('bg-white/50');
                        }
                    });

                    currentSlide = index;

                    setTimeout(() => {
                        isTransitioning = false;
                    }, 1000);
                }

                function nextSlide() {
                    const next = (currentSlide + 1) % slides.length;
                    showSlide(next);
                }

                // Auto-advance carousel every 5 seconds
                setInterval(nextSlide, 5000);

                // Manual dot navigation
                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        showSlide(index);
                    });
                });

                // Pause on hover (optional)
                const heroSection = document.querySelector('.hero-carousel');
                let autoAdvance = setInterval(nextSlide, 5000);

                if (heroSection) {
                    heroSection.addEventListener('mouseenter', () => {
                        clearInterval(autoAdvance);
                    });

                    heroSection.addEventListener('mouseleave', () => {
                        autoAdvance = setInterval(nextSlide, 5000);
                    });
                }
            });
        </script>
    @endpush
@else
    @push('styles')
        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            .aspect-square {
                aspect-ratio: 1 / 1;
            }
        </style>
    @endpush
@endif
