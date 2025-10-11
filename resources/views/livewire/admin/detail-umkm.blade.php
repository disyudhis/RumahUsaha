<?php

use App\Models\UmkmProfile;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public $slug;
    public $umkm;

    // Form fields
    public $business_name;
    public $owner_name;
    public $categories;
    public $kecamatan;
    public $address;
    public $whatsapp;
    public $instagram;
    public $link_website;
    public $description;
    public $asal_komunitas;
    public $is_active;
    public $is_approved;

    public $logo;
    public $existingLogo;

    // UI state
    public $isEditing = false;
    public $showDeleteModal = false;

    public $showProductModal = false;
    public $showDeleteProductModal = false;
    public $editingProduct = null;
    public $productToDelete = null;

    // Product Form Fields
    #[Validate('required|string|max:255')]
    public $product_name = '';

    #[Validate('nullable|string')]
    public $product_description = '';

    #[Validate('required|numeric|min:0')]
    public $product_price = '';

    #[Validate('required|string')]
    public $product_category = '';

    #[Validate('nullable|image|max:2048')]
    public $product_image;

    public $existingProductImage;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->loadUmkm();
    }

    public function loadUmkm()
    {
        $this->umkm = UmkmProfile::with(['user', 'products'])
            ->where('slug', $this->slug)
            ->firstOrFail();

        // Populate form fields
        $this->business_name = $this->umkm->business_name;
        $this->owner_name = $this->umkm->owner_name;
        $this->categories = $this->umkm->categories;
        $this->kecamatan = $this->umkm->kecamatan;
        $this->address = $this->umkm->address;
        $this->whatsapp = $this->umkm->whatsapp;
        $this->instagram = $this->umkm->instagram;
        $this->link_website = $this->umkm->link_website;
        $this->description = $this->umkm->description;
        $this->asal_komunitas = $this->umkm->asal_komunitas;
        $this->is_active = $this->umkm->is_active;
        $this->is_approved = $this->umkm->is_approved;
        $this->existingLogo = $this->umkm->logo;
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;
        if (!$this->isEditing) {
            $this->loadUmkm(); // Reset form if cancelled
        }
    }

    public function updateUmkm()
    {
        $this->validate([
            'business_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'categories' => 'required|string',
            'kecamatan' => 'required|string|max:255',
            'address' => 'required|string',
            'whatsapp' => 'required|string|max:20',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $data = [
            'business_name' => $this->business_name,
            'owner_name' => $this->owner_name,
            'categories' => $this->categories,
            'kecamatan' => $this->kecamatan,
            'address' => $this->address,
            'whatsapp' => $this->whatsapp,
            'instagram' => $this->instagram,
            'link_website' => $this->link_website,
            'description' => $this->description,
            'asal_komunitas' => $this->asal_komunitas,
        ];

        // Handle logo upload
        if ($this->logo) {
            // Delete old logo if exists
            if ($this->existingLogo && Storage::exists($this->existingLogo)) {
                Storage::delete($this->existingLogo);
            }
            $data['logo'] = $this->logo->store('umkm-logos', 'public');
        }

        $this->umkm->update($data);

        $this->isEditing = false;
        $this->loadUmkm();

        session()->flash('success', 'Data UMKM berhasil diperbarui!');
    }

    public function confirmDelete()
    {
        $this->showDeleteModal = true;
    }

    public function deleteUmkm()
    {
        // Delete logo if exists
        if ($this->umkm->logo && Storage::exists($this->umkm->logo)) {
            Storage::delete($this->umkm->logo);
        }

        // Delete all products and their images
        foreach ($this->umkm->products as $product) {
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }
            $product->delete();
        }

        $this->umkm->user()->delete();
        $this->umkm->delete();

        session()->flash('success', 'UMKM berhasil dihapus!');
        return redirect()->route('admin.umkm');
    }

    public function toggleApproval()
    {
        try {
            $this->umkm->user->update(['is_approved' => !$this->umkm->is_approved]);
            $this->umkm->update(['is_approved' => !$this->umkm->is_approved, 'is_active' => !$this->umkm->is_approved]);
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui status persetujuan user terkait.');
        }
        $this->loadUmkm();
        session()->flash('success', 'Status persetujuan berhasil diubah!');
    }

    public function openProductModal($productId = null)
    {
        $this->resetProductForm();

        if ($productId) {
            $product = $this->umkm->products()->findOrFail($productId);
            $this->editingProduct = $product->id;
            $this->product_name = $product->name;
            $this->product_description = $product->description;
            $this->product_price = $product->price;
            $this->product_category = $product->category;
            $this->existingProductImage = $product->image;
        }

        $this->showProductModal = true;
    }

    public function closeProductModal()
    {
        $this->showProductModal = false;
        $this->resetProductForm();
    }

    public function resetProductForm()
    {
        $this->editingProduct = null;
        $this->product_name = '';
        $this->product_description = '';
        $this->product_price = '';
        $this->product_category = '';
        $this->product_image = null;
        $this->existingProductImage = null;
        $this->resetValidation();
    }

    public function saveProduct()
    {
        $this->validate([
            'product_name' => 'required|string|max:255',
            'product_description' => 'nullable|string',
            'product_price' => 'required|numeric|min:0',
            'product_category' => 'required|string',
            'product_image' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $this->product_name,
            'description' => $this->product_description,
            'price' => $this->product_price,
            'category' => $this->product_category,
            'umkm_profile_id' => $this->umkm->id,
        ];

        // Handle image upload
        if ($this->product_image) {
            // Delete old image if editing
            if ($this->editingProduct && $this->existingProductImage && Storage::exists($this->existingProductImage)) {
                Storage::delete($this->existingProductImage);
            }
            $data['image'] = $this->product_image->store('product-images', 'public');
        }

        if ($this->editingProduct) {
            $product = $this->umkm->products()->findOrFail($this->editingProduct);
            $product->update($data);
            $message = 'Produk berhasil diperbarui!';
        } else {
            $this->umkm->products()->create($data);
            $message = 'Produk berhasil ditambahkan!';
        }

        $this->closeProductModal();
        $this->loadUmkm();
        session()->flash('success', $message);
    }

    public function confirmDeleteProduct($productId)
    {
        $this->productToDelete = $productId;
        $this->showDeleteProductModal = true;
    }

    public function deleteProduct()
    {
        if ($this->productToDelete) {
            $product = $this->umkm->products()->findOrFail($this->productToDelete);

            // Delete image if exists
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }

            $product->delete();

            $this->showDeleteProductModal = false;
            $this->productToDelete = null;
            $this->loadUmkm();
            session()->flash('success', 'Produk berhasil dihapus!');
        }
    }

    public function toggleProductStatus($productId)
    {
        $product = $this->umkm->products()->findOrFail($productId);
        $product->update(['is_active' => !$product->is_active]);
        $this->loadUmkm();
        session()->flash('success', 'Status produk berhasil diubah!');
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-accent-50 via-white to-primary-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <a href="{{ route('admin.umkm') }}"
                        class="inline-flex items-center text-sm text-secondary-600 hover:text-primary-600 mb-3 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        Kembali ke Daftar UMKM
                    </a>
                    <h1 class="text-3xl font-bold text-secondary-900">Detail UMKM</h1>
                    <p class="text-secondary-600 mt-1">Kelola informasi UMKM dan produk terkait</p>
                </div>
            </div>
        </div>

        {{-- Success Message --}}
        @if (session()->has('success'))
            <div class="mb-6 bg-success-50 border border-success-200 text-success-800 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- UMKM Information Card --}}
                <div class="bg-white rounded-xl shadow-warm border border-accent-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-primary-600 to-fix-400 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                Informasi UMKM
                            </h2>

                            @if (!$isEditing)
                                <button wire:click="toggleEdit"
                                    class="px-4 py-2 bg-white text-primary-600 rounded-lg hover:bg-primary-50 transition-all duration-200 font-medium text-sm shadow-sm">
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="p-6">
                        @if (!$isEditing)
                            {{-- View Mode --}}
                            <div class="space-y-6">
                                {{-- Logo & Business Name --}}
                                <div class="flex items-start space-x-4 pb-6 border-b border-accent-100">
                                    <div class="flex-shrink-0">
                                        @if ($umkm->logo_url)
                                            <img src="{{ $umkm->logo_url }}" alt="Logo"
                                                class="w-24 h-24 rounded-xl object-cover shadow-warm border-2 border-primary-100">
                                        @else
                                            <div
                                                class="w-24 h-24 bg-gradient-to-br from-primary-100 to-accent-100 rounded-xl flex items-center justify-center shadow-warm border-2 border-primary-200">
                                                <span
                                                    class="text-2xl font-bold text-primary-600">{{ $umkm->initials }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-2xl font-bold text-secondary-900 mb-1">
                                            {{ $umkm->business_name }}</h3>
                                        <p class="text-secondary-600">{{ $umkm->owner_name }}</p>
                                        <div class="flex items-center gap-2 mt-2">
                                            <span
                                                class="px-3 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-medium">
                                                {{ UmkmProfile::CATEGORIES[$umkm->categories] ?? $umkm->categories }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Details Grid --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-secondary-700 mb-2">Kecamatan</label>
                                        <p class="text-secondary-900">{{ $umkm->kecamatan }}</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700 mb-2">Asal
                                            Komunitas</label>
                                        <p class="text-secondary-900">{{ $umkm->asal_komunitas ?: '-' }}</p>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block text-sm font-medium text-secondary-700 mb-2">Alamat</label>
                                        <p class="text-secondary-900">{{ $umkm->address }}</p>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-secondary-700 mb-2">WhatsApp</label>
                                        <a href="{{ $umkm->whatsapp_url }}" target="_blank"
                                            class="text-primary-600 hover:text-primary-700 flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
                                            </svg>
                                            {{ $umkm->whatsapp }}
                                        </a>
                                    </div>

                                    <div>
                                        <label
                                            class="block text-sm font-medium text-secondary-700 mb-2">Instagram</label>
                                        @if ($umkm->instagram)
                                            <a href="{{ $umkm->instagram_url }}" target="_blank"
                                                class="text-primary-600 hover:text-primary-700 flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                                </svg>
                                                {{ $umkm->instagram }}
                                            </a>
                                        @else
                                            <p class="text-secondary-600">-</p>
                                        @endif
                                    </div>

                                    @if ($umkm->link_website)
                                        <div class="md:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-secondary-700 mb-2">Website</label>
                                            <a href="{{ $umkm->link_website }}" target="_blank"
                                                class="text-primary-600 hover:text-primary-700 flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                                </svg>
                                                {{ $umkm->link_website }}
                                            </a>
                                        </div>
                                    @endif

                                    @if ($umkm->description)
                                        <div class="md:col-span-2">
                                            <label
                                                class="block text-sm font-medium text-secondary-700 mb-2">Deskripsi</label>
                                            <p class="text-secondary-900 whitespace-pre-line">{{ $umkm->description }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            {{-- Edit Mode --}}
                            <form wire:submit="updateUmkm">
                                <div class="space-y-6">
                                    {{-- Logo Upload --}}
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700 mb-2">Logo
                                            UMKM</label>
                                        <div class="flex items-center space-x-4">
                                            @if ($logo)
                                                <img src="{{ $logo->temporaryUrl() }}"
                                                    class="w-24 h-24 rounded-xl object-cover shadow-warm">
                                            @elseif ($existingLogo)
                                                <img src="{{ Storage::url($existingLogo) }}"
                                                    class="w-24 h-24 rounded-xl object-cover shadow-warm">
                                            @else
                                                <div
                                                    class="w-24 h-24 bg-accent-100 rounded-xl flex items-center justify-center">
                                                    <svg class="w-12 h-12 text-accent-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <input type="file" wire:model="logo" accept="image/*"
                                                class="block w-full text-sm text-secondary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                        </div>
                                        @error('logo')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Business Name --}}
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700 mb-2">Nama Usaha
                                            *</label>
                                        <input type="text" wire:model="business_name"
                                            class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        @error('business_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Owner Name --}}
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700 mb-2">Nama Pemilik
                                            *</label>
                                        <input type="text" wire:model="owner_name"
                                            class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        @error('owner_name')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Category --}}
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700 mb-2">Kategori
                                            *</label>
                                        <select wire:model="categories"
                                            class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                            <option value="">Pilih Kategori</option>
                                            @foreach (UmkmProfile::CATEGORIES as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('categories')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Kecamatan --}}
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700 mb-2">Kecamatan
                                            *</label>
                                        <input type="text" wire:model="kecamatan"
                                            class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        @error('kecamatan')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Asal Komunitas --}}
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700 mb-2">Asal
                                            Komunitas</label>
                                        <input type="text" wire:model="asal_komunitas"
                                            class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>

                                    {{-- Address --}}
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700 mb-2">Alamat
                                            *</label>
                                        <textarea wire:model="address" rows="3"
                                            class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                                        @error('address')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- WhatsApp --}}
                                    <div>
                                        <label class="block text-sm font-medium text-secondary-700 mb-2">WhatsApp
                                            *</label>
                                        <input type="text" wire:model="whatsapp" placeholder="08xxxxxxxxxx"
                                            class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                        @error('whatsapp')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    {{-- Instagram --}}
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-secondary-700 mb-2">Instagram</label>
                                        <input type="text" wire:model="instagram" placeholder="@username"
                                            class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>

                                    {{-- Website --}}
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-secondary-700 mb-2">Website</label>
                                        <input type="url" wire:model="link_website" placeholder="https://"
                                            class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                                    </div>

                                    {{-- Description --}}
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-secondary-700 mb-2">Deskripsi</label>
                                        <textarea wire:model="description" rows="4"
                                            class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div
                                        class="flex items-center justify-end space-x-3 pt-4 border-t border-accent-100">
                                        <button type="button" wire:click="toggleEdit"
                                            class="px-6 py-2 border border-secondary-300 text-secondary-700 rounded-lg hover:bg-secondary-50 transition-colors font-medium">
                                            Batal
                                        </button>
                                        <button type="submit"
                                            class="px-6 py-2 bg-gradient-to-r from-primary-600 to-primary-500 text-white rounded-lg hover:from-primary-700 hover:to-primary-600 shadow-warm transition-all duration-200 font-medium">
                                            Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Products Section --}}
                <div class="bg-white rounded-xl shadow-warm border border-accent-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-accent-600 to-accent-500 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-bold text-white flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Produk UMKM
                            </h2>
                            <button wire:click="openProductModal"
                                class="px-4 py-2 bg-white text-accent-600 rounded-lg hover:bg-accent-50 transition-all duration-200 font-medium text-sm shadow-sm flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Produk
                            </button>
                        </div>
                    </div>

                    <div class="p-6">
                        @if ($umkm->products->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($umkm->products as $product)
                                    <div
                                        class="border border-accent-100 rounded-lg p-4 hover:shadow-warm transition-all duration-200">
                                        <div class="flex items-start space-x-4">
                                            @if ($product->image)
                                                <img src="{{ Storage::url($product->image) }}"
                                                    alt="{{ $product->name }}"
                                                    class="w-20 h-20 rounded-lg object-cover">
                                            @else
                                                <div
                                                    class="w-20 h-20 bg-accent-100 rounded-lg flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-accent-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-secondary-900 mb-1">{{ $product->name }}
                                                </h4>
                                                <p class="text-primary-600 font-bold mb-2">Rp
                                                    {{ number_format($product->price, 0, ',', '.') }}</p>
                                                <div class="flex items-center gap-2">
                                                    <span
                                                        class="px-2 py-1 bg-accent-100 text-accent-700 rounded-full text-xs font-medium">
                                                        {{ $product->category_name }}
                                                    </span>
                                                    <button wire:click="toggleProductStatus({{ $product->id }})"
                                                        class="px-2 py-1 {{ $product->is_active ? 'bg-success-100 text-success-700' : 'bg-secondary-100 text-secondary-700' }} rounded-full text-xs font-medium hover:opacity-80 transition-opacity">
                                                        {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="flex flex-col gap-2">
                                                <button wire:click="openProductModal({{ $product->id }})"
                                                    class="p-2 text-primary-600 hover:bg-primary-50 rounded-lg transition-colors"
                                                    title="Edit">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button wire:click="confirmDeleteProduct({{ $product->id }})"
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                    title="Hapus">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 text-accent-300 mx-auto mb-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <p class="text-secondary-600 mb-4">Belum ada produk yang ditambahkan</p>
                                <button wire:click="openProductModal"
                                    class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium text-sm">
                                    Tambah Produk Pertama
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Status Card --}}
                <div class="bg-white rounded-xl shadow-warm border border-accent-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-fix-400 to-fix-100 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Status & Aksi
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        {{-- Approval Status --}}
                        <div class="flex items-center justify-between p-4 bg-accent-50 rounded-lg">
                            <div>
                                <p class="text-sm font-medium text-secondary-700">Status Disetujui</p>
                                <p class="text-xs text-secondary-600 mt-1">Verifikasi admin</p>
                            </div>
                            <button wire:click="toggleApproval"
                                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 {{ $umkm->is_approved ? 'bg-success-600' : 'bg-secondary-300' }}">
                                <span
                                    class="translate-x-0 pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $umkm->is_approved ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </div>

                        {{-- Delete Button --}}
                        <button wire:click="confirmDelete"
                            class="w-full px-4 py-3 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors font-medium text-sm border border-red-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus UMKM
                        </button>
                    </div>
                </div>

                {{-- Info Card --}}
                <div class="bg-white rounded-xl shadow-warm border border-accent-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-secondary-800 to-secondary-700 px-6 py-4">
                        <h3 class="text-lg font-bold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informasi Tambahan
                        </h3>
                    </div>

                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-xs text-secondary-600 mb-1">Pemilik Akun</p>
                            <p class="text-sm font-medium text-secondary-900">{{ $umkm->user->name }}</p>
                            <p class="text-xs text-secondary-600">{{ $umkm->user->email }}</p>
                        </div>

                        <div class="border-t border-accent-100 pt-4">
                            <p class="text-xs text-secondary-600 mb-1">Tanggal Dibuat</p>
                            <p class="text-sm font-medium text-secondary-900">
                                {{ $umkm->created_at->format('d M Y, H:i') }}</p>
                        </div>

                        <div class="border-t border-accent-100 pt-4">
                            <p class="text-xs text-secondary-600 mb-1">Terakhir Diupdate</p>
                            <p class="text-sm font-medium text-secondary-900">
                                {{ $umkm->updated_at->format('d M Y, H:i') }}</p>
                        </div>

                        <div class="border-t border-accent-100 pt-4">
                            <p class="text-xs text-secondary-600 mb-1">Total Produk</p>
                            <p class="text-sm font-medium text-secondary-900">{{ $umkm->products->count() }} Produk
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-secondary-900 bg-opacity-50 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showDeleteModal', false)">
            <div class="bg-white rounded-2xl shadow-warm-xl max-w-md w-full p-6 transform transition-all" @click.stop>
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-secondary-900 mb-2">Hapus UMKM?</h3>
                <p class="text-secondary-600 mb-6">
                    Apakah Anda yakin ingin menghapus <strong>{{ $umkm->business_name }}</strong>?
                    Tindakan ini akan menghapus semua produk dan data terkait secara permanen dan tidak dapat
                    dibatalkan.
                </p>

                <div class="flex items-center space-x-3">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="flex-1 px-4 py-2 border border-secondary-300 text-secondary-700 rounded-lg hover:bg-secondary-50 transition-colors font-medium">
                        Batal
                    </button>
                    <button wire:click="deleteUmkm"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium shadow-warm">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Product Modal --}}
    @if ($showProductModal)
        <div class="fixed inset-0 bg-secondary-900 bg-opacity-50 z-50 flex items-center justify-center p-4"
            wire:click.self="closeProductModal">
            <div class="bg-white rounded-2xl shadow-warm-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
                <div class="sticky top-0 bg-gradient-to-r from-accent-600 to-accent-500 px-6 py-4 rounded-t-2xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white">
                            {{ $editingProduct ? 'Edit Produk' : 'Tambah Produk Baru' }}
                        </h3>
                        <button wire:click="closeProductModal"
                            class="text-white hover:text-accent-100 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>

                <form wire:submit="saveProduct" class="p-6">
                    <div class="space-y-5">
                        {{-- Product Image --}}
                        <div>
                            <label class="block text-sm font-medium text-secondary-700 mb-2">Foto Produk</label>
                            <div class="flex items-center space-x-4">
                                @if ($product_image)
                                    <img src="{{ $product_image->temporaryUrl() }}"
                                        class="w-24 h-24 rounded-lg object-cover shadow-warm border-2 border-accent-200">
                                @elseif ($existingProductImage)
                                    <img src="{{ Storage::url($existingProductImage) }}"
                                        class="w-24 h-24 rounded-lg object-cover shadow-warm border-2 border-accent-200">
                                @else
                                    <div
                                        class="w-24 h-24 bg-accent-100 rounded-lg flex items-center justify-center border-2 border-accent-200">
                                        <svg class="w-12 h-12 text-accent-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <input type="file" wire:model="product_image" accept="image/*"
                                        class="block w-full text-sm text-secondary-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-accent-50 file:text-accent-700 hover:file:bg-accent-100">
                                    <p class="text-xs text-secondary-500 mt-1">PNG, JPG, JPEG (Max. 2MB)</p>
                                </div>
                            </div>
                            @error('product_image')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Product Name --}}
                        <div>
                            <label class="block text-sm font-medium text-secondary-700 mb-2">Nama Produk *</label>
                            <input type="text" wire:model="product_name"
                                placeholder="Contoh: Keripik Singkong Original"
                                class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                            @error('product_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="block text-sm font-medium text-secondary-700 mb-2">Kategori *</label>
                            <select wire:model="product_category"
                                class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                                <option value="">Pilih Kategori</option>
                                @foreach (App\Models\Product::CATEGORIES as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('product_category')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Price --}}
                        <div>
                            <label class="block text-sm font-medium text-secondary-700 mb-2">Harga *</label>
                            <div class="relative">
                                <span class="absolute left-4 top-2.5 text-secondary-500">Rp</span>
                                <input type="number" wire:model="product_price" placeholder="50000"
                                    class="w-full pl-12 pr-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-transparent">
                            </div>
                            @error('product_price')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-secondary-700 mb-2">Deskripsi</label>
                            <textarea wire:model="product_description" rows="4" placeholder="Deskripsikan produk Anda..."
                                class="w-full px-4 py-2 border border-accent-200 rounded-lg focus:ring-2 focus:ring-accent-500 focus:border-transparent"></textarea>
                            @error('product_description')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex items-center justify-end space-x-3 mt-6 pt-4 border-t border-accent-100">
                        <button type="button" wire:click="closeProductModal"
                            class="px-6 py-2 border border-secondary-300 text-secondary-700 rounded-lg hover:bg-secondary-50 transition-colors font-medium">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-gradient-to-r from-accent-600 to-accent-500 text-white rounded-lg hover:from-accent-700 hover:to-accent-600 shadow-warm transition-all duration-200 font-medium">
                            {{ $editingProduct ? 'Simpan Perubahan' : 'Tambah Produk' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Delete Product Modal --}}
    @if ($showDeleteProductModal)
        <div class="fixed inset-0 bg-secondary-900 bg-opacity-50 z-50 flex items-center justify-center p-4"
            wire:click.self="$set('showDeleteProductModal', false)">
            <div class="bg-white rounded-2xl shadow-warm-xl max-w-md w-full p-6 transform transition-all" @click.stop>
                <div class="flex items-center justify-center w-12 h-12 rounded-full bg-red-100 mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-xl font-bold text-secondary-900 mb-2">Hapus Produk?</h3>
                <p class="text-secondary-600 mb-6">
                    Apakah Anda yakin ingin menghapus produk ini? Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="flex items-center space-x-3">
                    <button wire:click="$set('showDeleteProductModal', false)"
                        class="flex-1 px-4 py-2 border border-secondary-300 text-secondary-700 rounded-lg hover:bg-secondary-50 transition-colors font-medium">
                        Batal
                    </button>
                    <button wire:click="deleteProduct"
                        class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium shadow-warm">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
