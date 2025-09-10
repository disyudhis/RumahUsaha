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
        'jasa' => '☕ Jasa',
        'digital' => '📱 Digital',
        'fashion' => '👗 Fashion',
        'kerajinan' => '🥕 Kerajinan',
        'lainnya' => '👔 Lainnya',
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
            $query
                ->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhereHas('umkmProfile', function ($q) {
                    $q->where('business_name', 'like', '%' . $this->search . '%');
                });
        }

        // Apply category filter (you'll need to add category field to products table)
        if ($this->category) {
            $query->where('category', $this->category);
        }

        return [
            'products' => $query->latest()->get(),
            'productsCount' => $query->count(),
        ];
    }
}; ?>

<div class="mb-8">
    {{-- Search Section --}}
    <div class="mb-8">
        <div class="max-w-2xl mx-auto">
            <div class="flex">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Masukkan nama produk atau usaha..."
                        class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-l-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                @if ($search || $category)
                    <button wire:click="clearFilters"
                        class="bg-gray-500 text-white px-4 py-3 hover:bg-gray-600 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
                <button class="bg-blue-500 text-white px-6 py-3 rounded-r-lg hover:bg-blue-600 font-medium">
                    Cari
                </button>
            </div>

            {{-- Active Filters --}}
            @if ($search || $category)
                <div class="mt-4 flex flex-wrap gap-2">
                    @if ($search)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                            Pencarian: "{{ $search }}"
                            <button wire:click="$set('search', '')" class="ml-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                    @if ($category)
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-green-100 text-green-800">
                            {{ $categories[$category] ?? $category }}
                            <button wire:click="$set('category', '')" class="ml-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        {{-- Left Sidebar - Categories --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">KATEGORI PRODUK</h3>
                <div class="space-y-3">
                    <button wire:click="clearFilters"
                        class="flex items-center w-full text-left text-gray-700 hover:text-blue-600 {{ !$category ? 'text-blue-600 font-medium' : '' }}">
                        <span class="mr-2">🏠</span>
                        <span class="text-sm">Semua Produk</span>
                    </button>
                    @foreach ($categories as $key => $label)
                        <button wire:click="setCategory('{{ $key }}')"
                            class="flex items-center w-full text-left text-gray-700 hover:text-blue-600 {{ $category === $key ? 'text-blue-600 font-medium' : '' }}">
                            <span class="text-sm">{{ $label }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Products Count --}}
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        Ditemukan {{ $productsCount }} produk
                    </p>
                </div>
            </div>
        </div>

        {{-- Products Grid --}}
        <div class="lg:col-span-3">
            {{-- Results Header --}}
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                    <span class="mr-2">⭐</span>
                    @if ($search)
                        HASIL PENCARIAN
                    @elseif($category)
                        {{ strtoupper($categories[$category] ?? $category) }}
                    @else
                        PRODUK TERBARU
                    @endif
                </h3>
            </div>

            {{-- Products Grid --}}
            @if ($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($products as $product)
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                            <div class="p-4">
                                {{-- Product Image --}}
                                <div
                                    class="bg-gray-100 rounded-lg h-40 flex items-center justify-center mb-4 overflow-hidden">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <span class="text-gray-400 text-sm">📷 Foto Produk</span>
                                    @endif
                                </div>

                                {{-- Product Info --}}
                                <h4 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $product->name }}</h4>

                                {{-- Business Name --}}
                                @if ($product->umkmProfile)
                                    <p class="text-xs text-gray-500 mb-2">
                                        🏪 {{ $product->umkmProfile->business_name }}
                                    </p>
                                @endif

                                {{-- Description --}}
                                @if ($product->description)
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
                                @endif

                                {{-- Price --}}
                                <p class="text-blue-600 font-semibold text-lg mb-4">Rp {{ $product->formatted_price }}
                                </p>

                                {{-- Actions --}}
                                {{-- <div class="flex gap-2">
                                    <button
                                        class="flex-1 bg-blue-500 text-white py-2 px-3 rounded hover:bg-blue-600 text-sm transition-colors">
                                        Detail
                                    </button>
                                    <button
                                        class="bg-green-500 text-white py-2 px-3 rounded hover:bg-green-600 text-sm transition-colors">
                                        💬
                                    </button>
                                </div> --}}
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Load More Button (if needed) --}}
                {{-- <div class="mt-8 text-center">
                    <button class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition-colors">
                        Lihat Produk Lainnya
                    </button>
                </div> --}}
            @else
                {{-- Empty State --}}
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🔍</div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">
                        @if ($search)
                            Tidak ada produk yang ditemukan untuk "{{ $search }}"
                        @elseif($category)
                            Belum ada produk di kategori {{ $categories[$category] ?? $category }}
                        @else
                            Belum ada produk yang tersedia
                        @endif
                    </h3>
                    <p class="text-gray-500 mb-6">
                        @if ($search || $category)
                            Coba ubah kata kunci pencarian atau pilih kategori lain
                        @else
                            Produk akan muncul di sini setelah UMKM menambahkan produk mereka
                        @endif
                    </p>
                    @if ($search || $category)
                        <button wire:click="clearFilters"
                            class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                            Lihat Semua Produk
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Loading State --}}
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 flex items-center">
            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            Memuat...
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
    </style>
@endpush
