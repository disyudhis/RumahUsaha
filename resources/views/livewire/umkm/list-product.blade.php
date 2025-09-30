<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithPagination;

    // Props yang bisa di-pass dari parent
    public $viewMode = 'full'; // 'full' untuk halaman lengkap, 'compact' untuk dashboard
    public $limit = null; // Batasan jumlah produk (untuk mode compact)
    public $showHeader = true; // Tampilkan header atau tidak
    public $showFilters = true; // Tampilkan filter atau tidak
    public $showPagination = true; // Tampilkan pagination atau tidak

    // Filter properties
    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = 'all'; // all, active, inactive

    // Computed property untuk products
    public function with(): array
    {
        $query = Auth::user()->umkmProfile->products()->latest();

        // Apply search
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply category filter
        if ($this->categoryFilter) {
            $query->where('category', $this->categoryFilter);
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            $query->where('is_active', $this->statusFilter === 'active');
        }

        // Apply limit for compact mode
        if ($this->limit) {
            $products = $query->take($this->limit)->get();
            return [
                'products' => $products,
                'categories' => Product::CATEGORIES,
            ];
        }

        // Pagination for full mode
        return [
            'products' => $this->showPagination ? $query->paginate(12) : $query->get(),
            'categories' => Product::CATEGORIES,
        ];
    }

    public function deleteProduct($productId)
    {
        $product = Product::findOrFail($productId);

        // Check ownership
        if ($product->umkm_profile_id !== Auth::user()->umkmProfile->id) {
            session()->flash('error', 'Anda tidak memiliki akses untuk menghapus produk ini.');
            return;
        }

        // Delete image if exists
        if ($product->image) {
            Storage::delete($product->image);
        }

        $product->delete();

        session()->flash('success', 'Produk berhasil dihapus!');
    }

    public function toggleStatus($productId)
    {
        $product = Product::findOrFail($productId);

        // Check ownership
        if ($product->umkm_profile_id !== Auth::user()->umkmProfile->id) {
            session()->flash('error', 'Anda tidak memiliki akses untuk mengubah status produk ini.');
            return;
        }

        $product->is_active = !$product->is_active;
        $product->save();

        session()->flash('success', 'Status produk berhasil diubah!');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function edit($id)
    {
        return $this->redirect(route('umkm.detail-product', ['slug' => $id]), true);
    }
}; ?>

