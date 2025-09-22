<?php

use Livewire\Volt\Component;
use App\Models\UmkmProfile;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public UmkmProfile $umkm;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showApprovalModal = false;

    // Edit form properties
    #[Validate('required|string|max:255')]
    public $business_name = '';

    #[Validate('required|string|max:255')]
    public $owner_name = '';

    #[Validate('nullable|string')]
    public $address = '';

    #[Validate('nullable|string|max:20')]
    public $whatsapp = '';

    #[Validate('nullable|string|max:100')]
    public $instagram = '';

    #[Validate('nullable|string')]
    public $description = '';

    #[Validate('nullable|url')]
    public $link_website = '';

    #[Validate('nullable|image|max:2048')]
    public $new_logo;

    public $is_active = true;

    public function mount($id)
    {
        $this->umkm = UmkmProfile::with(['products', 'user'])->findOrFail($id);
        $this->initializeEditForm();
    }

    public function initializeEditForm()
    {
        $this->business_name = $this->umkm->business_name;
        $this->owner_name = $this->umkm->owner_name;
        $this->address = $this->umkm->address ?? '';
        $this->whatsapp = $this->umkm->whatsapp ?? '';
        $this->instagram = $this->umkm->instagram ?? '';
        $this->description = $this->umkm->description ?? '';
        $this->link_website = $this->umkm->link_website ?? '';
        $this->is_active = $this->umkm->is_active;
    }

    public function toggleEditModal()
    {
        $this->showEditModal = !$this->showEditModal;
        if (!$this->showEditModal) {
            $this->initializeEditForm();
            $this->new_logo = null;
        }
    }

    public function toggleDeleteModal()
    {
        $this->showDeleteModal = !$this->showDeleteModal;
    }

    public function toggleApprovalModal()
    {
        $this->showApprovalModal = !$this->showApprovalModal;
    }

    public function approveUmkm()
    {
        $this->umkm->update([
            'is_approved' => true,
            'is_active' => true,
        ]);

        session()->flash('success', 'UMKM berhasil disetujui dan diaktifkan!');
        $this->showApprovalModal = false;
        $this->umkm->refresh();
    }

    public function rejectUmkm()
    {
        $this->umkm->update([
            'is_approved' => false,
            'is_active' => false,
        ]);

        session()->flash('success', 'UMKM ditolak dan dinonaktifkan.');
        $this->showApprovalModal = false;
        $this->umkm->refresh();
    }

    public function updateUmkm()
    {
        $this->validate();

        $updateData = [
            'business_name' => $this->business_name,
            'owner_name' => $this->owner_name,
            'address' => $this->address,
            'whatsapp' => $this->whatsapp,
            'instagram' => $this->instagram,
            'description' => $this->description,
            'link_website' => $this->link_website,
            'is_active' => $this->is_active,
        ];

        // Handle logo upload
        if ($this->new_logo) {
            // Delete old logo if exists
            if ($this->umkm->logo && Storage::exists($this->umkm->logo)) {
                Storage::delete($this->umkm->logo);
            }

            $updateData['logo'] = $this->new_logo->store('umkm-logos', 'public');
        }

        $this->umkm->update($updateData);

        session()->flash('success', 'Data UMKM berhasil diperbarui!');
        $this->showEditModal = false;
        $this->umkm->refresh();
    }

    public function deleteUmkm()
    {
        // Delete logo if exists
        if ($this->umkm->logo && Storage::exists($this->umkm->logo)) {
            Storage::delete($this->umkm->logo);
        }

        // Delete associated products and their images
        foreach ($this->umkm->products as $product) {
            if ($product->image && Storage::exists($product->image)) {
                Storage::delete($product->image);
            }
        }

        // Delete UMKM (will cascade delete products due to foreign key)
        $this->umkm->delete();

        session()->flash('success', 'UMKM dan semua produknya berhasil dihapus!');
        return redirect()->route('admin.umkm');
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header with Actions -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex justify-between items-start">
                <div>
                    <div class="flex items-center space-x-3 mb-2">
                        <a href="{{ route('admin.umkm') }}"
                            class="text-gray-500 hover:text-gray-700 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">Detail UMKM</h1>
                    </div>
                    <p class="text-gray-600">Kelola informasi UMKM dan produk-produknya</p>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- Approval Status -->
                    @if (!$umkm->is_approved)
                        <button wire:click="toggleApprovalModal"
                            class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700
                                       text-white font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pending Approval
                        </button>
                    @endif

                    <!-- Action Buttons -->
                    <button wire:click="toggleEditModal"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700
                                   text-white font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit UMKM
                    </button>

                    <button wire:click="toggleDeleteModal"
                        class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700
                                   text-white font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-6">
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- UMKM Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Info Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Informasi UMKM</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <!-- Logo -->
                            <div class="flex-shrink-0">
                                @if ($umkm->hasLogo())
                                    <img src="{{ $umkm->logo_url }}" alt="{{ $umkm->business_name }}"
                                        class="w-20 h-20 rounded-xl object-cover border-2 border-gray-200">
                                @else
                                    <div
                                        class="w-20 h-20 rounded-xl bg-gradient-to-br from-blue-400 to-blue-600
                                              flex items-center justify-center border-2 border-gray-200">
                                        <span class="text-xl font-bold text-white">{{ $umkm->initials }}</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Basic Details -->
                            <div class="flex-1">
                                <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $umkm->business_name }}</h2>
                                <p class="text-gray-600 mb-4">Pemilik: {{ $umkm->owner_name }}</p>

                                <div class="flex items-center space-x-4">
                                    <!-- Status Badges -->
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                               {{ $umkm->is_approved ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ $umkm->is_approved ? 'Disetujui' : 'Pending' }}
                                    </span>

                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                               {{ $umkm->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $umkm->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if ($umkm->description)
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h4 class="font-medium text-gray-900 mb-2">Deskripsi</h4>
                                <p class="text-gray-700 leading-relaxed">{{ $umkm->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Products Section -->
                @if ($umkm->products->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Produk ({{ $umkm->products->count() }})
                            </h3>
                            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Kelola Produk
                            </a>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($umkm->products->take(4) as $product)
                                    <div
                                        class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-start space-x-3">
                                            @if ($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}"
                                                    class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                            @else
                                                <div
                                                    class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-medium text-gray-900 truncate">{{ $product->name }}
                                                </h4>
                                                @if ($product->price)
                                                    <p class="text-blue-600 font-semibold">
                                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                                    </p>
                                                @endif
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium mt-1
                                                           {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ $product->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if ($umkm->products->count() > 4)
                                <div class="mt-4 text-center">
                                    <span class="text-gray-500 text-sm">
                                        dan {{ $umkm->products->count() - 4 }} produk lainnya
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Contact Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Kontak</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @if ($umkm->whatsapp)
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">WhatsApp</p>
                                    <a href="{{ $umkm->whatsapp_url }}" target="_blank"
                                        class="text-green-600 hover:text-green-800 font-medium">
                                        {{ $umkm->whatsapp }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if ($umkm->instagram)
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.621 5.367 11.988 11.988 11.988s11.987-5.367 11.987-11.988C24.004 5.367 18.637.001 12.017.001zM8.449 16.988c-2.428 0-4.399-1.971-4.399-4.399s1.971-4.399 4.399-4.399 4.399 1.971 4.399 4.399-1.971 4.399-4.399 4.399zm7.138 0c-2.428 0-4.399-1.971-4.399-4.399s1.971-4.399 4.399-4.399 4.399 1.971 4.399 4.399-1.971 4.399-4.399 4.399z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Instagram</p>
                                    <a href="{{ $umkm->instagram_url }}" target="_blank"
                                        class="text-pink-600 hover:text-pink-800 font-medium">
                                        {{ $umkm->instagram }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if ($umkm->link_website)
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9v-9m0-9v9" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Website</p>
                                    <a href="{{ $umkm->link_website }}" target="_blank"
                                        class="text-blue-600 hover:text-blue-800 font-medium text-sm truncate block max-w-[180px]">
                                        {{ $umkm->link_website }}
                                    </a>
                                </div>
                            </div>
                        @endif

                        @if ($umkm->address)
                            <div class="pt-4 border-t border-gray-200">
                                <div class="flex items-start space-x-3">
                                    <div
                                        class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-1">
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500 mb-1">Alamat</p>
                                        <div class="text-gray-700 text-sm leading-relaxed">
                                            {!! $umkm->formatted_address !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Statistics -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Statistik</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Total Produk</span>
                            <span class="font-semibold text-gray-900">{{ $umkm->products->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Produk Aktif</span>
                            <span class="font-semibold text-green-600">
                                {{ $umkm->products->where('is_active', true)->count() }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Bergabung</span>
                            <span class="font-semibold text-gray-900">{{ $umkm->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">User ID</span>
                            <span class="font-semibold text-gray-900">#{{ $umkm->user_id }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    @if ($showEditModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
            wire:click="toggleEditModal">
            <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden" wire:click.stop>
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-900">Edit UMKM</h3>
                    <button wire:click="toggleEditModal" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="updateUmkm" class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Name -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Usaha *</label>
                            <input type="text" wire:model="business_name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('business_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Owner Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Pemilik *</label>
                            <input type="text" wire:model="owner_name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('owner_name')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- WhatsApp -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">WhatsApp</label>
                            <input type="text" wire:model="whatsapp"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('whatsapp')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Instagram -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Instagram</label>
                            <input type="text" wire:model="instagram"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('instagram')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Website -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
                            <input type="url" wire:model="link_website"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('link_website')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Logo Upload -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Logo Baru</label>
                            <input type="file" wire:model="new_logo" accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('new_logo')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            @if ($new_logo)
                                <div class="mt-2">
                                    <img src="{{ $new_logo->temporaryUrl() }}"
                                        class="w-20 h-20 object-cover rounded-lg">
                                </div>
                            @endif
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Alamat</label>
                            <textarea wire:model="address" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            @error('address')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea wire:model="description" rows="4"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                            @error('description')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model="is_active"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-gray-700">UMKM Aktif</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" wire:click="toggleEditModal"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
            wire:click="toggleDeleteModal">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl" wire:click.stop>
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Hapus UMKM</h3>
                    <p class="text-gray-600 mb-6">
                        Apakah Anda yakin ingin menghapus UMKM <strong>{{ $umkm->business_name }}</strong>?
                        Tindakan ini akan menghapus semua data UMKM dan produk-produknya secara permanen.
                    </p>

                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                        <p class="text-red-800 text-sm">
                            <strong>Perhatian:</strong> Data yang akan dihapus:
                        </p>
                        <ul class="text-red-700 text-sm mt-2 space-y-1">
                            <li>• Informasi UMKM</li>
                            <li>• {{ $umkm->products->count() }} produk</li>
                            <li>• Gambar logo dan produk</li>
                        </ul>
                    </div>

                    <div class="flex space-x-3">
                        <button wire:click="toggleDeleteModal"
                            class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 rounded-lg
                                       hover:bg-gray-50 transition-colors font-medium">
                            Batal
                        </button>
                        <button wire:click="deleteUmkm"
                            class="flex-1 px-6 py-3 bg-red-600 text-white rounded-lg
                                       hover:bg-red-700 transition-colors font-medium">
                            Ya, Hapus
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Approval Modal -->
    @if ($showApprovalModal)
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 z-50"
            wire:click="toggleApprovalModal">
            <div class="bg-white rounded-2xl p-8 max-w-md w-full shadow-2xl" wire:click.stop>
                <div class="text-center">
                    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Persetujuan UMKM</h3>
                    <p class="text-gray-600 mb-6">
                        Pilih tindakan untuk UMKM <strong>{{ $umkm->business_name }}</strong>
                    </p>

                    <div class="space-y-3">
                        <button wire:click="approveUmkm"
                            class="w-full px-6 py-3 bg-green-600 text-white rounded-lg
                                       hover:bg-green-700 transition-colors font-medium">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Setujui & Aktifkan
                        </button>

                        <button wire:click="rejectUmkm"
                            class="w-full px-6 py-3 bg-red-600 text-white rounded-lg
                                       hover:bg-red-700 transition-colors font-medium">
                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Tolak & Nonaktifkan
                        </button>

                        <button wire:click="toggleApprovalModal"
                            class="w-full px-6 py-3 border border-gray-300 text-gray-700 rounded-lg
                                       hover:bg-gray-50 transition-colors font-medium">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Loading States -->
    <div wire:loading.flex class="fixed inset-0 bg-black/30 backdrop-blur-sm items-center justify-center z-50">
        <div class="bg-white rounded-xl p-6 shadow-2xl">
            <div class="flex items-center space-x-3">
                <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600"></div>
                <span class="text-gray-700 font-medium">Memproses...</span>
            </div>
        </div>
    </div>
</div>

