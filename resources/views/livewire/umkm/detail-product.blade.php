<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public Product $product;
    public $slug;

    #[Validate('required|string|max:255')]
    public $name = '';

    #[Validate('required|string')]
    public $description = '';

    #[Validate('required|numeric|min:0')]
    public $price = '';

    #[Validate('required|in:kuliner,fashion,jasa,kerajinan,kecantikan,kesehatan,pariwisata,pertanian,digital,edukasi,lainnya')]
    public $category = '';

    #[Validate('nullable|image|max:2048')]
    public $new_image;

    public $is_active = true;
    public $current_image = '';
    public $isEditing = false;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->loadProduct();
    }

    public function loadProduct()
    {
        $this->product = Product::where('slug', $this->slug)
            ->where('umkm_profile_id', auth()->user()->umkmProfile->id)
            ->firstOrFail();

        $this->name = $this->product->name;
        $this->description = $this->product->description;
        $this->price = $this->product->price;
        $this->category = $this->product->category;
        $this->is_active = $this->product->is_active;
        $this->current_image = $this->product->image;
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        if (!$this->isEditing) {
            $this->loadProduct();
            $this->new_image = null;
        }
    }

    public function updateProduct()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category,
            'is_active' => true,
        ];

        if ($this->new_image) {
            // Delete old image if exists
            if ($this->current_image && Storage::disk('public')->exists($this->current_image)) {
                Storage::disk('public')->delete($this->current_image);
            }

            $imagePath = $this->new_image->store('product-images', 'public');
            $data['image'] = $imagePath;
            $this->current_image = $imagePath;
        }

        $this->product->update($data);
        $this->loadProduct();
        $this->isEditing = false;
        $this->new_image = null;

        session()->flash('success', 'Produk berhasil diperbarui!');
    }

    public function cancelEdit()
    {
        $this->isEditing = false;
        $this->loadProduct();
        $this->new_image = null;
        $this->resetValidation();
    }

    public function deleteProduct()
    {
        // Delete image if exists
        if ($this->product->image && Storage::disk('public')->exists($this->product->image)) {
            Storage::disk('public')->delete($this->product->image);
        }

        $this->product->delete();

        session()->flash('success', 'Produk berhasil dihapus!');
        return redirect()->route('umkm.products');
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-neutral-50 via-accent-50 to-primary-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('umkm.products') }}"
                class="inline-flex items-center gap-2 text-primary-600 hover:text-primary-700 font-medium transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Daftar Produk
            </a>
        </div>

        <!-- Success Message -->
        @if (session()->has('success'))
            <div class="mb-6 bg-success-50 border border-success-200 text-success-800 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Product Image Section -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-warm-lg overflow-hidden sticky top-6">
                    <div class="aspect-square bg-neutral-100 relative">
                        @if ($new_image)
                            <img src="{{ $new_image->temporaryUrl() }}" alt="Preview"
                                class="w-full h-full object-cover">
                            <div
                                class="absolute top-2 right-2 bg-primary-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                Preview
                            </div>
                        @elseif($current_image)
                            <img src="{{ Storage::url($current_image) }}" alt="{{ $name }}"
                                class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-24 h-24 text-neutral-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>

                    @if ($isEditing)
                        <div class="p-4 border-t border-neutral-200">
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Ubah Gambar Produk</label>
                            <input type="file" wire:model="new_image" accept="image/*"
                                class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer">
                            @error('new_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-neutral-500">Format: JPG, PNG. Maksimal 2MB</p>
                        </div>
                    @endif

                    <!-- Status Badge -->
                    <div class="p-4 border-t border-neutral-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-neutral-700">Status Produk</span>
                            @if ($is_active)
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-success-100 text-success-800">
                                    <span class="w-2 h-2 bg-success-500 rounded-full"></span>
                                    Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-neutral-100 text-neutral-800">
                                    <span class="w-2 h-2 bg-neutral-500 rounded-full"></span>
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Details/Form Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-warm-lg overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-primary-500 to-primary-600 px-6 py-5">
                        <div class="flex items-center justify-between">
                            <h1 class="text-2xl font-bold text-white">
                                {{ $isEditing ? 'Edit Produk' : 'Detail Produk' }}
                            </h1>
                            @if (!$isEditing)
                                <button wire:click="toggleEdit"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white text-primary-600 rounded-lg font-medium hover:bg-primary-50 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit Produk
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="p-6">
                        @if ($isEditing)
                            <!-- Edit Form -->
                            <form wire:submit="updateProduct" class="space-y-6">
                                <!-- Nama Produk -->
                                <div>
                                    <label for="name" class="block text-sm font-semibold text-neutral-800 mb-2">
                                        Nama Produk <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="name" wire:model="name"
                                        class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Kategori -->
                                <div>
                                    <label for="category" class="block text-sm font-semibold text-neutral-800 mb-2">
                                        Kategori <span class="text-red-500">*</span>
                                    </label>
                                    <select id="category" wire:model="category"
                                        class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('category') border-red-500 @enderror">
                                        <option value="">Pilih Kategori</option>
                                        @foreach (Product::CATEGORIES as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Harga -->
                                <div>
                                    <label for="price" class="block text-sm font-semibold text-neutral-800 mb-2">
                                        Harga (Rp) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-500 font-medium">Rp</span>
                                        <input type="number" id="price" wire:model="price" min="0"
                                            step="1000"
                                            class="w-full pl-12 pr-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all @error('price') border-red-500 @enderror">
                                    </div>
                                    @error('price')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Deskripsi -->
                                <div>
                                    <label for="description" class="block text-sm font-semibold text-neutral-800 mb-2">
                                        Deskripsi Produk <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="description" wire:model="description" rows="5"
                                        class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all resize-none @error('description') border-red-500 @enderror"></textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-3 pt-4 border-t border-neutral-200">
                                    <button type="submit"
                                        class="flex-1 px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg font-semibold hover:from-primary-600 hover:to-primary-700 focus:ring-4 focus:ring-primary-200 transition-all shadow-warm">
                                        Simpan Perubahan
                                    </button>
                                    <button type="button" wire:click="cancelEdit"
                                        class="px-6 py-3 bg-neutral-100 text-neutral-700 rounded-lg font-semibold hover:bg-neutral-200 transition-colors">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        @else
                            <!-- View Mode -->
                            <div class="space-y-6">
                                <!-- Nama Produk -->
                                <div>
                                    <h3 class="text-sm font-medium text-neutral-500 mb-1">Nama Produk</h3>
                                    <p class="text-xl font-bold text-neutral-900">{{ $name }}</p>
                                </div>

                                <!-- Kategori & Harga -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div class="p-4 bg-accent-50 rounded-xl border border-accent-200">
                                        <h3 class="text-sm font-medium text-neutral-500 mb-1">Kategori</h3>
                                        <p class="text-lg font-semibold text-neutral-900">
                                            {{ $product->category_name }}</p>
                                    </div>
                                    <div class="p-4 bg-primary-50 rounded-xl border border-primary-200">
                                        <h3 class="text-sm font-medium text-neutral-500 mb-1">Harga</h3>
                                        <p class="text-lg font-bold text-primary-600">Rp
                                            {{ number_format($price, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <!-- Deskripsi -->
                                <div>
                                    <h3 class="text-sm font-medium text-neutral-500 mb-2">Deskripsi Produk</h3>
                                    <div class="p-4 bg-neutral-50 rounded-xl border border-neutral-200">
                                        <p class="text-neutral-700 leading-relaxed whitespace-pre-line">
                                            {{ $description }}</p>
                                    </div>
                                </div>

                                <!-- Informasi Tambahan -->
                                <div
                                    class="p-4 bg-gradient-to-br from-accent-50 to-primary-50 rounded-xl border border-accent-200">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <h3 class="text-xs font-medium text-neutral-500 mb-1">Dibuat pada</h3>
                                            <p class="text-sm font-semibold text-neutral-800">
                                                {{ $product->created_at->format('d M Y') }}</p>
                                        </div>
                                        <div>
                                            <h3 class="text-xs font-medium text-neutral-500 mb-1">Terakhir diubah</h3>
                                            <p class="text-sm font-semibold text-neutral-800">
                                                {{ $product->updated_at->format('d M Y') }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete Button -->
                                <div class="pt-4 border-t border-neutral-200">
                                    <button type="button"
                                        wire:confirm="Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan."
                                        wire:click="deleteProduct"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-50 text-red-600 rounded-lg font-medium hover:bg-red-100 border border-red-200 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus Produk
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

