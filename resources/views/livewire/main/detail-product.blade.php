<?php

use Livewire\Volt\Component;
use App\Models\Product;

new class extends Component {
    public Product $product;
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
        $this->dispatch('show-contact-modal');
    }

    public function showAllProduct()
    {
        return $this->redirect(route('main.products.index'), true);
    }

    public function showDetailProduct($productId)
    {
        return $this->redirect(route('main.products.show', ['slug' => $productId]), navigate: true);
    }

    public function shareProduct($platform)
    {
        $url = route('main.products.show', ['slug' => $this->product->slug]);
        $text = "Lihat produk {$this->product->name} dari {$this->product->umkmProfile->business_name}";

        $shareUrls = [
            'whatsapp' => 'https://wa.me/?text=' . urlencode("$text - $url"),
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url),
            'twitter' => 'https://twitter.com/intent/tweet?url=' . urlencode($url) . '&text=' . urlencode($text),
            'telegram' => 'https://t.me/share/url?url=' . urlencode($url) . '&text=' . urlencode($text),
        ];

        if (isset($shareUrls[$platform])) {
            $this->dispatch('open-share-url', url: $shareUrls[$platform]);
        }
    }

    public function copyLink()
    {
        $url = route('main.products.show', ['slug' => $this->product->slug]);
        $this->dispatch('link-copied', url: $url);
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-primary-50 to-accent-100" x-data="{ shareMenuOpen: false }">
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
                        <p class="text-md font-bold text-primary-600 font-inter">
                            <span class="text-4xl">Rp {{ $product->formatted_price }}</span>
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
                        <div class="flex space-x-3">
                            <a href="https://wa.me/+62{{ preg_replace('/[^0-9]/', '', $product->umkmProfile->whatsapp) }}"
                                target="_blank"
                                class="flex-1 bg-success-500 text-white py-3 px-4 rounded-lg font-medium hover:bg-success-600 transition-colors">
                                <svg class="w-4 h-4 inline-block mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M.5 3.75c0-1.036.84-1.875 1.875-1.875h3.75c.415 0 .79.26.933.649l1.66 4.577A1.875 1.875 0 017.5 8.75H6.25a.75.75 0 00-.75.75v1.5c0 1.65 1.35 3 3 3h3a.75.75 0 00.75-.75V12c0-.517.21-.986.553-1.325l2.447-2.447A1.875 1.875 0 0117.5 6.75v-.375C17.5 5.34 16.66 4.5 15.625 4.5H13.75c-.621 0-1.125.504-1.125 1.125v.375c0 .414-.336.75-.75.75h-1.5a.75.75 0 01-.75-.75v-.375c0-.621-.504-1.125-1.125-1.125H2.375A1.875 1.875 0 00.5 6.375v.375z" />
                                </svg>
                                WhatsApp
                            </a>
                            @if ($product->umkmProfile->instagram)
                                <a href="https://instagram.com/{{ ltrim($product->umkmProfile->instagram, '@') }}"
                                    target="_blank"
                                    class="flex-1 bg-gradient-to-r from-pink-500 via-red-500 to-yellow-500 text-white py-3 px-4 rounded-lg font-medium hover:opacity-90 transition-all">
                                    <svg class="w-4 h-4 inline-block mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M7.75 2h8.5A5.75 5.75 0 0122 7.75v8.5A5.75 5.75 0 0116.25 22h-8.5A5.75 5.75 0 012 16.25v-8.5A5.75 5.75 0 017.75 2zm0 1.5A4.25 4.25 0 003.5 7.75v8.5A4.25 4.25 0 007.75 20.5h8.5a4.25 4.25 0 004.25-4.25v-8.5A4.25 4.25 0 0016.25 3.5h-8.5zm9.25 2.75a1 1 0 110 2 1 1 0 010-2zM12 7a5 5 0 110 10 5 5 0 010-10zm0 1.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7z" />
                                    </svg>
                                    Instagram
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Share Button --}}
                    <div class="border-t border-neutral-200 pt-4 relative">
                        <button @click="shareMenuOpen = !shareMenuOpen"
                            class="w-full bg-white border-2 border-primary-500 text-primary-600 py-3 px-6 rounded-xl font-semibold hover:bg-primary-50 transition-all duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                            </svg>
                            Bagikan Produk
                        </button>

                        {{-- Share Menu Dropdown --}}
                        <div x-show="shareMenuOpen"
                             @click.away="shareMenuOpen = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-xl border border-neutral-200 z-10 p-2"
                             style="display: none;">
                            <div class="space-y-1">
                                <button wire:click="copyLink" @click="shareMenuOpen = false"
                                    class="w-full flex items-center px-4 py-2 text-sm text-neutral-700 hover:bg-neutral-100 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Salin Link
                                </button>
                            </div>
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
                        <button wire:click='showDetailProduct("{{ $related->slug }}")'
                            class="group bg-white rounded-xl shadow-md hover:shadow-warm-lg border border-neutral-200 hover:border-primary-300 transition-all duration-300 overflow-hidden">
                            <div class="aspect-square bg-neutral-100 overflow-hidden">
                                @if ($related->image)
                                    <img src="{{ asset('storage/' . $related->image) }}" alt="{{ $related->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary-100 to-accent-200">
                                        <svg class="w-12 h-12 text-primary-300" fill="currentColor"
                                            viewBox="0 0 20 20">
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
                        </button>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:init', () => {
            // Handle share URL
            Livewire.on('open-share-url', (event) => {
                window.open(event.url, '_blank', 'width=600,height=400');
            });

            // Handle copy link
            Livewire.on('link-copied', (event) => {
                // Copy to clipboard
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(event.url).then(() => {
                        showToast('Link berhasil disalin!', 'success');
                    }).catch(() => {
                        fallbackCopyTextToClipboard(event.url);
                    });
                } else {
                    fallbackCopyTextToClipboard(event.url);
                }
            });

            // Fallback copy method untuk browser lama
            function fallbackCopyTextToClipboard(text) {
                const textArea = document.createElement("textarea");
                textArea.value = text;
                textArea.style.position = "fixed";
                textArea.style.left = "-999999px";
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    document.execCommand('copy');
                    showToast('Link berhasil disalin!', 'success');
                } catch (err) {
                    showToast('Gagal menyalin link', 'error');
                }

                document.body.removeChild(textArea);
            }

            // Show toast notification
            function showToast(message, type = 'success') {
                // Remove existing toast if any
                const existingToast = document.getElementById('copy-toast');
                if (existingToast) {
                    existingToast.remove();
                }

                const toast = document.createElement('div');
                toast.id = 'copy-toast';
                toast.className =
                    `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 flex items-center space-x-2 transition-all duration-300`;

                if (type === 'success') {
                    toast.className += ' bg-success-500 text-white';
                    toast.innerHTML = `
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>${message}</span>
                `;
                } else {
                    toast.className += ' bg-danger-500 text-white';
                    toast.innerHTML = `
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>${message}</span>
                `;
                }

                document.body.appendChild(toast);

                // Animate in
                setTimeout(() => {
                    toast.style.opacity = '1';
                }, 10);

                // Remove after 3 seconds
                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(1rem)';
                    setTimeout(() => {
                        if (toast.parentNode) {
                            toast.remove();
                        }
                    }, 300);
                }, 3000);
            }
        });
    </script>
@endpush
