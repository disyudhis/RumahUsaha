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
            'category' => 'required|string|in:kuliner,fashion,kerajinan,jasa,digital,lainnya',
            'image' => 'required|image|max:2048', // max 2MB
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
        // Format price with thousand separators
        if ($propertyName === 'price') {
            $this->price = $this->formatPrice($this->price);
        }

        // Format WhatsApp number
        if ($propertyName === 'whatsapp') {
            $this->whatsapp = $this->formatWhatsAppNumber($this->whatsapp);
        }

        $this->validateOnly($propertyName);
    }

    // Format price
    private function formatPrice($price)
    {
        // Remove non-numeric characters except decimal point
        $cleaned = preg_replace('/[^0-9.]/', '', $price);

        // Ensure only one decimal point
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

        // Remove all non-numeric characters except +
        $cleaned = preg_replace('/[^0-9+]/', '', $number);

        // If starts with 08, replace with +628
        if (str_starts_with($cleaned, '08')) {
            $cleaned = '+62' . substr($cleaned, 1);
        }
        // If starts with 8, add +62
        elseif (str_starts_with($cleaned, '8') && !str_starts_with($cleaned, '+')) {
            $cleaned = '+62' . $cleaned;
        }

        return $cleaned;
    }

    // Get current user's UMKM profile
    private function getUserUmkmProfile()
    {
        return auth()->user()->umkmProfile ?? UmkmProfile::where('user_id', auth()->id())->first();
    }

    // Submit form
    public function submit()
    {
        $this->isSubmitting = true;
        $this->validate();

        try {
            // Check if user has UMKM profile
            $umkmProfile = $this->getUserUmkmProfile();

            if (!$umkmProfile) {
                session()->flash('error', 'Anda harus melengkapi profil UMKM terlebih dahulu.');
                $this->isSubmitting = false;
                return;
            }

            // Prepare product data
            $productData = [
                'name' => $this->name,
                'description' => $this->description,
                'price' => floatval(str_replace(',', '', $this->price)), // Convert to float
                'category' => $this->category,
                'umkm_profile_id' => $umkmProfile->id,
            ];

            // Handle image upload
            if ($this->image) {
                $productData['image'] = $this->image->store('product-images', 'public');
            }

            // Create product
            $product = Product::create($productData);

            // // Update UMKM profile contact info if provided
            // $updateData = [];
            // if ($this->whatsapp && $this->whatsapp !== $umkmProfile->whatsapp) {
            //     $updateData['whatsapp'] = $this->whatsapp;
            // }
            // if ($this->address && $this->address !== $umkmProfile->address) {
            //     $updateData['address'] = $this->address;
            // }

            // if (!empty($updateData)) {
            //     $umkmProfile->update($updateData);
            // }

            // Reset form
            $this->reset();
            $this->isSubmitting = false;

            // Success message
            session()->flash('success', 'Produk berhasil dipublikasikan! 🎉');

            // Dispatch event for additional UI feedback
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

    // Mount - prefill contact info from UMKM profile
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
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
            <span class="text-xl mr-2">📦</span>
            Daftarkan Produk Pertama Anda
        </h2>

        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Product Form --}}
        <form wire:submit="submit" class="space-y-6">
            {{-- Product Image Upload --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Foto Produk <span class="text-red-500">*</span>
                </label>
                <div
                    class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer @error('image') border-red-300 bg-red-50 @enderror">
                    @if ($image)
                        <!-- Preview uploaded image -->
                        <div class="mb-4">
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview Produk"
                                class="mx-auto h-32 w-32 object-cover rounded-lg border-2 border-gray-200">
                            <p class="mt-2 text-sm text-green-600">Foto berhasil dipilih</p>
                            <button type="button" wire:click="$set('image', null)"
                                class="mt-1 text-xs text-red-600 hover:underline">
                                Hapus foto
                            </button>
                        </div>
                    @else
                        <!-- Upload area -->
                        <div class="text-3xl mb-2">📸</div>
                        <div class="text-sm text-gray-600 mb-2">
                            <label for="image" class="cursor-pointer font-medium text-blue-600 hover:text-blue-500">
                                Klik untuk upload foto produk
                            </label>
                        </div>
                        <div class="text-xs text-gray-500">JPG, PNG, maksimal 2MB</div>
                    @endif
                    <input id="image" wire:model="image" type="file" class="hidden" accept="image/*">
                </div>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Loading indicator for image upload -->
            <div wire:loading wire:target="image" class="text-sm text-blue-600 text-center">
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-blue-600 inline" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Mengupload foto...
            </div>

            {{-- Product Details --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Produk <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" wire:model.live="name" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-300 @enderror"
                        placeholder="Contoh: Kue Brownies Premium">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Harga <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500 text-sm">Rp</span>
                        <input type="text" id="price" wire:model.live="price" required
                            class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('price') border-red-300 @enderror"
                            placeholder="50000">
                    </div>
                    @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @if ($price)
                        <p class="mt-1 text-xs text-green-600">{{ $this->formattedPrice }}</p>
                    @endif
                </div>

                <div>
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="category" wire:model.live="category" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category') border-red-300 @enderror">
                        <option value="">Pilih Kategori</option>
                        @foreach ($this->getCategories() as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Deskripsi Produk <span class="text-red-500">*</span>
                </label>
                <textarea id="description" wire:model.live="description" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none @error('description') border-red-300 @enderror"
                    rows="4" placeholder="Ceritakan tentang produk Anda: bahan, keunggulan, cara pemesanan, dll."></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">{{ strlen($description) }}/2000 karakter</p>
            </div>

            {{-- Submit Button --}}
            <div class="pt-4">
                <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 disabled:bg-blue-400 text-white py-3 px-4 rounded-lg font-medium transition-colors flex items-center justify-center">
                    <span wire:loading.remove wire:target="submit" class="flex items-center">
                        <span class="mr-2">🚀</span>
                        Publikasikan Produk
                    </span>
                    <span wire:loading wire:target="submit" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Sedang mempublikasi...
                    </span>
                </button>

                <button type="button" wire:click="resetForm"
                    class="w-full mt-3 bg-gray-100 hover:bg-gray-200 text-gray-700 py-2 px-4 rounded-lg font-medium transition-colors">
                    Reset Form
                </button>
            </div>

            <!-- Helper Info -->
            <div class="mt-6 p-4 bg-amber-50 rounded-lg border border-amber-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-700">
                            <strong>Tips:</strong> Gunakan foto produk yang menarik dan berkualitas baik.
                            Deskripsi yang detail akan membantu calon pembeli memahami produk Anda.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
