<?php

use Livewire\Volt\Component;
use App\Models\Product;

new class extends Component {
    public Product $product;
    // public $product;
    public $relatedProducts = [];

    public function mount($slug)
    {
        $this->product = Product::where('slug', $slug)->firstOrFail();
        $this->loadProduct();
        $this->loadRelatedProducts();
    }

    public function loadProduct()
    {
        $this->product = Product::with(['umkmProfile'])
            ->where('slug', $this->product->slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function loadRelatedProducts()
    {
        $this->relatedProducts = Product::with(['umkmProfile'])
            ->where('category', $this->product->category)
            ->where('id', '!=', $this->product->id)
            ->where('is_active', true)
            ->take(4)
            ->get();
    }

    public function contactUmkm()
    {
        // Logic untuk kontak UMKM (WhatsApp, Email, dll)
        $this->dispatch('show-contact-modal');
    }

    public function showAllProduct()
    {
        return $this->redirect(route('main.products.index'), true);
    }

    public function showDetailProduct($productId)
    {
        return $this->redirect(route('main.products.show', ['id' => $productId]), navigate: true);
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-primary-50 to-accent-100">
    {{-- Breadcrumb --}}
    <div class="bg-white shadow-sm border-b border-neutral-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <nav class="flex items-center space-x-2 text-sm text-neutral-600">
                <a href="{{ route('home') }}" class="hover:text-primary-600 transition-colors">
                    <svg class="w-4 h-4 inline-block mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                    Beranda
                </a>
                <span>/</span>
                <button wire:click='showAllProduct' class="hover:text-primary-600 transition-colors">Produk</button>
                <span>/</span>
                <span class="text-neutral-900 font-medium">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Product Detail Section --}}
        <div class="bg-white rounded-2xl shadow-warm-lg overflow-hidden mb-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
                {{-- Product Image --}}
                <div class="space-y-4">
                    <div class="aspect-square rounded-xl overflow-hidden bg-neutral-100">
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-700">
                        @else
                            <div
                                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-accent-200">
                                <svg class="w-24 h-24 text-primary-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Product Info --}}
                <div class="space-y-6">
                    {{-- Category Badge --}}
                    <div class="flex items-center space-x-2">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800 border border-primary-200">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                            </svg>
                            {{ $product->category_name }}
                        </span>
                    </div>

                    {{-- Product Name --}}
                    <div>
                        <h1 class="text-3xl font-bold text-neutral-900 leading-tight mb-2">
                            {{ $product->name }}
                        </h1>
                        <p class="text-4xl font-bold text-primary-600 font-inter">
                            Rp {{ $product->formatted_price }}
                        </p>
                    </div>

                    {{-- UMKM Info --}}
                    <div class="bg-gradient-to-r from-accent-50 to-primary-50 p-4 rounded-xl border border-accent-200">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-primary-500 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-neutral-600">Dijual oleh</p>
                                <h3 class="font-semibold text-neutral-900">{{ $product->umkmProfile->business_name }}
                                </h3>
                                @if ($product->umkmProfile->address)
                                    <p class="text-sm text-neutral-600 flex items-center mt-1">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $product->umkmProfile->address }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Contact Actions --}}
                    <div class="space-y-3">
                        <button wire:click="contactUmkm"
                            class="w-full bg-gradient-to-r from-primary-500 to-primary-600 text-white py-4 px-6 rounded-xl font-semibold hover:from-primary-600 hover:to-primary-700 transform hover:-translate-y-0.5 transition-all duration-200 shadow-warm">
                            <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                            </svg>
                            Hubungi Penjual
                        </button>

                        <div class="flex space-x-3">
                            <button
                                class="flex-1 bg-success-500 text-white py-3 px-4 rounded-lg font-medium hover:bg-success-600 transition-colors">
                                <svg class="w-4 h-4 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M.5 3.75c0-1.036.84-1.875 1.875-1.875h3.75c.415 0 .79.26.933.649l1.66 4.577A1.875 1.875 0 017.5 8.75H6.25a.75.75 0 00-.75.75v1.5c0 1.65 1.35 3 3 3h3a.75.75 0 00.75-.75V12c0-.517.21-.986.553-1.325l2.447-2.447A1.875 1.875 0 0117.5 6.75v-.375C17.5 5.34 16.66 4.5 15.625 4.5H13.75c-.621 0-1.125.504-1.125 1.125v.375c0 .414-.336.75-.75.75h-1.5a.75.75 0 01-.75-.75v-.375c0-.621-.504-1.125-1.125-1.125H2.375A1.875 1.875 0 00.5 6.375v.375z" />
                                </svg>
                                WhatsApp
                            </button>
                            <button
                                class="flex-1 bg-neutral-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-neutral-700 transition-colors">
                                <svg class="w-4 h-4 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                </svg>
                                Email
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Description --}}
            <div class="border-t border-neutral-200 p-8">
                <h2 class="text-2xl font-bold text-neutral-900 mb-4">Deskripsi Produk</h2>
                <div class="prose max-w-none text-neutral-700 leading-relaxed">
                    @if ($product->description)
                        {!! nl2br(e($product->description)) !!}
                    @else
                        <p class="text-neutral-500 italic">Belum ada deskripsi untuk produk ini.</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- Related Products --}}
        @if ($relatedProducts->count() > 0)
            <div class="bg-white rounded-2xl shadow-warm-lg overflow-hidden p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-neutral-900">Produk Serupa</h2>
                    <a href="{{ route('main.products.index', ['category' => $product->category]) }}"
                        class="text-primary-600 hover:text-primary-700 font-medium text-sm flex items-center group">
                        Lihat Semua
                        <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($relatedProducts as $related)
                        <a href="{{ route('main.products.show', $related->id) }}"
                            class="group bg-white rounded-xl shadow-md hover:shadow-warm-lg border border-neutral-200 hover:border-primary-300 transition-all duration-300 overflow-hidden">
                            <div class="aspect-square bg-neutral-100 overflow-hidden">
                                @if ($related->image)
                                    <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-accent-200">
                                        <svg class="w-12 h-12 text-primary-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3
                                    class="font-semibold text-neutral-900 line-clamp-2 mb-2 group-hover:text-primary-700 transition-colors">
                                    {{ $related->name }}
                                </h3>
                                <p class="text-lg font-bold text-primary-600 mb-1">
                                    Rp {{ $related->formatted_price }}
                                </p>
                                <p class="text-sm text-neutral-600">
                                    {{ $related->umkmProfile->business_name }}
                                </p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Contact Modal (you can implement this as needed) --}}
    {{-- Add your modal component here --}}
</div>
