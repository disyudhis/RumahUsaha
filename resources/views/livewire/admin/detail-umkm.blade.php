<?php

use Livewire\Volt\Component;
use App\Models\UmkmProfile;

new class extends Component {
    public UmkmProfile $umkm;
    public $showContactModal = false;

    public function mount($id)
    {
        $this->umkm = UmkmProfile::with(['products' => function($query) {
            $query->where('is_active', true)->limit(6);
        }])->findOrFail($id);
    }

    public function toggleContactModal()
    {
        $this->showContactModal = !$this->showContactModal;
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-primary-50 to-accent-100">
    <!-- Hero Section -->
    <div class="relative overflow-hidden bg-hero-gradient">
        <div class="absolute inset-0 bg-white/20"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <!-- UMKM Info -->
                <div class="space-y-6">
                    <div class="flex items-center space-x-4">
                        <!-- Logo/Avatar -->
                        <div class="relative">
                            @if($umkm->hasLogo())
                                <img src="{{ $umkm->logo_url }}"
                                     alt="{{ $umkm->business_name }}"
                                     class="w-20 h-20 rounded-2xl object-cover border-4 border-white shadow-warm-lg">
                            @else
                                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-primary-400 to-primary-600
                                          flex items-center justify-center border-4 border-white shadow-warm-lg">
                                    <span class="text-2xl font-bold text-white">{{ $umkm->initials }}</span>
                                </div>
                            @endif
                            <!-- Status Badge -->
                            @if($umkm->is_active)
                                <div class="absolute -top-2 -right-2 bg-success-500 w-6 h-6 rounded-full
                                          flex items-center justify-center border-2 border-white">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1">
                            <h1 class="text-3xl lg:text-4xl font-bold text-fix-100 mb-2">
                                {{ $umkm->business_name }}
                            </h1>
                            <p class="text-secondary-700 font-medium">
                                Pemilik: {{ $umkm->owner_name }}
                            </p>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($umkm->description)
                        <div class="bg-white/80 backdrop-blur-sm rounded-2xl p-6 shadow-warm">
                            <p class="text-neutral-700 leading-relaxed">{{ $umkm->description }}</p>
                        </div>
                    @endif

                    <!-- Contact Actions -->
                    <div class="flex flex-wrap gap-4">
                        @if($umkm->whatsapp_url)
                            <a href="{{ $umkm->whatsapp_url }}" target="_blank"
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600
                                      text-white font-semibold rounded-xl hover:from-green-600 hover:to-green-700
                                      transition-all duration-200 shadow-warm hover:shadow-warm-lg transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                </svg>
                                WhatsApp
                            </a>
                        @endif

                        @if($umkm->instagram_url)
                            <a href="{{ $umkm->instagram_url }}" target="_blank"
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600
                                      text-white font-semibold rounded-xl hover:from-pink-600 hover:to-purple-700
                                      transition-all duration-200 shadow-warm hover:shadow-warm-lg transform hover:-translate-y-0.5">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.621 5.367 11.988 11.988 11.988s11.987-5.367 11.987-11.988C24.004 5.367 18.637.001 12.017.001zM8.449 16.988c-2.428 0-4.399-1.971-4.399-4.399s1.971-4.399 4.399-4.399 4.399 1.971 4.399 4.399-1.971 4.399-4.399 4.399zm7.138 0c-2.428 0-4.399-1.971-4.399-4.399s1.971-4.399 4.399-4.399 4.399 1.971 4.399 4.399-1.971 4.399-4.399 4.399z"/>
                                </svg>
                                Instagram
                            </a>
                        @endif

                        <button wire:click="toggleContactModal"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600
                                       text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700
                                       transition-all duration-200 shadow-warm hover:shadow-warm-lg transform hover:-translate-y-0.5">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Lihat Alamat
                        </button>
                    </div>
                </div>

                <!-- Stats/Info Cards -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Products Count -->
                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-6 text-center shadow-warm">
                            <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-fix-100">{{ $umkm->products->count() }}</h3>
                            <p class="text-neutral-600 text-sm">Produk</p>
                        </div>

                        <!-- Join Date -->
                        <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-6 text-center shadow-warm">
                            <div class="w-12 h-12 bg-accent-200 rounded-xl flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-accent-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-fix-100">{{ $umkm->created_at->format('M Y') }}</h3>
                            <p class="text-neutral-600 text-sm">Bergabung</p>
                        </div>
                    </div>

                    <!-- Status Card -->
                    <div class="bg-white/90 backdrop-blur-sm rounded-2xl p-6 shadow-warm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-neutral-800 mb-2">Status UMKM</h3>
                                <div class="flex items-center space-x-2">
                                    @if($umkm->is_active)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                   bg-success-100 text-success-800">
                                            <span class="w-2 h-2 bg-success-500 rounded-full mr-1"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                   bg-neutral-100 text-neutral-800">
                                            <span class="w-2 h-2 bg-neutral-400 rounded-full mr-1"></span>
                                            Tidak Aktif
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="w-12 h-12 bg-gradient-to-br from-secondary-200 to-secondary-300 rounded-xl
                                      flex items-center justify-center">
                                <svg class="w-6 h-6 text-secondary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Section -->
    @if($umkm->products->count() > 0)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center mb-10">
                <h2 class="text-3xl font-bold text-fix-100 mb-4">Produk Kami</h2>
                <p class="text-neutral-600 max-w-2xl mx-auto">
                    Temukan koleksi produk berkualitas dari {{ $umkm->business_name }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($umkm->products as $product)
                    <div class="bg-white rounded-2xl shadow-warm hover:shadow-warm-lg transition-all duration-300
                              overflow-hidden group hover:-translate-y-1">
                        <!-- Product Image -->
                        <div class="aspect-w-16 aspect-h-12 bg-gradient-to-br from-primary-50 to-accent-100
                                  relative overflow-hidden">
                            @if($product->image_url)
                                <img src="{{ $product->image_url }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-48 flex items-center justify-center">
                                    <div class="w-16 h-16 bg-primary-200 rounded-2xl flex items-center justify-center">
                                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                        </svg>
                                    </div>
                                </div>
                            @endif
                            <!-- Price Badge -->
                            @if($product->price)
                                <div class="absolute top-4 right-4 bg-white/95 backdrop-blur-sm rounded-xl px-3 py-2">
                                    <span class="text-primary-600 font-bold text-sm">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="p-6">
                            <h3 class="font-bold text-lg text-neutral-800 mb-2 group-hover:text-primary-600 transition-colors">
                                {{ $product->name }}
                            </h3>
                            @if($product->description)
                                <p class="text-neutral-600 text-sm leading-relaxed mb-4 line-clamp-3">
                                    {{ Str::limit($product->description, 120) }}
                                </p>
                            @endif

                            <!-- Product Actions -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    @if($product->category)
                                        <span class="px-2 py-1 bg-accent-100 text-accent-700 text-xs rounded-lg font-medium">
                                            {{ $product->category }}
                                        </span>
                                    @endif
                                </div>

                                <button class="text-primary-600 hover:text-primary-700 font-medium text-sm
                                             hover:underline transition-colors">
                                    Lihat Detail
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- View All Products Button -->
            @if($umkm->products->count() >= 6)
                <div class="text-center mt-10">
                    <a href="#"
                       class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-primary-500 to-primary-600
                              text-white font-semibold rounded-xl hover:from-primary-600 hover:to-primary-700
                              transition-all duration-200 shadow-warm hover:shadow-warm-lg transform hover:-translate-y-0.5">
                        Lihat Semua Produk
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            @endif
        </div>
    @endif

    <!-- Contact Modal -->
    @if($showContactModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
             wire:click="toggleContactModal">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-warm-xl"
                 wire:click.stop>
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-primary-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-neutral-800 mb-2">Alamat {{ $umkm->business_name }}</h3>
                </div>

                @if($umkm->address)
                    <div class="bg-neutral-50 rounded-xl p-4 mb-6">
                        <p class="text-neutral-700 leading-relaxed">
                            {!! $umkm->formatted_address !!}
                        </p>
                    </div>
                @else
                    <div class="text-center py-8">
                        <p class="text-neutral-500">Alamat belum tersedia</p>
                    </div>
                @endif

                <button wire:click="toggleContactModal"
                        class="w-full px-6 py-3 bg-neutral-100 hover:bg-neutral-200 text-neutral-700
                               font-medium rounded-xl transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    @endif
</div>
