<?php

use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\UmkmProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

new class extends Component {
    use WithFileUploads;

    // User registration properties
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    // UMKM Profile properties
    public string $business_name = '';
    public string $owner_name = '';
    public string $categories = '';
    public string $kecamatan = '';
    public string $address = '';
    public string $whatsapp = '';
    public string $instagram = '';
    public string $asal_komunitas = '';
    public string $link_website = '';
    public string $description = '';
    public $logo;

    // UI state
    public bool $isSubmitting = false;

    // Validation rules
    protected function rules()
    {
        return [
            // User validation
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'password_confirmation' => 'required',

            // UMKM Profile validation
            'business_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'categories' => 'required|string|in:' . implode(',', array_keys(UmkmProfile::CATEGORIES)),
            'kecamatan' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'whatsapp' => 'nullable|string|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
            'instagram' => 'nullable|string|regex:/^@?[a-zA-Z0-9._]+$/|max:30',
            'asal_komunitas' => 'nullable|string|max:255',
            'link_website' => 'nullable|url|max:255',
            'description' => 'nullable|string|max:1000',
            'logo' => 'nullable|image|max:1024', // max 1MB
        ];
    }

    // Custom validation messages
    protected $messages = [
        // User messages
        'name.required' => 'Nama lengkap wajib diisi.',
        'name.max' => 'Nama lengkap maksimal 255 karakter.',
        'email.required' => 'Email wajib diisi.',
        'email.email' => 'Format email tidak valid.',
        'email.unique' => 'Email sudah terdaftar.',
        'password.required' => 'Password wajib diisi.',
        'password.min' => 'Password minimal 8 karakter.',
        'password.confirmed' => 'Konfirmasi password tidak cocok.',
        'password_confirmation.required' => 'Konfirmasi password wajib diisi.',

        // UMKM Profile messages
        'business_name.required' => 'Nama usaha wajib diisi.',
        'business_name.max' => 'Nama usaha maksimal 255 karakter.',
        'owner_name.required' => 'Nama pemilik wajib diisi.',
        'owner_name.max' => 'Nama pemilik maksimal 255 karakter.',
        'categories.required' => 'Kategori usaha wajib dipilih.',
        'categories.in' => 'Kategori usaha tidak valid.',
        'kecamatan.required' => 'Kecamatan wajib diisi.',
        'kecamatan.max' => 'Kecamatan maksimal 255 karakter.',
        'address.max' => 'Alamat usaha maksimal 500 karakter.',
        'whatsapp.regex' => 'Format nomor WhatsApp tidak valid.',
        'whatsapp.min' => 'Nomor WhatsApp minimal 10 digit.',
        'whatsapp.max' => 'Nomor WhatsApp maksimal 15 digit.',
        'instagram.regex' => 'Format username Instagram tidak valid.',
        'instagram.max' => 'Username Instagram maksimal 30 karakter.',
        'asal_komunitas.max' => 'Asal komunitas maksimal 255 karakter.',
        'link_website.url' => 'Format URL website tidak valid.',
        'link_website.max' => 'Link website maksimal 255 karakter.',
        'description.max' => 'Deskripsi usaha maksimal 1000 karakter.',
        'logo.image' => 'File harus berupa gambar.',
        'logo.max' => 'Ukuran logo maksimal 1MB.',
    ];

    // Real-time validation and formatting
    public function updated($propertyName)
    {
        // Auto-fill owner name from user name
        if ($propertyName === 'name' && empty($this->owner_name)) {
            $this->owner_name = $this->name;
        }

        // Format WhatsApp number
        if ($propertyName === 'whatsapp') {
            $this->whatsapp = $this->formatWhatsAppNumber($this->whatsapp);
        }

        // Format Instagram username
        if ($propertyName === 'instagram') {
            $this->instagram = $this->formatInstagramUsername($this->instagram);
        }

        $this->validateOnly($propertyName);
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

    // Format Instagram username
    private function formatInstagramUsername($username)
    {
        $username = trim($username);
        return $username;
    }

    // Main submit function
    public function register()
    {
        $this->isSubmitting = true;
        $this->validate();

        try {
            DB::transaction(function () {
                // Create user
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'password' => Hash::make($this->password),
                    'user_type' => User::ROLE_UMKM_OWNER,
                    'is_approved' => true,
                ]);

                // Prepare UMKM profile data
                $profileData = [
                    'user_id' => $user->id,
                    'business_name' => $this->business_name,
                    'owner_name' => $this->owner_name,
                    'categories' => $this->categories,
                    'kecamatan' => $this->kecamatan,
                    'address' => $this->address ?: null,
                    'whatsapp' => $this->whatsapp ?: null,
                    'instagram' => $this->instagram ?: null,
                    'asal_komunitas' => $this->asal_komunitas ?: null,
                    'link_website' => $this->link_website ?: null,
                    'description' => $this->description ?: null,
                    'is_active' => true,
                    'is_approved' => true,
                ];

                // Handle logo upload
                if ($this->logo) {
                    $profileData['logo'] = $this->logo->store('umkm-logos', 'public');
                }

                // Create UMKM profile
                UmkmProfile::create($profileData);

                // Auto login the user
                auth()->login($user);
            });

            // Reset form
            $this->reset();
            $this->isSubmitting = false;

            // Success message
            session()->flash('success', 'Pendaftaran berhasil! Selamat datang di platform UMKM.');

            // Redirect
            return redirect()->route('admin.umkm');
        } catch (\Exception $e) {
            $this->isSubmitting = false;
            session()->flash('error', 'Terjadi kesalahan saat mendaftar. Silakan coba lagi.');
            \Log::error('UMKM Registration Error: ' . $e->getMessage());
        }
    }

    // Reset form
    public function resetForm()
    {
        $this->reset();
        $this->resetValidation();
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-primary-50 via-neutral-50 to-accent-100 py-12 px-4 sm:px-6 lg:px-8">
    <!-- Header Section -->
    <div class="mb-8 text-center">
        <div class="flex items-center justify-center mb-4">
            <div
                class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
        </div>
        <h1 class="text-4xl font-bold text-neutral-900 mb-2 font-aleo">Daftar UMKM</h1>
        <p class="text-lg text-neutral-600">Bergabunglah dengan platform UMKM kami dan kembangkan bisnis Anda</p>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-neutral-100">
        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="mx-6 mt-6 p-4 bg-success-50 border-l-4 border-success-500 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-success-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-success-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mx-6 mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <form wire:submit="register" class="p-8 space-y-8">
            <!-- User Registration Section -->
            <div class="space-y-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <h3 class="ml-3 text-xl font-bold text-neutral-900 font-aleo">Data Akun</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-semibold text-neutral-800 mb-2">
                            Nama Lengkap <span class="text-primary-600">*</span>
                        </label>
                        <input type="text" id="name" wire:model.live="name" placeholder="Masukkan nama lengkap"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('name') border-red-400 @enderror">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-neutral-800 mb-2">
                            Email <span class="text-primary-600">*</span>
                        </label>
                        <input type="email" id="email" wire:model.live="email" placeholder="contoh@email.com"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('email') border-red-400 @enderror">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-neutral-800 mb-2">
                            Password <span class="text-primary-600">*</span>
                        </label>
                        <input type="password" id="password" wire:model.live="password"
                            placeholder="Minimal 8 karakter"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('password') border-red-400 @enderror">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Confirmation -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-neutral-800 mb-2">
                            Konfirmasi Password <span class="text-primary-600">*</span>
                        </label>
                        <input type="password" id="password_confirmation" wire:model.live="password_confirmation"
                            placeholder="Ulangi password"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('password_confirmation') border-red-400 @enderror">
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-neutral-200"></div>

            <!-- UMKM Profile Section -->
            <div class="space-y-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center">
                        <svg class="h-6 w-6 text-secondary-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="ml-3 text-xl font-bold text-neutral-900 font-aleo">Informasi Usaha</h3>
                </div>

                <!-- Business Name & Owner Name -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="business_name" class="block text-sm font-semibold text-neutral-800 mb-2">
                            Nama Usaha <span class="text-primary-600">*</span>
                        </label>
                        <input type="text" id="business_name" wire:model.live="business_name"
                            placeholder="Nama bisnis/usaha"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('business_name') border-red-400 @enderror">
                        @error('business_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="owner_name" class="block text-sm font-semibold text-neutral-800 mb-2">
                            Nama Pemilik <span class="text-primary-600">*</span>
                        </label>
                        <input type="text" id="owner_name" wire:model.live="owner_name"
                            placeholder="Nama pemilik usaha"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('owner_name') border-red-400 @enderror">
                        @error('owner_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if ($owner_name && $owner_name === $name)
                            <p class="mt-1 text-xs text-success-600 font-medium">✓ Otomatis terisi dari nama lengkap
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Category & Kecamatan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="categories" class="block text-sm font-semibold text-neutral-800 mb-2">
                            Kategori Usaha <span class="text-primary-600">*</span>
                        </label>
                        <select id="categories" wire:model.live="categories"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('categories') border-red-400 @enderror">
                            <option value="">Pilih Kategori</option>
                            @foreach (UmkmProfile::CATEGORIES as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('categories')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="kecamatan" class="block text-sm font-semibold text-neutral-800 mb-2">
                            Kecamatan <span class="text-primary-600">*</span>
                        </label>
                        <input type="text" id="kecamatan" wire:model.live="kecamatan"
                            placeholder="Nama kecamatan"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('kecamatan') border-red-400 @enderror">
                        @error('kecamatan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- WhatsApp & Instagram -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="whatsapp"
                            class="block text-sm font-semibold text-neutral-800 mb-2">WhatsApp</label>
                        <input type="tel" id="whatsapp" wire:model.live="whatsapp" placeholder="08123456789"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('whatsapp') border-red-400 @enderror">
                        @error('whatsapp')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if ($whatsapp)
                            <p class="mt-1 text-xs text-success-600 font-medium">✓ Format: {{ $whatsapp }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="instagram"
                            class="block text-sm font-semibold text-neutral-800 mb-2">Instagram</label>
                        <input type="text" id="instagram" wire:model.live="instagram"
                            placeholder="username_instagram"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('instagram') border-red-400 @enderror">
                        @error('instagram')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Asal Komunitas & Link Website -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="asal_komunitas" class="block text-sm font-semibold text-neutral-800 mb-2">Asal
                            Komunitas</label>
                        <input type="text" id="asal_komunitas" wire:model.live="asal_komunitas"
                            placeholder="Nama komunitas (opsional)"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('asal_komunitas') border-red-400 @enderror">
                        @error('asal_komunitas')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="link_website" class="block text-sm font-semibold text-neutral-800 mb-2">Link
                            Website</label>
                        <input type="url" id="link_website" wire:model.live="link_website"
                            placeholder="https://example.com"
                            class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('link_website') border-red-400 @enderror">
                        @error('link_website')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Business Address -->
                <div>
                    <label for="address" class="block text-sm font-semibold text-neutral-800 mb-2">Alamat
                        Usaha</label>
                    <textarea id="address" wire:model.live="address" rows="2" placeholder="Alamat lengkap usaha (opsional)"
                        class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('address') border-red-400 @enderror"></textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-neutral-500">{{ strlen($address) }}/500 karakter</p>
                </div>

                <!-- Business Description -->
                <div>
                    <label for="description" class="block text-sm font-semibold text-neutral-800 mb-2">Deskripsi
                        Usaha</label>
                    <textarea id="description" wire:model.live="description" rows="3"
                        placeholder="Ceritakan tentang usaha Anda, produk/jasa yang ditawarkan (opsional)"
                        class="w-full px-4 py-3 border border-neutral-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors duration-200 bg-neutral-50 @error('description') border-red-400 @enderror"></textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-neutral-500">{{ strlen($description) }}/1000 karakter</p>
                </div>

                <!-- Logo Upload -->
                <div>
                    <label class="block text-sm font-semibold text-neutral-800 mb-3">Upload Logo Usaha</label>
                    <div
                        class="border-2 border-dashed border-neutral-300 rounded-lg p-6 text-center hover:border-primary-400 hover:bg-primary-50 transition-all duration-200 @error('logo') border-red-400 @enderror">
                        @if ($logo)
                            <div class="space-y-3">
                                <img src="{{ $logo->temporaryUrl() }}" alt="Preview Logo"
                                    class="mx-auto h-24 w-24 object-cover rounded-lg border-2 border-primary-200 shadow-md">
                                <div>
                                    <p class="text-sm font-medium text-success-600">✓ Logo berhasil dipilih</p>
                                    <button type="button" wire:click="$set('logo', null)"
                                        class="mt-2 text-xs text-red-600 hover:text-red-700 font-medium hover:underline">
                                        Hapus dan pilih ulang
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="space-y-3">
                                <svg class="mx-auto h-12 w-12 text-neutral-400" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="text-sm">
                                    <label for="logo"
                                        class="cursor-pointer font-semibold text-primary-600 hover:text-primary-700">
                                        Klik untuk upload logo
                                    </label>
                                    <span class="text-neutral-600"> atau drag & drop</span>
                                </div>
                                <p class="text-xs text-neutral-500">JPG, PNG, maksimal 1MB</p>
                            </div>
                        @endif
                        <input id="logo" wire:model="logo" type="file" class="sr-only" accept="image/*">
                    </div>
                    @error('logo')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Loading indicator -->
            <div wire:loading wire:target="logo"
                class="flex items-center justify-center text-sm text-primary-600 bg-primary-50 rounded-lg py-3">
                <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-primary-600" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Mengupload logo...
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 pt-8 border-t border-neutral-200">
                <button type="submit" wire:loading.attr="disabled" wire:target="register"
                    class="flex-1 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 disabled:from-primary-400 disabled:to-primary-400 text-white px-6 py-3 rounded-lg text-sm font-semibold transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl">
                    <span wire:loading.remove wire:target="register" class="flex items-center justify-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Daftar UMKM
                    </span>
                    <span wire:loading wire:target="register" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        Sedang mendaftar...
                    </span>
                </button>

                <button type="button" wire:click="resetForm"
                    class="bg-neutral-100 hover:bg-neutral-200 text-neutral-700 px-6 py-3 rounded-lg text-sm font-semibold transition-colors duration-200">
                    Reset Form
                </button>
            </div>

            <!-- Info Box -->
            <div class="mt-6 p-4 bg-accent-50 rounded-lg border-l-4 border-secondary-600">
                <div class="flex gap-3">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-secondary-600 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-secondary-900">
                            <strong>Perhatian:</strong> Setelah mendaftar, Anda akan otomatis login dan dapat mengakses
                            dashboard UMKM. Pastikan data yang diisi sudah benar karena beberapa informasi akan sulit
                            diubah setelah registrasi.
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
