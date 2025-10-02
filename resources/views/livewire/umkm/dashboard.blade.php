<?php

use Livewire\Volt\Component;
use App\Models\Product;
use App\Models\UmkmProfile;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public $umkmProfile;
    public $totalProducts = 0;
    public $activeProducts = 0;
    public $pendingProducts = 0;
    public $totalViews = 0;
    public $recentProducts = [];
    public $maxProducts = 3; // Batas maksimal produk

    public function mount()
    {
        $this->umkmProfile = Auth::user()->umkmProfile ?? null;

        if ($this->umkmProfile) {
            $this->loadStats();
            $this->loadRecentProducts();
        }
    }

    private function loadStats()
    {
        $products = $this->umkmProfile->products();

        $this->totalProducts = $products->count();
        $this->activeProducts = $products->where('is_active', true)->count();
        $this->pendingProducts = $products->where('is_active', false)->count();

        // Jika ada kolom views di table products, uncomment baris ini
        // $this->totalViews = $products->sum('views');
    }

    private function loadRecentProducts()
    {
        $this->recentProducts = $this->umkmProfile->products()->latest()->take(3)->get();
    }

    public function canAddProduct()
    {
        return $this->totalProducts < $this->maxProducts;
    }
}; ?>

<div>
    <div class="py-4 max-w-6xl mx-auto">
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-warm p-6 border border-neutral-100">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-neutral-800 mb-2">
                            🏪 Dashboard UMKM
                        </h1>
                        <p class="text-neutral-600">
                            @if ($umkmProfile)
                                Selamat datang, {{ $umkmProfile->business_name }}! Kelola produk dan bisnis Anda dengan
                                mudah
                            @else
                                Lengkapi profil UMKM Anda untuk mulai berjualan
                            @endif
                        </p>
                    </div>
                    <div class="hidden sm:flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-sm text-neutral-500">Total Produk</div>
                            <div class="text-2xl font-bold text-primary-600">{{ $totalProducts }}/{{ $maxProducts }}
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-neutral-500">Produk Aktif</div>
                            <div class="text-2xl font-bold text-success-600">{{ $activeProducts }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!$umkmProfile)
            {{-- Profile Setup Alert --}}
            {{-- <div class="mb-8">
            <div class="bg-accent-50 border border-accent-200 rounded-xl p-6">
                <div class="flex items-start">
                    <div class="text-accent-500 text-2xl mr-4">⚠️</div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-accent-800 mb-2">Profil UMKM Belum Lengkap</h3>
                        <p class="text-accent-700 mb-4">Anda perlu melengkapi profil UMKM sebelum dapat menambahkan
                            produk dan mulai berjualan.</p>
                        <a href="{{ route('umkm.profile') }}"
                            class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors shadow-warm">
                            Lengkapi Profil UMKM
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}
        @else
            {{-- Quick Actions --}}
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-neutral-800 mb-4">Aksi Cepat</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @if ($this->canAddProduct())
                        <a href="{{ route('umkm.products') }}"
                            class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-lg shadow-warm p-6 hover:shadow-warm-lg transition-all border border-primary-200">
                            <div class="text-center">
                                <div class="text-3xl mb-2">➕</div>
                                <h3 class="font-medium text-primary-800 mb-1">Tambah Produk</h3>
                                <p class="text-sm text-primary-600">Daftarkan produk baru</p>
                                <p class="text-xs text-primary-500 mt-2">Sisa slot: {{ $maxProducts - $totalProducts }}
                                </p>
                            </div>
                        </a>
                    @else
                        <div
                            class="bg-neutral-50 rounded-lg shadow-sm p-6 border border-neutral-200 opacity-60 cursor-not-allowed">
                            <div class="text-center">
                                <div class="text-3xl mb-2">🔒</div>
                                <h3 class="font-medium text-neutral-600 mb-1">Tambah Produk</h3>
                                <p class="text-sm text-neutral-500">Batas maksimal tercapai</p>
                                <p class="text-xs text-neutral-400 mt-2">{{ $totalProducts }}/{{ $maxProducts }} produk
                                </p>
                            </div>
                        </div>
                    @endif

                    {{-- <a href="{{ route('umkm.products') }}"
                    class="bg-white rounded-lg shadow-warm p-6 hover:shadow-warm-lg transition-all border border-neutral-100">
                    <div class="text-center">
                        <div class="text-3xl mb-2">📦</div>
                        <h3 class="font-medium text-neutral-800 mb-1">Kelola Produk</h3>
                        <p class="text-sm text-neutral-600">Edit dan atur produk</p>
                    </div>
                </a> --}}

                    {{-- <a href="{{ route('umkm.profile') }}"
                    class="bg-white rounded-lg shadow-warm p-6 hover:shadow-warm-lg transition-all border border-neutral-100">
                    <div class="text-center">
                        <div class="text-3xl mb-2">⚙️</div>
                        <h3 class="font-medium text-neutral-800 mb-1">Pengaturan</h3>
                        <p class="text-sm text-neutral-600">Atur profil bisnis</p>
                    </div>
                </a> --}}

                    {{-- <a href="#" class="bg-white rounded-lg shadow-warm p-6 hover:shadow-warm-lg transition-all border border-neutral-100">
                    <div class="text-center">
                        <div class="text-3xl mb-2">📊</div>
                        <h3 class="font-medium text-neutral-800 mb-1">Statistik</h3>
                        <p class="text-sm text-neutral-600">Lihat performa bisnis</p>
                    </div>
                </a> --}}
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Recent Products --}}
                <div class="bg-white rounded-xl shadow-warm p-6 border border-neutral-100">
                    @if ($recentProducts->count() > 0)
                        <livewire:umkm.list-product :view-mode="'compact'" :limit="3" :show-header="true"
                            :show-filters="false" :show-pagination="false" />
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-8">
                            <div class="text-4xl mb-3">📦</div>
                            <h3 class="font-medium text-neutral-800 mb-2">Belum Ada Produk</h3>
                            <p class="text-neutral-600 text-sm mb-4">
                                Mulai dengan menambahkan produk pertama Anda
                            </p>
                            @if ($this->canAddProduct())
                                <a href="{{ route('umkm.products') }}"
                                    class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors shadow-warm">
                                    <span class="mr-2">+</span>
                                    Tambah Produk
                                </a>
                            @else
                                <p class="text-sm text-neutral-500 italic">Batas maksimal produk telah tercapai</p>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Product Limit Info --}}
                <div
                    class="bg-gradient-to-br from-accent-50 via-accent-100 to-primary-50 rounded-xl p-6 border border-accent-200 shadow-warm">
                    <h2 class="text-lg font-semibold text-neutral-800 mb-4 flex items-center">
                        <span class="mr-2">ℹ️</span>
                        Informasi Penting
                    </h2>

                    <div class="space-y-4">
                        {{-- Product Limit Card --}}
                        <div class="bg-white/80 backdrop-blur-sm rounded-lg p-4 border border-primary-200">
                            <div class="flex items-start">
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                                    <span class="text-2xl">📊</span>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-neutral-800 text-base mb-2">Batas Maksimal Produk</h4>
                                    <p class="text-neutral-700 text-sm mb-3">
                                        Setiap UMKM dapat memasarkan <span class="font-bold text-primary-700">maksimal
                                            {{ $maxProducts }} produk</span> di platform ini.
                                    </p>
                                    <div class="bg-primary-50 rounded-lg p-3 border border-primary-200">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-medium text-neutral-700">Penggunaan Slot:</span>
                                            <span
                                                class="text-sm font-bold text-primary-700">{{ $totalProducts }}/{{ $maxProducts }}</span>
                                        </div>
                                        <div class="w-full bg-neutral-200 rounded-full h-2.5">
                                            <div class="bg-gradient-to-r from-primary-400 to-primary-600 h-2.5 rounded-full transition-all duration-500"
                                                style="width: {{ ($totalProducts / $maxProducts) * 100 }}%"></div>
                                        </div>
                                        @if ($totalProducts >= $maxProducts)
                                            <p class="text-xs text-primary-600 mt-2 font-medium">
                                                ⚠️ Anda telah mencapai batas maksimal produk
                                            </p>
                                        @else
                                            <p class="text-xs text-success-600 mt-2">
                                                ✓ Anda masih bisa menambahkan {{ $maxProducts - $totalProducts }}
                                                produk lagi
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Tips --}}
                        <div class="bg-white/60 backdrop-blur-sm rounded-lg p-4 border border-accent-200">
                            <h4 class="font-medium text-neutral-800 text-sm mb-3 flex items-center">
                                <span class="mr-2">💡</span>
                                Tips Memaksimalkan Slot Produk
                            </h4>
                            <ul class="space-y-2 text-xs text-neutral-700">
                                <li class="flex items-start">
                                    <span class="text-primary-500 mr-2 mt-0.5">•</span>
                                    <span>Pilih produk terbaik dan paling laris untuk dipasarkan</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-primary-500 mr-2 mt-0.5">•</span>
                                    <span>Gunakan foto berkualitas tinggi untuk setiap produk</span>
                                </li>
                                <li class="flex items-start">
                                    <span class="text-primary-500 mr-2 mt-0.5">•</span>
                                    <span>Update informasi produk secara berkala</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div
                    class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-lg shadow-warm p-4 text-center border border-primary-200">
                    <div class="text-2xl font-bold text-primary-700 mb-1">{{ $totalProducts }}/{{ $maxProducts }}
                    </div>
                    <div class="text-sm text-primary-600">Total Produk</div>
                </div>

                <div
                    class="bg-gradient-to-br from-success-50 to-success-100 rounded-lg shadow-warm p-4 text-center border border-success-200">
                    <div class="text-2xl font-bold text-success-700 mb-1">{{ $activeProducts }}</div>
                    <div class="text-sm text-success-600">Produk Aktif</div>
                </div>

                <div
                    class="bg-gradient-to-br from-accent-50 to-accent-100 rounded-lg shadow-warm p-4 text-center border border-accent-200">
                    <div class="text-2xl font-bold text-accent-700 mb-1">{{ $pendingProducts }}</div>
                    <div class="text-sm text-accent-600">Produk Nonaktif</div>
                </div>

                <div
                    class="bg-gradient-to-br from-secondary-50 to-secondary-100 rounded-lg shadow-warm p-4 text-center border border-secondary-200">
                    <div class="text-2xl font-bold text-secondary-700 mb-1">{{ $maxProducts - $totalProducts }}</div>
                    <div class="text-sm text-secondary-600">Slot Tersisa</div>
                </div>
            </div>
        @endif
    </div>
</div>