<div>
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center justify-between">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="text-green-600 hover:text-green-800">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    @endif

    {{-- Header Section --}}
    @if ($showHeader)
        <div class="mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">
                        @if ($viewMode === 'compact')
                            📦 Produk Terbaru
                        @else
                            Kelola Produk
                        @endif
                    </h2>
                    @if ($viewMode === 'full')
                        <p class="text-gray-600 text-sm mt-1">
                            Kelola semua produk UMKM Anda dengan mudah
                        </p>
                    @endif
                </div>
                @if ($viewMode === 'full')
                    <a href="{{ route('umkm.products') }}"
                        class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Produk
                    </a>
                @endif
            </div>
        </div>
    @endif

    {{-- Filters Section --}}
    @if ($showFilters && $viewMode === 'full')
        <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Produk</label>
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama atau deskripsi produk..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>

                {{-- Category Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                    <select wire:model.live="categoryFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select wire:model.live="statusFilter"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="all">Semua Status</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Tidak Aktif</option>
                    </select>
                </div>
            </div>
        </div>
    @endif

    {{-- Products Grid --}}
    @if ($products->count() > 0)
        {{-- Compact Mode (Dashboard) --}}
        @if ($viewMode === 'compact')
            <div class="space-y-3">
                @foreach ($products as $product)
                    <div class="bg-gray-50 rounded-lg p-3 hover:bg-gray-100 transition-colors">
                        <div class="flex gap-3">
                            {{-- Product Image --}}
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 rounded-lg overflow-hidden bg-gray-200">
                                    @if ($product->image)
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Product Info --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-medium text-gray-900 text-sm truncate">{{ $product->name }}</h3>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $product->category_name }}</p>
                                        <p class="text-sm font-semibold text-blue-600 mt-1">
                                            Rp {{ $product->formatted_price }}
                                        </p>
                                    </div>

                                    {{-- Status Badge --}}
                                    <div class="flex-shrink-0">
                                        @if ($product->is_active)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                Non-aktif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action Buttons (Compact) --}}
                                <div class="flex gap-1.5 mt-2">
                                    <button wire:click='edit("{{ $product->slug }}")'
                                        class="flex-1 px-2 py-1 bg-blue-50 text-blue-600 text-xs font-medium rounded hover:bg-blue-100 transition-colors">
                                        Edit
                                    </button>
                                    <button wire:click="toggleStatus({{ $product->id }})" wire:loading.attr="disabled"
                                        class="flex-1 px-2 py-1 {{ $product->is_active ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-green-50 text-green-700 hover:bg-green-100' }} text-xs font-medium rounded transition-colors">
                                        <span wire:loading.remove wire:target="toggleStatus({{ $product->id }})">
                                            {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                        </span>
                                        <span wire:loading wire:target="toggleStatus({{ $product->id }})">
                                            ...
                                        </span>
                                    </button>
                                    <button wire:click="deleteProduct({{ $product->id }})"
                                        wire:confirm="Apakah Anda yakin ingin menghapus produk ini?"
                                        class="px-2 py-1 bg-red-50 text-red-600 text-xs font-medium rounded hover:bg-red-100 transition-colors"
                                        wire:loading.attr="disabled">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Full Mode (Products Page) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 xl:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow overflow-hidden group">
                        {{-- Product Image --}}
                        <div class="relative aspect-square overflow-hidden bg-gray-100">
                            @if ($product->image)
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Status Badge --}}
                            <div class="absolute top-2 right-2">
                                @if ($product->is_active)
                                    <span
                                        class="bg-green-500 text-white text-xs font-medium px-2 py-1 rounded-full">Aktif</span>
                                @else
                                    <span
                                        class="bg-gray-500 text-white text-xs font-medium px-2 py-1 rounded-full">Tidak
                                        Aktif</span>
                                @endif
                            </div>
                        </div>

                        {{-- Product Info --}}
                        <div class="p-4">
                            <div class="mb-2">
                                <span
                                    class="text-xs font-medium text-primary-600 bg-primary-50 px-2 py-1 rounded">{{ $product->category_name }}</span>
                            </div>
                            <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $product->description }}</p>
                            <div class="text-primary-600 font-bold text-lg mb-4">
                                Rp {{ $product->formatted_price }}
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex gap-2">
                                <button wire:click='edit("{{ $product->slug }}")'
                                    class="flex-1 text-center px-3 py-2 bg-primary-50 text-primary-600 text-sm font-medium rounded-lg hover:bg-primary-100 transition-colors">
                                    Edit
                                </button>
                                <button wire:click="toggleStatus({{ $product->id }})" wire:loading.attr="disabled"
                                    class="flex-1 px-3 py-2 {{ $product->is_active ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-green-50 text-green-700 hover:bg-green-100' }} text-sm font-medium rounded-lg transition-colors">
                                    <span wire:loading.remove wire:target="toggleStatus({{ $product->id }})">
                                        {{ $product->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </span>
                                    <span wire:loading wire:target="toggleStatus({{ $product->id }})">
                                        Memproses...
                                    </span>
                                </button>
                                <button wire:click="deleteProduct({{ $product->id }})"
                                    wire:confirm="Apakah Anda yakin ingin menghapus produk ini?"
                                    class="px-3 py-2 bg-red-50 text-red-600 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors"
                                    wire:loading.attr="disabled">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Pagination --}}
        @if ($showPagination && $viewMode === 'full')
            <div class="mt-6">
                {{ $products->links() }}
            </div>
        @endif

        {{-- View All Link for Compact Mode --}}
        @if ($viewMode === 'compact' && $limit)
            <div class="mt-4">
                <a href="{{ route('umkm.list-product') }}"
                    class="block w-full text-center px-4 py-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Lihat Semua Produk →
                </a>
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="bg-gray-50 rounded-lg p-8 text-center">
            <div class="text-gray-400 mb-3">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                @if ($search || $categoryFilter || $statusFilter !== 'all')
                    Tidak Ada Produk yang Ditemukan
                @else
                    Belum Ada Produk
                @endif
            </h3>
            <p class="text-gray-600 text-sm mb-4">
                @if ($search || $categoryFilter || $statusFilter !== 'all')
                    Coba ubah filter pencarian Anda
                @else
                    Mulai tambahkan produk pertama Anda
                @endif
            </p>
            @if (!$search && !$categoryFilter && $statusFilter === 'all')
                <a href="{{ route('umkm.products') }}"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Produk
                </a>
            @else
                <button wire:click="$set('search', ''); $set('categoryFilter', ''); $set('statusFilter', 'all')"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    Hapus Semua Filter
                </button>
            @endif
        </div>
    @endif
</div>
