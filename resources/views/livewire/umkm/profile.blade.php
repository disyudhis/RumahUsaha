<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\UmkmProfile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    use WithFileUploads;

    public $umkmProfile;
    public $business_name;
    public $owner_name;
    public $categories;
    public $kecamatan;
    public $address;
    public $whatsapp;
    public $instagram;
    public $link_website;
    public $description;
    public $logo;
    public $existingLogo;
    public $removeLogo = false;

    public $successMessage = '';
    public $errorMessage = '';

    public function mount()
    {
        $this->umkmProfile = Auth::user()->umkmProfile;

        if ($this->umkmProfile) {
            $this->business_name = $this->umkmProfile->business_name;
            $this->owner_name = $this->umkmProfile->owner_name;
            $this->categories = $this->umkmProfile->categories;
            $this->kecamatan = $this->umkmProfile->kecamatan;
            $this->address = $this->umkmProfile->address;
            $this->whatsapp = $this->umkmProfile->whatsapp;
            $this->instagram = $this->umkmProfile->instagram;
            $this->link_website = $this->umkmProfile->link_website;
            $this->description = $this->umkmProfile->description;
            $this->existingLogo = $this->umkmProfile->logo;
        }
    }

    public function updatedLogo()
    {
        $this->validate([
            'logo' => 'image|max:2048', // 2MB Max
        ]);
    }

    public function removeExistingLogo()
    {
        $this->removeLogo = true;
        $this->existingLogo = null;
    }

    public function cancelRemoveLogo()
    {
        $this->removeLogo = false;
        $this->existingLogo = $this->umkmProfile->logo;
    }

    public function updateProfile()
    {
        $this->validate(
            [
                'business_name' => 'required|string|max:255',
                'owner_name' => 'required|string|max:255',
                'categories' => 'required|string',
                'kecamatan' => 'required|string|max:255',
                'address' => 'required|string',
                'whatsapp' => 'required|string|max:20',
                'instagram' => 'nullable|string|max:255',
                'link_website' => 'nullable|url|max:255',
                'description' => 'required|string|',
                'logo' => 'nullable|image|max:2048',
            ],
            [
                'business_name.required' => 'Nama usaha wajib diisi',
                'owner_name.required' => 'Nama pemilik wajib diisi',
                'categories.required' => 'Kategori usaha wajib dipilih',
                'kecamatan.required' => 'Kecamatan wajib diisi',
                'address.required' => 'Alamat lengkap wajib diisi',
                'whatsapp.required' => 'Nomor WhatsApp wajib diisi',
                'description.required' => 'Deskripsi usaha wajib diisi',
                'logo.image' => 'File harus berupa gambar',
                'logo.max' => 'Ukuran logo maksimal 2MB',
                'link_website.url' => 'Format website tidak valid',
            ],
        );

        try {
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
            ];

            // Handle logo upload
            if ($this->logo) {
                // Delete old logo if exists
                if ($this->umkmProfile->logo) {
                    Storage::delete($this->umkmProfile->logo);
                }
                $data['logo'] = $this->logo->store('umkm-logos', 'public');
            } elseif ($this->removeLogo && $this->umkmProfile->logo) {
                // Remove logo if requested
                Storage::delete($this->umkmProfile->logo);
                $data['logo'] = null;
            }

            $this->umkmProfile->update($data);

            $this->successMessage = 'Profil berhasil diperbarui!';
            $this->errorMessage = '';
            $this->logo = null;
            $this->existingLogo = $this->umkmProfile->logo;
            $this->removeLogo = false;

            // Scroll to top to show success message
            $this->dispatch('profile-updated');
        } catch (\Exception $e) {
            $this->errorMessage = 'Terjadi kesalahan: ' . $e->getMessage();
            $this->successMessage = '';
        }
    }

    public function with()
    {
        return [
            'categoryOptions' => UmkmProfile::CATEGORIES,
        ];
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-primary-50 via-white to-accent-50 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('umkm.dashboard') }}"
                    class="mr-4 text-secondary-600 hover:text-primary-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-secondary-900">Edit Profil UMKM</h1>
                    <p class="text-secondary-600 mt-1">Perbarui informasi profil bisnis Anda</p>
                </div>
            </div>
        </div>

        {{-- Alert Messages --}}
        @if ($successMessage)
            <div class="mb-6 bg-success-50 border border-success-200 text-success-800 px-4 py-3 rounded-lg flex items-start shadow-sm"
                x-data="{ show: true }" x-show="show" x-transition>
                <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <p class="font-medium">{{ $successMessage }}</p>
                </div>
                <button @click="show = false" class="ml-3 text-success-600 hover:text-success-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif

        @if ($errorMessage)
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-start shadow-sm"
                x-data="{ show: true }" x-show="show" x-transition>
                <svg class="w-5 h-5 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
                <div class="flex-1">
                    <p class="font-medium">{{ $errorMessage }}</p>
                </div>
                <button @click="show = false" class="ml-3 text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- Form Card --}}
        <div class="bg-white rounded-xl shadow-warm-lg border border-accent-100 overflow-hidden">
            <form wire:submit="updateProfile">
                {{-- Logo Section --}}
                <div class="p-6 bg-gradient-to-r from-primary-50 to-accent-50 border-b border-accent-100">
                    <h3 class="text-lg font-semibold text-secondary-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Logo Usaha
                    </h3>

                    <div class="flex items-start space-x-6">
                        {{-- Current Logo Preview --}}
                        <div class="flex-shrink-0">
                            @if ($existingLogo && !$removeLogo)
                                <div class="relative group">
                                    <img src="{{ Storage::url($existingLogo) }}" alt="Logo"
                                        class="w-32 h-32 rounded-lg object-cover border-2 border-accent-200 shadow-md">
                                    <button type="button" wire:click="removeExistingLogo"
                                        class="absolute inset-0 bg-red-600 bg-opacity-0 group-hover:bg-opacity-75 rounded-lg transition-all duration-200 flex items-center justify-center">
                                        <span
                                            class="text-white opacity-0 group-hover:opacity-100 text-sm font-medium flex items-center">
                                            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            Hapus
                                        </span>
                                    </button>
                                </div>
                            @elseif($logo)
                                <img src="{{ $logo->temporaryUrl() }}" alt="Preview"
                                    class="w-32 h-32 rounded-lg object-cover border-2 border-primary-200 shadow-md">
                            @else
                                <div
                                    class="w-32 h-32 rounded-lg bg-secondary-100 flex items-center justify-center border-2 border-dashed border-secondary-300">
                                    <svg class="w-12 h-12 text-secondary-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Upload Instructions --}}
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-secondary-700 mb-2">
                                Upload Logo Baru
                            </label>
                            <input type="file" wire:model="logo" accept="image/*"
                                class="block w-full text-sm text-secondary-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:cursor-pointer cursor-pointer border border-accent-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">

                            <p class="mt-2 text-xs text-secondary-500">
                                Format: JPG, PNG, maksimal 2MB. Rasio 1:1 (persegi) disarankan.
                            </p>

                            @error('logo')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror

                            <div wire:loading wire:target="logo" class="mt-2">
                                <div class="flex items-center text-sm text-primary-600">
                                    <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Mengunggah...
                                </div>
                            </div>

                            @if ($removeLogo)
                                <div class="mt-3 flex items-center space-x-2">
                                    <span class="text-sm text-red-600">Logo akan dihapus</span>
                                    <button type="button" wire:click="cancelRemoveLogo"
                                        class="text-sm text-primary-600 hover:text-primary-700 font-medium">
                                        Batalkan
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Basic Information --}}
                <div class="p-6 space-y-6">
                    <div>
                        <h3
                            class="text-lg font-semibold text-secondary-900 mb-4 flex items-center border-b border-accent-100 pb-2">
                            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Informasi Dasar
                        </h3>
                    </div>

                    {{-- Business Name --}}
                    <div>
                        <label for="business_name" class="block text-sm font-medium text-secondary-700 mb-2">
                            Nama Usaha <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="business_name" wire:model="business_name"
                            class="w-full px-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors @error('business_name') border-red-500 @enderror"
                            placeholder="Contoh: Warung Makan Sederhana">
                        @error('business_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Owner Name --}}
                    <div>
                        <label for="owner_name" class="block text-sm font-medium text-secondary-700 mb-2">
                            Nama Pemilik <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="owner_name" wire:model="owner_name"
                            class="w-full px-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors @error('owner_name') border-red-500 @enderror"
                            placeholder="Nama lengkap pemilik usaha">
                        @error('owner_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="categories" class="block text-sm font-medium text-secondary-700 mb-2">
                            Kategori Usaha <span class="text-red-500">*</span>
                        </label>
                        <select id="categories" wire:model="categories"
                            class="w-full px-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors @error('categories') border-red-500 @enderror">
                            <option value="">Pilih Kategori</option>
                            @foreach ($categoryOptions as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('categories')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Grid Layout for Kecamatan and WhatsApp --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kecamatan --}}
                        <div>
                            <label for="kecamatan" class="block text-sm font-medium text-secondary-700 mb-2">
                                Kecamatan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="kecamatan" wire:model="kecamatan"
                                class="w-full px-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors @error('kecamatan') border-red-500 @enderror"
                                placeholder="Contoh: Bekasi Utara">
                            @error('kecamatan')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- WhatsApp --}}
                        <div>
                            <label for="whatsapp" class="block text-sm font-medium text-secondary-700 mb-2">
                                Nomor WhatsApp <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="whatsapp" wire:model="whatsapp"
                                class="w-full px-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors @error('whatsapp') border-red-500 @enderror"
                                placeholder="08xxxxxxxxxx">
                            @error('whatsapp')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label for="address" class="block text-sm font-medium text-secondary-700 mb-2">
                            Alamat Lengkap <span class="text-red-500">*</span>
                        </label>
                        <textarea id="address" wire:model="address" rows="3"
                            class="w-full px-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-none @error('address') border-red-500 @enderror"
                            placeholder="Jalan, RT/RW, Kelurahan, Kota/Kabupaten"></textarea>
                        @error('address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Social Media & Website Section --}}
                    {{-- <div class="pt-4">
                        <h3
                            class="text-lg font-semibold text-secondary-900 mb-4 flex items-center border-b border-accent-100 pb-2">
                            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                            </svg>
                            Media Sosial & Website
                        </h3>
                    </div> --}}

                    {{-- Instagram --}}
                    {{-- <div>
                        <label for="instagram" class="block text-sm font-medium text-secondary-700 mb-2">
                            Username Instagram
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-secondary-500 text-sm">@</span>
                            </div>
                            <input type="text" id="instagram" wire:model="instagram"
                                class="w-full pl-8 pr-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors @error('instagram') border-red-500 @enderror"
                                placeholder="username_anda">
                        </div>
                        @error('instagram')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div> --}}

                    {{-- Website --}}
                    {{-- <div>
                        <label for="link_website" class="block text-sm font-medium text-secondary-700 mb-2">
                            Link Website
                        </label>
                        <input type="url" id="link_website" wire:model="link_website"
                            class="w-full px-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors @error('link_website') border-red-500 @enderror"
                            placeholder="https://website-anda.com">
                        @error('link_website')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div> --}}

                    {{-- Description --}}
                    <div class="pt-4">
                        <h3
                            class="text-lg font-semibold text-secondary-900 mb-4 flex items-center border-b border-accent-100 pb-2">
                            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Deskripsi Usaha
                        </h3>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-secondary-700 mb-2">
                            Deskripsi <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" wire:model="description" rows="5"
                            class="w-full px-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors resize-none @error('description') border-red-500 @enderror"
                            placeholder="Ceritakan tentang usaha Anda, produk/jasa yang ditawarkan, keunggulan, dan hal menarik lainnya... (minimal 50 karakter)"></textarea>
                        <div class="flex justify-between items-center mt-1">
                            @error('description')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @else
                                <p class="text-sm text-secondary-500">Minimal 50 karakter</p>
                            @enderror
                            <p class="text-xs text-secondary-400">{{ strlen($description ?? '') }} karakter</p>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="px-6 py-4 bg-secondary-50 border-t border-accent-100 flex items-center justify-between">
                    <div class="text-sm text-secondary-600">
                        <span class="text-red-500">*</span> Wajib diisi
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('umkm.dashboard') }}"
                            class="px-6 py-2.5 border border-accent-200 text-secondary-700 rounded-lg hover:bg-secondary-50 transition-colors font-medium">
                            Batal
                        </a>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-primary-600 to-fix-400 text-white rounded-lg hover:from-primary-700 hover:to-fix-100 transition-all shadow-warm hover:shadow-warm-lg font-medium flex items-center"
                            wire:loading.attr="disabled" wire:target="updateProfile">
                            <span wire:loading.remove wire:target="updateProfile">
                                Simpan Perubahan
                            </span>
                            <span wire:loading wire:target="updateProfile" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Scroll to top on success --}}
    @push('scripts')
        <script>
            document.addEventListener('livewire:initialized', () => {
                Livewire.on('profile-updated', () => {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            });
        </script>
    @endpush
</div>
