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
}; ?>

<div>
    <div class="py-4 max-w-6xl mx-auto">
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">
                            🏪 Dashboard UMKM
                        </h1>
                        <p class="text-gray-600">
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
                            <div class="text-sm text-gray-500">Total Produk</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $totalProducts }}</div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">Produk Aktif</div>
                            <div class="text-2xl font-bold text-green-600">{{ $activeProducts }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (!$umkmProfile)
            {{-- Profile Setup Alert --}}
            {{-- <div class="mb-8">
            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                <div class="flex items-start">
                    <div class="text-yellow-400 text-2xl mr-4">⚠️</div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-yellow-800 mb-2">Profil UMKM Belum Lengkap</h3>
                        <p class="text-yellow-700 mb-4">Anda perlu melengkapi profil UMKM sebelum dapat menambahkan
                            produk dan mulai berjualan.</p>
                        <a href="{{ route('umkm.profile') }}"
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 transition-colors">
                            Lengkapi Profil UMKM
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}
        @else
            {{-- Quick Actions --}}
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Aksi Cepat</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="{{ route('umkm.products') }}"
                        class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                        <div class="text-center">
                            <div class="text-3xl mb-2">➕</div>
                            <h3 class="font-medium text-gray-900 mb-1">Tambah Produk</h3>
                            <p class="text-sm text-gray-500">Daftarkan produk baru</p>
                        </div>
                    </a>

                    {{-- <a href="{{ route('umkm.products') }}"
                    class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="text-center">
                        <div class="text-3xl mb-2">📦</div>
                        <h3 class="font-medium text-gray-900 mb-1">Kelola Produk</h3>
                        <p class="text-sm text-gray-500">Edit dan atur produk</p>
                    </div>
                </a> --}}

                    {{-- <a href="{{ route('umkm.profile') }}"
                    class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="text-center">
                        <div class="text-3xl mb-2">⚙️</div>
                        <h3 class="font-medium text-gray-900 mb-1">Pengaturan</h3>
                        <p class="text-sm text-gray-500">Atur profil bisnis</p>
                    </div>
                </a> --}}

                    {{-- <a href="#" class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                    <div class="text-center">
                        <div class="text-3xl mb-2">📊</div>
                        <h3 class="font-medium text-gray-900 mb-1">Statistik</h3>
                        <p class="text-sm text-gray-500">Lihat performa bisnis</p>
                    </div>
                </a> --}}
                </div>
            </div>

            {{-- Main Content Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                {{-- Recent Products --}}
                <div class="bg-white rounded-xl shadow-sm p-6">
                    @if ($recentProducts->count() > 0)
                        <livewire:umkm.list-product :view-mode="'compact'" :limit="1" :show-header="true"
                            :show-filters="false" :show-pagination="false" />
                    @else
                        {{-- Empty State --}}
                        <div class="text-center py-8">
                            <div class="text-4xl mb-3">📦</div>
                            <h3 class="font-medium text-gray-900 mb-2">Belum Ada Produk</h3>
                            <p class="text-gray-500 text-sm mb-4">
                                Mulai dengan menambahkan produk pertama Anda
                            </p>
                            <a href="{{ route('umkm.products') }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                                <span class="mr-2">+</span>
                                Tambah Produk
                            </a>
                        </div>
                    @endif
                </div>

                {{-- Business Tips --}}
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6">
                    <h2 class="text-lg font-semibold text-green-800 mb-4 flex items-center">
                        <span class="mr-2">💡</span>
                        Tips Bisnis
                    </h2>

                    <div class="space-y-3">
                        <div class="flex items-start bg-white bg-opacity-50 rounded-lg p-3">
                            <span class="text-green-600 mr-3 mt-0.5">✓</span>
                            <div>
                                <h4 class="font-medium text-green-800 text-sm">Foto Produk Berkualitas</h4>
                                <p class="text-green-700 text-xs mt-1">Gunakan foto yang jelas dan menarik untuk
                                    meningkatkan daya tarik produk</p>
                            </div>
                        </div>

                        <div class="flex items-start bg-white bg-opacity-50 rounded-lg p-3">
                            <span class="text-green-600 mr-3 mt-0.5">✓</span>
                            <div>
                                <h4 class="font-medium text-green-800 text-sm">Deskripsi Lengkap</h4>
                                <p class="text-green-700 text-xs mt-1">Berikan informasi detail tentang produk dan cara
                                    pemesanan</p>
                            </div>
                        </div>

                        <div class="flex items-start bg-white bg-opacity-50 rounded-lg p-3">
                            <span class="text-green-600 mr-3 mt-0.5">✓</span>
                            <div>
                                <h4 class="font-medium text-green-800 text-sm">Update Berkala</h4>
                                <p class="text-green-700 text-xs mt-1">Perbarui status produk dan informasi secara rutin
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600 mb-1">{{ $totalProducts }}</div>
                    <div class="text-sm text-gray-500">Total Produk</div>
                </div>

                <div class="bg-white rounded-lg shadow-sm p-4 text-center">
                    <div class="text-2xl font-bold text-green-600 mb-1">{{ $activeProducts }}</div>
                    <div class="text-sm text-gray-500">Produk Aktif</div>
                </div>
            </div>
        @endif

        {{-- Getting Started Section --}}
        {{-- <div class="bg-blue-50 rounded-xl p-6">
            <h2 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                <span class="mr-2">🚀</span>
                {{ $umkmProfile ? 'Tips Mengoptimalkan Bisnis' : 'Mulai Berjualan di BIZHOUSE.ID' }}
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white rounded-lg p-4">
                    <div
                        class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm mb-3">
                        1</div>
                    <h3 class="font-medium text-gray-900 mb-2">
                        {{ $umkmProfile ? 'Daftarkan Lebih Banyak Produk' : 'Daftarkan Produk' }}
                    </h3>
                    <p class="text-gray-600 text-sm">Upload foto dan informasi produk Anda dengan lengkap</p>
                </div>

                <div class="bg-white rounded-lg p-4">
                    <div
                        class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm mb-3">
                        2</div>
                    <h3 class="font-medium text-gray-900 mb-2">
                        {{ $umkmProfile ? 'Optimalkan Profil' : 'Kelola Pesanan' }}
                    </h3>
                    <p class="text-gray-600 text-sm">
                        {{ $umkmProfile ? 'Lengkapi informasi kontak dan deskripsi bisnis' : 'Pantau dan respons pesanan
                        dari pelanggan dengan cepat' }}
                    </p>
                </div>

                <div class="bg-white rounded-lg p-4">
                    <div
                        class="w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center font-bold text-sm mb-3">
                        3</div>
                    <h3 class="font-medium text-gray-900 mb-2">Kembangkan Bisnis</h3>
                    <p class="text-gray-600 text-sm">Gunakan fitur promosi untuk meningkatkan penjualan</p>
                </div>
            </div>
        </div> --}}
    </div>
</div>
