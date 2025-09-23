<?php

// resources/views/livewire/main/list-product.blade.php
use Livewire\Volt\Component;
use App\Models\Product;
use Livewire\Attributes\Url;

new class extends Component {
    #[Url]
    public $search = '';

    #[Url]
    public $category = '';

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
            'class' => 'hero-img-top', // untuk gambar landscape standar
            'alt' => 'UMKM Hero 1'
        ],
        [
            'path' => 'assets/bg2.jpg',
            'class' => 'hero-img-center', // untuk gambar yang fokus di bagian atas
            'alt' => 'UMKM Hero 2'
        ],
        [
            'path' => 'assets/bg3.jpg',
            'class' => 'hero-img-center', // untuk gambar yang fokus di bagian atas
            'alt' => 'UMKM Hero 2'
        ],
        [
            'path' => 'assets/bg4.jpg',
            'class' => 'hero-img-center', // untuk gambar yang fokus di bagian atas
            'alt' => 'UMKM Hero 2'
        ],
    ];

    public function updatedSearch()
    {
        // Reset category when searching
        $this->reset('category');
    }

    public function setCategory($cat)
    {
        $this->category = $cat;
        $this->reset('search');
    }

    public function clearFilters()
    {
        $this->reset(['search', 'category']);
    }

    public function with()
    {
        $query = Product::with('umkmProfile');

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('umkmProfile', function ($subQuery) {
                        $subQuery->where('business_name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        // Apply category filter
        if ($this->category) {
            $query->where('category', $this->category);
        }

        return [
            'products' => $query->latest()->limit(8)->get(), // Limit to show recent products
            'productsCount' => $query->count(),
        ];
    }
}; ?>

<div>
    {{-- Hero Section with Image Carousel --}}
    <div class="relative mb-8">
        <div class="container mx-auto">
            <!-- Hero Section dengan Background Image Carousel -->
            <div class="min-h-[400px] lg:min-h-[700px] overflow-hidden">

                <!-- Image Carousel -->
                <div class="hero-carousel absolute inset-0">
                    @foreach($heroImages as $index => $imageData)
                        <div class="carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out {{ $index === 0 ? 'opacity-100' : 'opacity-0' }}"
                             data-slide="{{ $index }}">
                            <img src="{{ asset($imageData['path']) }}" alt="{{ $imageData['alt'] }}"
                                class="w-full h-full {{ $imageData['class'] }}">
                        </div>
                    @endforeach
                </div>

                <!-- Carousel Navigation Dots -->
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 z-20 flex space-x-3">
                    @foreach($heroImages as $index => $imageData)
                        <button class="carousel-dot w-3 h-3 rounded-full transition-all duration-300 {{ $index === 0 ? 'bg-white' : 'bg-white/50' }}"
                                data-slide="{{ $index }}"></button>
                    @endforeach
                </div>

                <!-- Gradient Overlay dari kiri ke tengah -->
                <div class="absolute inset-0 lg:bg-gradient-to-r lg:from-fix-200 lg:via-fix-200/50 lg:to-transparent sm:bg-gradient-to-r sm:from-fix-200 sm:via-fix-300/10 sm:to-fix-100/70 bg-gradient-to-r from-fix-200 via-fix-200/50 to-transparent z-10"></div>

                <!-- Content -->
                <div class="relative z-20 h-full flex items-center">
                    <div class="w-full lg:w-1/2 p-8 lg:p-24">
                        <h1
                            class="text-4xl font-aleo lg:text-5xl xl:text-6xl font-bold text-black mb-6 leading-tight">
                            Dari Rumah,<br>
                            <span class="text-primary-600">Untuk Negeri</span>
                        </h1>
                        <p class="text-lg lg:text-xl font-acme text-black mb-8 max-w-lg">
                            Etalase Digital UMKM Anda
                        </p>

                        <!-- Search Form -->
                        <div class="max-w-lg">
                            <div class="flex shadow-lg rounded-xl overflow-hidden bg-white">
                                <div class="flex-1 relative">
                                    <input type="text" wire:model.live.debounce.300ms="search"
                                        placeholder="Cari Produk"
                                        class="w-full pl-6 pr-4 py-4 border-0 focus:ring-0 focus:outline-none text-gray-700 placeholder-gray-400 text-lg">
                                </div>
                                <button
                                    class="bg-fix-100 hover:bg-secondary-900 text-white px-8 py-4 font-semibold text-lg transition-colors">
                                    Cari
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Category Filter Buttons --}}
    <div class="px-4 sm:px-6">
        <div class="flex flex-wrap justify-center gap-3 mb-8">
            <button wire:click="clearFilters"
                class="px-4 py-2.5 rounded-full text-xs sm:text-sm font-medium transition-all duration-200 {{ !$category ? 'bg-secondary-800 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-orange-50 hover:text-orange-600 border border-gray-200' }}">
                🏠 Semua
            </button>
            @foreach ($categories as $key => $label)
                <button wire:click="setCategory('{{ $key }}')"
                    class="px-4 py-2.5 rounded-full text-xs sm:text-sm font-medium transition-all duration-200 whitespace-nowrap {{ $category === $key ? 'bg-secondary-800 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-orange-50 hover:text-orange-600 border border-gray-200' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Products Section --}}
        <div class="mb-12">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">PRODUK TERBARU</h2>
                @if ($search || $category)
                    <button wire:click="clearFilters" class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                        Lihat Semua →
                    </button>
                @endif
            </div>

            {{-- Active Filters --}}
            @if ($search || $category)
                <div class="mb-6 flex flex-wrap gap-2">
                    @if ($search)
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm bg-orange-100 text-orange-800 border border-orange-200">
                            Pencarian: "{{ $search }}"
                            <button wire:click="$set('search', '')" class="ml-2 hover:text-orange-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if ($category)
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm bg-blue-100 text-blue-800 border border-blue-200">
                            {{ $categories[$category] ?? $category }}
                            <button wire:click="$set('category', '')" class="ml-2 hover:text-blue-900">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                </div>
            @endif

            {{-- Products Grid --}}
            @if ($products->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    @foreach ($products as $product)
                        <div
                            class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 group">
                            {{-- Product Image --}}
                            <div
                                class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div
                                        class="flex items-center justify-center h-full bg-gradient-to-br from-orange-100 to-yellow-100">
                                        <div class="text-center">
                                            <div class="text-4xl mb-2">📷</div>
                                            <span class="text-gray-400 text-sm">Foto Produk</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Product Info --}}
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 text-lg">{{ $product->name }}</h3>

                                {{-- Price --}}
                                <div class="mb-4">
                                    <span class="text-xl font-bold text-gray-900">
                                        Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>

                                {{-- Action Button --}}
                                <button
                                    class="w-full bg-primary-400 hover:bg-primary-300 text-white font-medium py-2.5 px-4 rounded-lg transition-colors duration-200">
                                    Detail Produk
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Show All Products Button --}}
                @if ($products->count() >= 8)
                    <div class="text-center">
                        <button
                            class="bg-white hover:bg-gray-50 text-gray-700 font-medium px-8 py-3 rounded-lg border border-gray-200 transition-colors duration-200 shadow-sm hover:shadow-md">
                            Lihat Produk Lainnya
                        </button>
                    </div>
                @endif
            @else
                {{-- Empty State --}}
                <div class="text-center py-16">
                    <div class="text-8xl mb-6 opacity-50">🔍</div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">
                        @if ($search)
                            Produk tidak ditemukan
                        @elseif($category)
                            Belum ada produk di kategori ini
                        @else
                            Belum ada produk tersedia
                        @endif
                    </h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">
                        @if ($search)
                            Tidak ada produk yang cocok dengan pencarian "{{ $search }}". Coba kata kunci lain
                            atau
                            lihat semua produk.
                        @elseif($category)
                            Kategori {{ $categories[$category] ?? $category }} belum memiliki produk. Coba kategori
                            lain
                            atau lihat semua produk.
                        @else
                            Produk dari UMKM akan muncul di sini. Pantau terus untuk update terbaru!
                        @endif
                    </p>
                    @if ($search || $category)
                        <button wire:click="clearFilters"
                            class="bg-primary-400 hover:bg-primary-300 text-white font-semibold px-8 py-3 rounded-xl transition-colors duration-200">
                            Lihat Semua Produk
                        </button>
                    @endif
                </div>
            @endif

            {{-- Products Count Info --}}
            @if ($products->count() > 0)
                <div class="text-center mt-6 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        @if ($search || $category)
                            Menampilkan {{ $products->count() }} dari {{ $productsCount }} produk
                            @if ($search)
                                untuk "{{ $search }}"
                            @endif
                            @if ($category)
                                di kategori {{ $categories[$category] ?? $category }}
                            @endif
                        @else
                            Menampilkan {{ $products->count() }} produk terbaru dari {{ $productsCount }} total produk
                        @endif
                    </p>
                </div>
            @endif
        </div>

        {{-- Loading State --}}
        <div wire:loading class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-2xl p-8 flex items-center shadow-xl">
                <svg class="animate-spin -ml-1 mr-4 h-6 w-6 text-orange-500" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-gray-700 font-medium">Memuat produk...</span>
            </div>
        </div>
    </div>
</div>

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
            background: linear-gradient(45deg, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.1) 100%);
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
