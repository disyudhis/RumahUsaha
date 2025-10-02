<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\UmkmProfile;

new class extends Component {
    use WithFileUploads;

    // Product properties
    public string $name = '';
    public string $description = '';
    public $price = '';
    public string $category = '';
    public $image;

    // Contact properties
    public string $whatsapp = '';
    public string $address = '';

    // UI state
    public bool $isSubmitting = false;

    // Available categories
    public function getCategories()
    {
        return Product::CATEGORIES;
    }

    // Validation rules
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:' . implode(',', array_keys(Product::CATEGORIES)),
            'image' => 'required|image|max:2048',
            'whatsapp' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
            'address' => 'nullable|string|max:500',
        ];
    }

    // Custom validation messages
    protected $messages = [
        'name.required' => 'Nama produk wajib diisi.',
        'name.max' => 'Nama produk maksimal 255 karakter.',
        'description.required' => 'Deskripsi produk wajib diisi.',
        'description.max' => 'Deskripsi produk maksimal 2000 karakter.',
        'price.required' => 'Harga produk wajib diisi.',
        'price.numeric' => 'Harga harus berupa angka.',
        'price.min' => 'Harga tidak boleh negatif.',
        'category.required' => 'Kategori produk wajib dipilih.',
        'category.in' => 'Kategori yang dipilih tidak valid.',
        'image.required' => 'Foto produk wajib diupload.',
        'image.image' => 'File harus berupa gambar.',
        'image.max' => 'Ukuran foto maksimal 2MB.',
        'whatsapp.required' => 'Nomor WhatsApp wajib diisi.',
        'whatsapp.regex' => 'Format nomor WhatsApp tidak valid.',
        'whatsapp.min' => 'Nomor WhatsApp minimal 10 digit.',
        'whatsapp.max' => 'Nomor WhatsApp maksimal 15 digit.',
        'address.max' => 'Alamat maksimal 500 karakter.',
    ];

    // Real-time validation and formatting
    public function updated($propertyName)
    {
        if ($propertyName === 'price') {
            $this->price = $this->formatPrice($this->price);
        }

        if ($propertyName === 'whatsapp') {
            $this->whatsapp = $this->formatWhatsAppNumber($this->whatsapp);
        }

        $this->validateOnly($propertyName);
    }

    // Format price
    private function formatPrice($price)
    {
        $cleaned = preg_replace('/[^0-9.]/', '', $price);
        $parts = explode('.', $cleaned);
        if (count($parts) > 2) {
            $cleaned = $parts[0] . '.' . $parts[1];
        }
        return $cleaned;
    }

    // Format WhatsApp number
    private function formatWhatsAppNumber($number)
    {
        if (empty($number)) {
            return '';
        }

        $cleaned = preg_replace('/[^0-9+]/', '', $number);

        if (str_starts_with($cleaned, '08')) {
            $cleaned = '+62' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8') && !str_starts_with($cleaned, '+')) {
            $cleaned = '+62' . $cleaned;
        }

        return $cleaned;
    }

    // Get current user's UMKM profile
    private function getUserUmkmProfile()
    {
        return auth()->user()->umkmProfile ?? UmkmProfile::where('user_id', auth()->id())->first();
    }

    private function checkProductLimit()
    {
        $umkmProfile = $this->getUserUmkmProfile();

        if (!$umkmProfile) {
            return false;
        }

        $productCount = $umkmProfile->products()->count();
        return $productCount >= 3;
    }

    private function getProductCount()
    {
        $umkmProfile = $this->getUserUmkmProfile();
        return $umkmProfile ? $umkmProfile->products()->count() : 0;
    }

    // Submit form
    public function submit()
    {
        $this->isSubmitting = true;

        // Check product limit first
        if ($this->checkProductLimit()) {
            session()->flash('error', 'Anda sudah mencapai batas maksimal 3 produk. Hapus produk lama untuk menambahkan produk baru.');
            $this->isSubmitting = false;
            return;
        }

        $this->validate();

        try {
            $umkmProfile = $this->getUserUmkmProfile();

            if (!$umkmProfile) {
                session()->flash('error', 'Anda harus melengkapi profil UMKM terlebih dahulu.');
                $this->isSubmitting = false;
                return;
            }

            $productData = [
                'name' => $this->name,
                'description' => $this->description,
                'price' => floatval(str_replace(',', '', $this->price)),
                'category' => $this->category,
                'umkm_profile_id' => $umkmProfile->id,
            ];

            if ($this->image) {
                $productData['image'] = $this->image->store('product-images', 'public');
            }

            $product = Product::create($productData);

            $this->reset();
            $this->isSubmitting = false;

            session()->flash('success', 'Produk berhasil dipublikasikan! 🎉');
            $this->dispatch('product-created', ['productId' => $product->id]);
        } catch (\Exception $e) {
            $this->isSubmitting = false;
            session()->flash('error', 'Terjadi kesalahan saat mempublikasikan produk.');
            \Log::error('Product Creation Error: ' . $e->getMessage());
        }
    }

    // Reset form
    public function resetForm()
    {
        $this->reset();
        $this->resetValidation();
    }

    // Get formatted price display
    public function getFormattedPriceProperty()
    {
        if (!$this->price) {
            return '';
        }

        $numericPrice = floatval(str_replace(',', '', $this->price));
        return 'Rp ' . number_format($numericPrice, 0, ',', '.');
    }

    // Mount
    public function mount()
    {
        $umkmProfile = $this->getUserUmkmProfile();

        if ($umkmProfile) {
            $this->whatsapp = $umkmProfile->whatsapp ?? '';
            $this->address = $umkmProfile->address ?? '';
        }
    }
}; ?>

