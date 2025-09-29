<?php
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use App\Models\UmkmProfile;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    // Account Information
    public $name = '';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';

    // Business Information
    public $business_name = '';
    public $owner_name = '';
    public $address = '';
    public $kecamatan = '';
    public $category = '';
    public $whatsapp = '';
    public $instagram = '';
    public $description = '';
    public $logo;
    public $terms = false;

    // UI State
    public $isSubmitting = false;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->mixedCase()->numbers()],
            'business_name' => ['required', 'string', 'max:255', 'min:3'],
            'owner_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s]+$/'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'kecamatan' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'in:' . implode(',', array_keys(UmkmProfile::CATEGORIES))],
            'whatsapp' => ['required', 'string', 'regex:/^[8][0-9]{8,12}$/'],
            'instagram' => ['nullable', 'string', 'max:30', 'regex:/^[a-zA-Z0-9._]+$/'],
            'description' => ['required', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function updatedName($value)
    {
        // Auto-fill owner name jika belum diisi
        if (empty($this->owner_name)) {
            $this->owner_name = $value;
        }
    }

    // Tambahkan computed property untuk memberikan opsi
    public function canAutoFillOwner()
    {
        return !empty($this->name) && empty($this->owner_name);
    }

    public function getCategories()
    {
        return UmkmProfile::CATEGORIES;
    }

    public function submit()
    {
        if ($this->isSubmitting) {
            return;
        }

        $this->isSubmitting = true;

        try {
            // Reset errors sebelum validasi
            $this->resetErrorBag();

            $validatedData = $this->validate();

            DB::transaction(function () use ($validatedData) {
                $user = User::create([
                    'name' => $validatedData['name'],
                    'email' => $validatedData['email'],
                    'password' => Hash::make($validatedData['password']),
                    'user_type' => User::ROLE_UMKM_OWNER,
                    'is_approved' => false,
                ]);

                $logoPath = null;
                if ($this->logo) {
                    $logoPath = $this->logo->store('umkm/logos', 'public');
                }

                UmkmProfile::create([
                    'user_id' => $user->id,
                    'business_name' => $validatedData['business_name'],
                    'owner_name' => $validatedData['owner_name'],
                    'address' => $validatedData['address'],
                    'kecamatan' => $validatedData['kecamatan'],
                    'categories' => $validatedData['category'],
                    'whatsapp' => $validatedData['whatsapp'],
                    'instagram' => $validatedData['instagram'],
                    'description' => $validatedData['description'],
                    'logo' => $logoPath,
                    'is_active' => false,
                    'is_approved' => false,
                ]);
            });

            session()->flash('success', 'Pendaftaran berhasil! Akun Anda sedang menunggu persetujuan dari admin. Kami akan mengirimkan notifikasi melalui email setelah akun disetujui.');
            $this->resetForm();

            // Dispatch event untuk menampilkan modal konfirmasi
            $this->dispatch('showRegistrationSuccess');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Tangkap validation exception khusus
            \Log::error('Validation Error: ', $e->errors());

            // Scroll ke error pertama
            $this->dispatch('scrollToError');
        } catch (\Exception $e) {
            \Log::error('UMKM Registration Error: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat mendaftarkan akun. Silakan coba lagi.');
        } finally {
            $this->isSubmitting = false;
        }
    }

    private function resetForm()
    {
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'business_name', 'owner_name', 'address', 'kecamatan', 'category', 'whatsapp', 'instagram', 'description', 'logo', 'terms']);
    }
}; ?>

