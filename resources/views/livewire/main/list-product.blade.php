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
        'jasa' => '✂️ Jasa',
        'kerajinan' => '🧶 Kerajinan',
        'lainnya' => '⭐ Lainnya',
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
    {{-- Hero Section --}}
    <div class="rounded-2xl mb-12">
        <div class="container mx-auto">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                {{-- Left Content --}}
                <div class="text-center lg:text-left">
                    <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                        Dari Rumah,<br>
                        <span>Untuk Negeri</span>
                    </h1>
                    <p class="text-lg lg:text-xl text-black mb-8 max-w-lg mx-auto lg:mx-0">
                        Etalase Digital UMKM Komunitas Anda
                    </p>

                    {{-- Search Form --}}
                    <div class="max-w-lg mx-auto lg:mx-0">
                        <div class="flex shadow-lg rounded-xl overflow-hidden bg-white">
                            <div class="flex-1 relative">
                                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari Produk"
                                    class="w-full pl-6 pr-4 py-4 border-0 focus:ring-0 focus:outline-none text-gray-700 placeholder-gray-400 text-lg">
                            </div>
                            <button
                                class="bg-secondary-800 hover:bg-secondary-900 text-white px-8 py-4 font-semibold text-lg transition-colors">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Right Content - Hero Image --}}
                <div class="relative">
                    <div class="relative z-10">
                        <div class="aspect-square lg:aspect-[4/3] rounded-3xl overflow-hidden shadow-xl">
                            <img src="{{ asset('assets/bg.jpg') }}" alt="UMKM Hero" class="w-full h-full object-cover"
                                style="background: linear-gradient(135deg, #FED7AA 0%, #FDBA74 100%);">
                        </div>
                    </div>
                    {{-- Decorative Elements --}}
                    <div class="absolute -top-6 -right-6 w-24 h-24 bg-orange-200 rounded-full opacity-60"></div>
                    <div class="absolute -bottom-6 -left-6 w-32 h-32 bg-yellow-200 rounded-full opacity-40"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Category Filter Buttons --}}
    <div class="flex flex-wrap justify-center gap-3 mb-8">
        <button wire:click="clearFilters"
            class="px-6 py-2.5 rounded-full text-sm font-medium transition-all duration-200 {{ !$category ? 'bg-secondary-800 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-orange-50 hover:text-orange-600 border border-gray-200' }}">
            🏠 Semua
        </button>
        @foreach ($categories as $key => $label)
            <button wire:click="setCategory('{{ $key }}')"
                class="px-6 py-2.5 rounded-full text-sm font-medium transition-all duration-200 {{ $category === $key ? 'bg-secondary-800 text-white shadow-md' : 'bg-white text-gray-600 hover:bg-orange-50 hover:text-orange-600 border border-gray-200' }}">
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
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @foreach ($products as $product)
                    <div
                        class="bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 group">
                        {{-- Product Image --}}
                        <div class="relative aspect-square bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">
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
                        Tidak ada produk yang cocok dengan pencarian "{{ $search }}". Coba kata kunci lain atau
                        lihat semua produk.
                    @elseif($category)
                        Kategori {{ $categories[$category] ?? $category }} belum memiliki produk. Coba kategori lain
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
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="text-gray-700 font-medium">Memuat produk...</span>
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
    </style>
@endpush