<div class="max-w-5xl mx-auto p-4 lg:col-span-2">
    <div class="bg-white rounded-2xl shadow-warm-lg border border-neutral-100 overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gradient-to-r from-primary-50 to-accent-50 px-8 py-6 border-b border-primary-100">
            <div class="flex items-center space-x-3">
                <div class="bg-primary-500 p-3 rounded-xl shadow-warm">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-neutral-900">Daftarkan Produk Baru</h2>
                    <p class="text-sm text-neutral-600 mt-1">Publikasikan produk Anda dan jangkau lebih banyak pelanggan
                    </p>
                </div>
            </div>
        </div>

        @php
            $productCount = $this->getProductCount();
            $remainingSlots = 3 - $productCount;
        @endphp

        @if ($productCount >= 3)
            <div class="mb-6 p-5 bg-amber-50 border-l-4 border-amber-500 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-amber-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-amber-800">
                            Batas Maksimal Tercapai
                        </p>
                        <p class="mt-1 text-sm text-amber-700">
                            Anda sudah mendaftarkan 3 produk (maksimal). Untuk menambahkan produk baru, silakan hapus
                            salah satu produk yang sudah ada terlebih dahulu.
                        </p>
                    </div>
                </div>
            </div>
        @elseif($productCount > 0)
            <div class="mb-6 p-5 bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-semibold text-blue-800">
                            Sisa Slot Produk: {{ $remainingSlots }} dari 3
                        </p>
                        <p class="mt-1 text-sm text-blue-700">
                            Anda masih bisa menambahkan {{ $remainingSlots }} produk lagi.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        <div class="p-8">
            <!-- Success/Error Messages -->
            @if (session()->has('success'))
                <div class="mb-6 p-5 bg-success-50 border-l-4 border-success-500 rounded-lg shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-success-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-semibold text-success-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mb-6 p-5 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($productCount >= 3)
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-amber-100 rounded-full mb-4">
                        <svg class="w-10 h-10 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-neutral-800 mb-2">Batas Maksimal Produk Tercapai</h3>
                    <p class="text-neutral-600 mb-6">Anda sudah mendaftarkan 3 produk. Untuk menambah produk baru, hapus
                        produk lama terlebih dahulu.</p>
                    <a href="{{ route('umkm.dashboard') }}"
                        class="inline-flex items-center px-6 py-3 bg-primary-500 hover:bg-primary-600 text-white rounded-xl font-semibold transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Dashboard
                    </a>
                </div>
            @else
                <form wire:submit="submit" class="space-y-8">
                    {{-- Product Image Upload - BIGGER --}}
                    <div>
                        <label class="block text-base font-semibold text-neutral-800 mb-3">
                            Foto Produk <span class="text-primary-600">*</span>
                        </label>
                        <div
                            class="border-3 border-dashed rounded-2xl overflow-hidden transition-all duration-300
                        @error('image') border-red-400 bg-red-50 @else border-primary-300 bg-gradient-to-br from-primary-50 to-accent-50 hover:border-primary-400 hover:shadow-warm @enderror">
                            @if ($image)
                                <!-- Preview uploaded image - BIGGER -->
                                <div class="p-8 text-center">
                                    <div class="relative inline-block">
                                        <img src="{{ $image->temporaryUrl() }}" alt="Preview Produk"
                                            class="mx-auto h-64 w-64 object-cover rounded-xl shadow-warm-lg border-4 border-white">
                                        <div class="absolute -top-2 -right-2">
                                            <button type="button" wire:click="$set('image', null)"
                                                class="bg-red-500 hover:bg-red-600 text-white rounded-full p-2 shadow-lg transition-all duration-200 hover:scale-110">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        class="mt-4 inline-flex items-center px-4 py-2 bg-success-100 text-success-700 rounded-full">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="font-medium text-sm">Foto berhasil dipilih</span>
                                    </div>
                                </div>
                            @else
                                <!-- Upload area - BIGGER -->
                                <label for="image"
                                    class="block cursor-pointer p-12 text-center hover:bg-primary-100/50 transition-colors">
                                    <div class="flex flex-col items-center justify-center space-y-4">
                                        <div class="bg-primary-500 p-6 rounded-2xl shadow-warm-lg">
                                            <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-lg font-semibold text-neutral-800 mb-2">
                                                Klik untuk upload foto produk
                                            </p>
                                            <p class="text-sm text-neutral-600">
                                                atau drag & drop file di sini
                                            </p>
                                        </div>
                                        <div
                                            class="flex items-center space-x-2 text-xs text-neutral-500 bg-white px-4 py-2 rounded-full">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            <span>JPG, PNG • Maksimal 2MB</span>
                                        </div>
                                    </div>
                                </label>
                            @endif
                            <input id="image" wire:model="image" type="file" class="hidden"
                                accept="image/*">
                        </div>
                        @error('image')
                            <p class="mt-2 text-sm text-red-600 font-medium flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Loading indicator for image upload -->
                    <div wire:loading wire:target="image" class="text-center py-4">
                        <div class="inline-flex items-center px-6 py-3 bg-primary-100 text-primary-700 rounded-full">
                            <svg class="animate-spin h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="font-medium">Mengupload foto...</span>
                        </div>
                    </div>

                    {{-- Product Details --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="lg:col-span-2">
                            <label for="name" class="block text-sm font-semibold text-neutral-800 mb-2">
                                Nama Produk <span class="text-primary-600">*</span>
                            </label>
                            <input type="text" id="name" wire:model.live="name" required
                                class="w-full px-4 py-3 border-2 border-neutral-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all @error('name') border-red-400 bg-red-50 @enderror"
                                placeholder="Contoh: Kue Brownies Premium">
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="price" class="block text-sm font-semibold text-neutral-800 mb-2">
                                Harga <span class="text-primary-600">*</span>
                            </label>
                            <div class="relative">
                                <span
                                    class="absolute left-4 top-1/2 -translate-y-1/2 text-neutral-500 font-medium">Rp</span>
                                <input type="text" id="price" wire:model.live="price" required
                                    class="w-full pl-12 pr-4 py-3 border-2 border-neutral-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all @error('price') border-red-400 bg-red-50 @enderror"
                                    placeholder="50000">
                            </div>
                            @error('price')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                            @if ($price)
                                <p class="mt-2 text-sm text-success-600 font-medium">✓ {{ $this->formattedPrice }}</p>
                            @endif
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-semibold text-neutral-800 mb-2">
                                Kategori <span class="text-primary-600">*</span>
                            </label>
                            <select id="category" wire:model.live="category" required
                                class="w-full px-4 py-3 border-2 border-neutral-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all @error('category') border-red-400 bg-red-50 @enderror">
                                <option value="">Pilih Kategori</option>
                                @foreach ($this->getCategories() as $value => $label)
                                    <option value="{{ $value }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('category')
                                <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-semibold text-neutral-800 mb-2">
                            Deskripsi Produk <span class="text-primary-600">*</span>
                        </label>
                        <textarea id="description" wire:model.live="description" required
                            class="w-full px-4 py-3 border-2 border-neutral-200 rounded-xl focus:ring-2 focus:ring-primary-500 focus:border-primary-500 resize-none transition-all @error('description') border-red-400 bg-red-50 @enderror"
                            rows="5" placeholder="Ceritakan tentang produk Anda: bahan, keunggulan, cara pemesanan, dll."></textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 flex justify-between items-center">
                            <p class="text-xs text-neutral-500">Deskripsi yang detail akan menarik lebih banyak pembeli
                            </p>
                            <p class="text-xs font-medium text-neutral-600">{{ strlen($description) }}/2000 karakter
                            </p>
                        </div>
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-6 space-y-3">
                        <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                            class="w-full bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 disabled:from-primary-300 disabled:to-primary-400 text-white py-4 px-6 rounded-xl font-semibold transition-all duration-200 shadow-warm-lg hover:shadow-warm-xl disabled:cursor-not-allowed transform hover:-translate-y-0.5">
                            <span wire:loading.remove wire:target="submit" class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                </svg>
                                Publikasikan Produk Sekarang
                            </span>
                            <span wire:loading wire:target="submit" class="flex items-center justify-center">
                                <svg class="animate-spin h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Sedang mempublikasi...
                            </span>
                        </button>

                        <button type="button" wire:click="resetForm"
                            class="w-full bg-neutral-100 hover:bg-neutral-200 text-neutral-700 py-3 px-6 rounded-xl font-semibold transition-all duration-200">
                            Reset Form
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>