<div>
    <div class="min-h-screen bg-gradient-to-br from-fix-300/20 via-white to-fix-200/30 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Success/Error Messages --}}
            @if (session()->has('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
            @endif

            @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Header Section --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-fix-400 rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z" />
                    </svg>
                </div>
                <h1 class="text-3xl font-aleo font-bold text-black mb-2">Daftar Sebagai Pemilik UMKM</h1>
                <p class="text-lg font-inter text-black max-w-2xl mx-auto">
                    Bergabunglah dengan komunitas UMKM BIZHOUSE.ID dan mulai memasarkan produk Anda kepada tetangga
                    sekitar
                </p>
            </div>

            {{-- Registration Form --}}
            <div class="bg-white rounded-2xl shadow-warm-lg border border-accent-100 overflow-hidden">
                <div class="bg-fix-400 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-white font-semibold">Form Pendaftaran</h2>
                        @if ($isSubmitting)
                        <div class="flex items-center text-white text-sm">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Memproses...
                        </div>
                        @endif
                    </div>
                </div>

                <form wire:submit="submit" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-8">
                    {{-- Progress Indicator --}}
                    <div class="mb-8 bg-white rounded-lg p-4 shadow-sm border border-accent-200">
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center">
                                <div
                                    class="w-6 h-6 bg-primary-500 text-white rounded-full flex items-center justify-center text-xs mr-2">
                                    1</div>
                                <span class="text-primary-600 font-medium">Informasi Akun</span>
                            </div>
                            <div class="flex items-center">
                                <div
                                    class="w-6 h-6 bg-primary-500 text-white rounded-full flex items-center justify-center text-xs mr-2">
                                    2</div>
                                <span class="text-primary-600 font-medium">Profil Bisnis</span>
                            </div>
                            <div class="flex items-center">
                                <div
                                    class="w-6 h-6 bg-accent-300 text-white rounded-full flex items-center justify-center text-xs mr-2">
                                    3</div>
                                <span class="text-accent-600">Selesai</span>
                            </div>
                        </div>
                        <div class="mt-3 bg-accent-200 rounded-full h-2">
                            <div class="bg-primary-500 h-2 rounded-full" style="width: 66%"></div>
                        </div>
                    </div>
                    <div>
                        {{-- Account Information Section dengan penjelasan --}}
                        <div class="flex items-center mb-6">
                            <div
                                class="w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-secondary-800">Informasi Akun</h2>
                                <p class="text-sm text-secondary-600">Data pribadi Anda untuk login ke platform</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            {{-- Full Name --}}
                            <div class="sm:col-span-1">
                                <label for="name" class="block text-sm font-medium text-secondary-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="name" wire:model.blur="name" required
                                    class="w-full px-4 py-3 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('name') border-red-300 @enderror"
                                    placeholder="Masukkan nama lengkap Anda">
                                @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div class="sm:col-span-1">
                                <label for="email" class="block text-sm font-medium text-secondary-700 mb-2">
                                    Alamat Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" wire:model.blur="email" required
                                    class="w-full px-4 py-3 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('email') border-red-300 @enderror"
                                    placeholder="nama@email.com">
                                @error('email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Password dengan real-time validation --}}
                            <div class="sm:col-span-1">
                                <label for="password" class="block text-sm font-medium text-secondary-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password" wire:model.live="password" required
                                        class="w-full px-4 py-3 pr-12 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('password') border-red-300 @enderror"
                                        placeholder="Minimal 8 karakter">
                                    <button type="button" onclick="togglePassword('password')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-400 hover:text-secondary-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Password strength indicator --}}
                                @if (!empty($password))
                                <div class="mt-2 space-y-1">
                                    <div class="flex items-center text-xs">
                                        <span
                                            class="@if (strlen($password) >= 8) text-green-600 @else text-red-600 @endif">
                                            ✓ Minimal 8 karakter
                                        </span>
                                    </div>
                                    <div class="flex items-center text-xs">
                                        <span
                                            class="@if (preg_match('/[a-z]/', $password) && preg_match('/[A-Z]/', $password)) text-green-600 @else text-red-600 @endif">
                                            ✓ Huruf besar dan kecil
                                        </span>
                                    </div>
                                    <div class="flex items-center text-xs">
                                        <span
                                            class="@if (preg_match('/[0-9]/', $password)) text-green-600 @else text-red-600 @endif">
                                            ✓ Mengandung angka
                                        </span>
                                    </div>
                                </div>
                                @endif

                                @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div class="sm:col-span-1">
                                <label for="password_confirmation"
                                    class="block text-sm font-medium text-secondary-700 mb-2">
                                    Konfirmasi Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password_confirmation"
                                        wire:model.blur="password_confirmation" required
                                        class="w-full px-4 py-3 pr-12 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                        placeholder="Ulangi password">
                                    <button type="button" onclick="togglePassword('password_confirmation')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary-400 hover:text-secondary-600">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Business Information Section --}}
                    <div class="border-t border-accent-200 pt-8">
                        <div class="flex items-center mb-6">
                            <div
                                class="w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-semibold text-secondary-800">Profil Bisnis</h2>
                                <p class="text-sm text-secondary-600">Informasi tentang usaha yang akan Anda daftarkan
                                </p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            {{-- Business Name --}}
                            <div>
                                <label for="business_name" class="block text-sm font-medium text-secondary-700 mb-2">
                                    Nama Usaha <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="business_name" wire:model.blur="business_name" required
                                    class="w-full px-4 py-3 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('business_name') border-red-300 @enderror"
                                    placeholder="Contoh: Warung Makan Ibu Sari">
                                @error('business_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Owner Name dengan auto-fill helper --}}
                            <div>
                                <label for="owner_name" class="block text-sm font-medium text-secondary-700 mb-2">
                                    Nama Pemilik <span class="text-red-500">*</span>
                                </label>

                                @if ($this->canAutoFillOwner())
                                <div class="mb-2 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-sm text-blue-700 mb-2">Apakah nama pemilik sama dengan nama
                                        akun?</p>
                                    <button type="button" wire:click="$set('owner_name', '{{ $name }}')"
                                        class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full hover:bg-blue-200">
                                        Gunakan "{{ $name }}"
                                    </button>
                                </div>
                                @endif

                                <input type="text" id="owner_name" wire:model.blur="owner_name" required disabled
                                    class="w-full px-4 py-3 border bg-gray-50 border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('owner_name') border-red-300 @enderror"
                                    placeholder="Nama pemilik usaha">
                                @error('owner_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Business Address --}}
                            <div>
                                <label for="address" class="block text-sm font-medium text-secondary-700 mb-2">
                                    Alamat Usaha <span class="text-red-500">*</span>
                                </label>
                                <textarea id="address" wire:model.blur="address" rows="3" required
                                    class="w-full px-4 py-3 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('address') border-red-300 @enderror"
                                    placeholder="Alamat lengkap usaha Anda (termasuk RT/RW, kelurahan, kecamatan)"></textarea>
                                @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                {{-- Kecamatan --}}
                                <div>
                                    <label for="kecamatan" class="block text-sm font-medium text-secondary-700 mb-2">
                                        Kecamatan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="kecamatan" wire:model.blur="kecamatan" required
                                        class="w-full px-4 py-3 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('kecamatan') border-red-300 @enderror"
                                        placeholder="Contoh: Kecamatan Sukajadi">
                                    @error('kecamatan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Category --}}
                                <div>
                                    <label for="category" class="block text-sm font-medium text-secondary-700 mb-2">
                                        Kategori Usaha <span class="text-red-500">*</span>
                                    </label>
                                    <select id="category" wire:model.blur="category" required
                                        class="w-full px-4 py-3 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('category') border-red-300 @enderror">
                                        <option value="">Pilih kategori usaha</option>
                                        @foreach ($this->getCategories() as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Contact Information --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                {{-- WhatsApp --}}
                                <div>
                                    <label for="whatsapp" class="block text-sm font-medium text-secondary-700 mb-2">
                                        Nomor WhatsApp <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary-500">
                                            <span class="text-sm">+62</span>
                                        </div>
                                        <input type="tel" id="whatsapp" wire:model.blur="whatsapp" required
                                            class="w-full pl-12 pr-4 py-3 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('whatsapp') border-red-300 @enderror"
                                            placeholder="812345678900">
                                    </div>
                                    <p class="mt-1 text-xs text-secondary-500">
                                        Format: 8123456789 (tanpa +62 atau 0 di depan)
                                    </p>
                                    @error('whatsapp')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Instagram --}}
                                <div>
                                    <label for="instagram" class="block text-sm font-medium text-secondary-700 mb-2">
                                        Instagram (Opsional)
                                    </label>
                                    <div class="relative">
                                        <div class="absolute left-3 top-1/2 -translate-y-1/2 text-secondary-500">
                                            <span class="text-sm">@</span>
                                        </div>
                                        <input type="text" id="instagram" wire:model.blur="instagram"
                                            class="w-full pl-8 pr-4 py-3 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('instagram') border-red-300 @enderror"
                                            placeholder="username_anda">
                                    </div>
                                    <p class="mt-1 text-xs text-secondary-500">
                                        Username Instagram tanpa tanda @
                                    </p>
                                    @error('instagram')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            {{-- Business Description --}}
                            <div>
                                <label for="description" class="block text-sm font-medium text-secondary-700 mb-2">
                                    Deskripsi Usaha <span class="text-red-500">*</span>
                                </label>
                                <textarea id="description" wire:model.live="description" rows="4" required
                                    class="w-full px-4 py-3 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('description') border-red-300 @enderror"
                                    placeholder="Ceritakan tentang usaha Anda, produk yang dijual, keunikan usaha, dsb. (minimal 50 karakter)"></textarea>
                                <div class="flex justify-between mt-1">
                                    <div class="text-xs text-secondary-500">
                                        Minimal 50 karakter untuk deskripsi yang baik
                                    </div>
                                    <div class="text-xs text-secondary-500">
                                        <span
                                            class="@if (strlen($description) < 50) text-red-500 @elseif(strlen($description) < 100) text-yellow-500 @else text-green-500 @endif">
                                            {{ strlen($description) }}
                                        </span>/1000
                                    </div>
                                </div>
                                @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Logo Upload --}}
                            <div wire:ignore>
                                <label for="logo" class="block text-sm font-medium text-secondary-700 mb-2">
                                    Logo Usaha (Opsional)
                                </label>
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div id="logoPreview"
                                            class="w-20 h-20 bg-accent-100 border-2 border-dashed border-accent-300 rounded-lg flex items-center justify-center overflow-hidden">
                                            @if ($logo)
                                            <img src="{{ $logo->temporaryUrl() }}" alt="Logo Preview"
                                                class="w-full h-full object-cover rounded-lg">
                                            @else
                                            <svg class="w-8 h-8 text-accent-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" id="logo" wire:model="logo" accept="image/*"
                                            class="w-full px-4 py-3 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors @error('logo') border-red-300 @enderror">
                                        <p class="mt-1 text-xs text-secondary-500">
                                            JPG, PNG, atau GIF. Maksimal 2MB. Rasio persegi (1:1) disarankan.
                                        </p>
                                        @if ($logo)
                                        <div class="mt-2">
                                            <button type="button" wire:click="$set('logo', null)"
                                                class="text-xs text-red-600 hover:text-red-800">
                                                Hapus logo
                                            </button>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Terms and Conditions --}}
                    {{-- <div class="border-t border-accent-200 pt-8">
                        <div class="flex items-start space-x-3">
                            <input type="checkbox" id="terms" wire:model="terms" required
                                class="w-4 h-4 mt-1 text-primary-600 bg-white border-accent-300 rounded focus:ring-primary-500 focus:ring-2">
                            <label for="terms" class="text-sm text-secondary-700">
                                Saya menyetujui <a href="#"
                                    class="text-primary-600 hover:text-primary-700 underline">Syarat dan Ketentuan</a>
                                serta <a href="#" class="text-primary-600 hover:text-primary-700 underline">Kebijakan
                                    Privasi</a>
                                BIZHOUSE.ID. Saya juga setuju untuk menerima komunikasi terkait platform melalui email
                                dan WhatsApp.
                            </label>
                        </div>
                        @error('terms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div> --}}

                    {{-- Submit Button --}}
                    <div class="border-t border-accent-200 pt-8">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                                class="flex-1 bg-fix-400 text-white px-8 py-4 rounded-lg font-semibold hover:bg-fix-500 focus:ring-4 focus:ring-primary-200 transition-all duration-200 flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed">
                                <div wire:loading.remove wire:target="submit" class="flex items-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                    Daftar Sekarang
                                </div>
                                <div wire:loading wire:target="submit" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Memproses...
                                </div>
                            </button>
                            <a href="{{ route('home') }}"
                                class="flex-1 sm:flex-none bg-white text-secondary-700 px-8 py-4 rounded-lg font-semibold border border-accent-200 hover:bg-accent-50 focus:ring-4 focus:ring-accent-200 transition-all duration-200 text-center">
                                Kembali
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-amber-700">
                            <strong>Nama Lengkap</strong> adalah nama Anda sebagai pemilik akun. <br>
                            <strong>Nama Pemilik</strong> adalah nama yang akan ditampilkan sebagai pemilik usaha (bisa
                            sama atau berbeda).
                        </p>
                    </div>
                </div>
            </div>

            {{-- Loading Overlay --}}
            <div wire:loading.flex wire:target="submit"
                class="fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
                <div class="bg-white rounded-lg p-6 max-w-sm mx-4">
                    <div class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-primary-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        <span>Mendaftarkan akun Anda...</span>
                    </div>
                </div>
            </div>
        </div>

        <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50"
            style="display: none;">
            <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                <div class="mb-4">
                    <div class="w-16 h-16 bg-green-100 rounded-full mx-auto flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-secondary-800 mb-2">Pendaftaran Berhasil!</h3>
                    <p class="text-secondary-600 mb-6">
                        Terima kasih telah mendaftar. Akun Anda sedang menunggu persetujuan dari admin.
                        Kami akan mengirimkan notifikasi melalui email setelah akun disetujui.
                    </p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-2 flex-shrink-0" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <div class="text-left text-sm text-blue-700">
                                <p class="font-medium mb-1">Langkah selanjutnya:</p>
                                <ul class="space-y-1">
                                    <li>• Admin akan meninjau pendaftaran Anda</li>
                                    <li>• Proses persetujuan maksimal 1x24 jam</li>
                                    <li>• Cek email Anda secara berkala</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="closeSuccessModal()"
                    class="w-full bg-fix-400 text-white px-6 py-3 rounded-lg font-semibold hover:bg-fix-500 transition-colors">
                    Kembali ke Beranda
                </button>
            </div>
        </div>
    </div>

    {{-- JavaScript for UI enhancements --}}
    @push('scripts')
    <script>
        // Password visibility toggle
            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
                field.setAttribute('type', type);
            }

            // WhatsApp formatting
            document.addEventListener('DOMContentLoaded', function() {
                const whatsappField = document.getElementById('whatsapp');
                if (whatsappField) {
                    whatsappField.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/[^0-9]/g, '');
                        if (value.length > 0 && value.charAt(0) === '0') {
                            value = '8' + value.substring(1);
                        }
                        if (value.length > 13) {
                            value = value.substring(0, 13);
                        }
                        e.target.value = value;
                        @this.set('whatsapp', value);
                    });
                }

                // Instagram formatting
                const instagramField = document.getElementById('instagram');
                if (instagramField) {
                    instagramField.addEventListener('input', function(e) {
                        let value = e.target.value.replace(/^@/, '');
                        e.target.value = value;
                        @this.set('instagram', value);
                    });
                }
            });

            // Scroll to first error on validation failure
            document.addEventListener('livewire:init', () => {
                Livewire.on('scrollToError', () => {
                    setTimeout(() => {
                        const firstError = document.querySelector('.border-red-300');
                        if (firstError) {
                            firstError.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                            firstError.focus();
                        }
                    }, 100);
                });
            });

            // Show success modal
            document.addEventListener('livewire:init', () => {
                Livewire.on('showRegistrationSuccess', () => {
                    document.getElementById('successModal').style.display = 'flex';
                });
            });

            function closeSuccessModal() {
                document.getElementById('successModal').style.display = 'none';
                window.location.href = "{{ route('home') }}";
            }

            // Close modal when clicking outside
            document.getElementById('successModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeSuccessModal();
                }
            });
    </script>
    @endpush
</div>