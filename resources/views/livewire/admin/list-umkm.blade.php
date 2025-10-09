<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\UmkmProfile;

new class extends Component {
    use WithPagination;

    #[Url(as: 'search')]
    public $search = '';

    #[Url(as: 'category')]
    public $categoryFilter = '';

    #[Url(as: 'status')]
    public $statusFilter = '';

    #[Url(as: 'approval')]
    public $approvalFilter = '';

    public $showDeleteModal = false;
    public $umkmToDelete = null;

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

    public function updatingApprovalFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'categoryFilter', 'statusFilter', 'approvalFilter']);
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $umkm = UmkmProfile::findOrFail($id);
        $umkm->update(['is_active' => !$umkm->is_active]);

        session()->flash('message', 'Status UMKM berhasil diubah!');
    }

    public function toggleApproval($id)
    {
        $umkm = UmkmProfile::findOrFail($id);
        $umkm->update(['is_approved' => !$umkm->is_approved]);

        session()->flash('message', 'Status approval UMKM berhasil diubah!');
    }

    public function confirmDelete($id)
    {
        $this->umkmToDelete = $id;
        $this->showDeleteModal = true;
    }

    public function deleteUmkm()
    {
        if ($this->umkmToDelete) {
            $umkm = UmkmProfile::findOrFail($this->umkmToDelete);

            // Delete logo if exists
            if ($umkm->logo && Storage::exists($umkm->logo)) {
                Storage::delete($umkm->logo);
            }

            $umkm->delete();

            session()->flash('message', 'UMKM berhasil dihapus!');
            $this->showDeleteModal = false;
            $this->umkmToDelete = null;
        }
    }

    public function with(): array
    {
        $query = UmkmProfile::query()->with('user');

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('business_name', 'like', '%' . $this->search . '%')
                  ->orWhere('owner_name', 'like', '%' . $this->search . '%')
                  ->orWhere('kecamatan', 'like', '%' . $this->search . '%');
            });
        }

        // Apply category filter
        if ($this->categoryFilter) {
            $query->where('categories', $this->categoryFilter);
        }

        // Apply status filter
        if ($this->statusFilter !== '') {
            $query->where('is_active', $this->statusFilter === '1');
        }

        // Apply approval filter
        if ($this->approvalFilter !== '') {
            $query->where('is_approved', $this->approvalFilter === '1');
        }

        return [
            'umkmProfiles' => $query->latest()->paginate(10),
            'totalUmkm' => UmkmProfile::count(),
            'activeUmkm' => UmkmProfile::where('is_active', true)->count(),
            'pendingApproval' => UmkmProfile::where('is_approved', false)->count(),
        ];
    }

    public function showUmkm($id)
    {
        return $this->redirect(route('admin.detail-umkm', ['slug' => $id]), true);
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-accent-50 via-white to-primary-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-900 mb-2">
                        Manajemen UMKM
                    </h1>
                    <p class="text-secondary-600">
                        Kelola dan monitor profil UMKM yang terdaftar di platform
                    </p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('admin.umkm.create') }}"
                        class="inline-flex items-center px-5 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-medium rounded-lg shadow-warm hover:from-primary-700 hover:to-primary-600 transition-all duration-200 hover:shadow-warm-lg hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah UMKM
                    </a>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div
                class="bg-white rounded-xl shadow-warm border border-accent-100 p-6 hover:shadow-warm-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary-600 mb-1">Total UMKM</p>
                        <p class="text-3xl font-bold text-secondary-900">{{ $totalUmkm }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-warm border border-accent-100 p-6 hover:shadow-warm-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary-600 mb-1">UMKM Aktif</p>
                        <p class="text-3xl font-bold text-success-600">{{ $activeUmkm }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-success-100 to-success-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-warm border border-accent-100 p-6 hover:shadow-warm-lg transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-secondary-600 mb-1">Pending Approval</p>
                        <p class="text-3xl font-bold text-fix-400">{{ $pendingApproval }}</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-fix-200 to-fix-300 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-fix-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div class="mb-6 bg-success-50 border border-success-200 rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-success-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <p class="text-success-800 font-medium">{{ session('message') }}</p>
                </div>
                <button wire:click="$set('message', null)" class="text-success-600 hover:text-success-800">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        @endif

        {{-- Filters Section --}}
        <div class="bg-white rounded-xl shadow-warm border border-accent-100 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                        Cari UMKM
                    </label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Nama usaha, pemilik, kecamatan..."
                            class="w-full pl-10 pr-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <svg class="absolute left-3 top-3 w-5 h-5 text-secondary-400" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Category Filter --}}
                <div>
                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                        Kategori
                    </label>
                    <select wire:model.live="categoryFilter"
                        class="w-full px-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
                        <option value="">Semua Kategori</option>
                        @foreach (UmkmProfile::CATEGORIES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                        Status & Approval
                    </label>
                    <div class="flex gap-2">
                        <select wire:model.live="statusFilter"
                            class="flex-1 px-3 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors text-sm">
                            <option value="">Status</option>
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                        <select wire:model.live="approvalFilter"
                            class="flex-1 px-3 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors text-sm">
                            <option value="">Approval</option>
                            <option value="1">Approved</option>
                            <option value="0">Pending</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- Clear Filters --}}
            @if ($search || $categoryFilter || $statusFilter !== '' || $approvalFilter !== '')
                <div class="mt-4 pt-4 border-t border-accent-100">
                    <button wire:click="clearFilters"
                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-secondary-700 bg-accent-50 hover:bg-accent-100 rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Hapus Filter
                    </button>
                </div>
            @endif
        </div>

        {{-- UMKM List --}}
        <div class="bg-white rounded-xl shadow-warm border border-accent-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-accent-200">
                    <thead class="bg-gradient-to-r from-accent-50 to-primary-50">
                        <tr>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-secondary-700 uppercase tracking-wider">
                                UMKM
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-secondary-700 uppercase tracking-wider">
                                Kategori
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-secondary-700 uppercase tracking-wider">
                                Lokasi
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-secondary-700 uppercase tracking-wider">
                                Kontak
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-secondary-700 uppercase tracking-wider">
                                Status
                            </th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold text-secondary-700 uppercase tracking-wider">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-accent-100">
                        @forelse($umkmProfiles as $umkm)
                            <tr class="hover:bg-accent-50 transition-colors">
                                {{-- UMKM Info --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-4">
                                        <div class="flex-shrink-0">
                                            @if ($umkm->hasLogo())
                                                <img src="{{ $umkm->logo_url }}" alt="{{ $umkm->business_name }}"
                                                    class="w-12 h-12 rounded-lg object-cover border-2 border-accent-200">
                                            @else
                                                <div
                                                    class="w-12 h-12 rounded-lg bg-gradient-to-br from-primary-500 to-fix-400 flex items-center justify-center border-2 border-accent-200">
                                                    <span
                                                        class="text-white font-bold text-sm">{{ $umkm->initials }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-secondary-900">
                                                {{ $umkm->business_name }}
                                            </p>
                                            <p class="text-xs text-secondary-600">
                                                {{ $umkm->owner_name }}
                                            </p>
                                            <p class="text-xs text-secondary-500 mt-0.5">
                                                {{ $umkm->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Category --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-primary-100 text-primary-800">
                                        {{ UmkmProfile::CATEGORIES[$umkm->categories] ?? $umkm->categories }}
                                    </span>
                                </td>

                                {{-- Location --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center text-sm text-secondary-700">
                                        <svg class="w-4 h-4 mr-1.5 text-secondary-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        {{ $umkm->kecamatan }}
                                    </div>
                                </td>

                                {{-- Contact --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        @if ($umkm->whatsapp)
                                            <a href="{{ $umkm->whatsapp_url }}" target="_blank"
                                                class="p-1.5 bg-success-100 hover:bg-success-200 text-success-600 rounded transition-colors">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654z" />
                                                </svg>
                                            </a>
                                        @endif
                                        @if ($umkm->instagram)
                                            <a href="{{ $umkm->instagram_url }}" target="_blank"
                                                class="p-1.5 bg-pink-100 hover:bg-pink-200 text-pink-600 rounded transition-colors">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    <div class="space-y-2">
                                        <button wire:click="toggleStatus({{ $umkm->id }})"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium transition-all duration-200 hover:scale-105 {{ $umkm->is_active ? 'bg-success-100 text-success-800 hover:bg-success-200' : 'bg-secondary-100 text-secondary-800 hover:bg-secondary-200' }}">
                                            <span
                                                class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $umkm->is_active ? 'bg-success-600' : 'bg-secondary-600' }}"></span>
                                            {{ $umkm->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                        <button wire:click="toggleApproval({{ $umkm->id }})"
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium transition-all duration-200 hover:scale-105 {{ $umkm->is_approved ? 'bg-primary-100 text-primary-800 hover:bg-primary-200' : 'bg-fix-200 text-fix-400 hover:bg-fix-300' }}">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $umkm->is_approved ? 'Approved' : 'Pending' }}
                                        </button>
                                    </div>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button wire:click='showUmkm("{{ $umkm->slug }}")'
                                            class="p-2 text-secondary-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200"
                                            title="Lihat Profil">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>
                                        {{-- <a href="{{ route('admin.umkm.edit', $umkm->id) }}"
                                           class="p-2 text-secondary-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all duration-200"
                                           title="Edit">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a> --}}
                                        {{-- <button wire:click="confirmDelete({{ $umkm->id }})"
                                                class="p-2 text-secondary-600 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200"
                                                title="Hapus">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button> --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-secondary-300 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <p class="text-secondary-600 font-medium mb-2">Tidak ada data UMKM</p>
                                        <p class="text-secondary-500 text-sm">
                                            @if ($search || $categoryFilter || $statusFilter !== '' || $approvalFilter !== '')
                                                Tidak ada hasil yang sesuai dengan filter. Coba ubah kriteria pencarian.
                                            @else
                                                Belum ada UMKM yang terdaftar di sistem.
                                            @endif
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($umkmProfiles->hasPages())
                <div class="bg-accent-50 px-6 py-4 border-t border-accent-200">
                    {{ $umkmProfiles->links() }}
                </div>
            @endif
        </div>
    </div>

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
            aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 bg-secondary-900 bg-opacity-75 transition-opacity"
                    wire:click="$set('showDeleteModal', false)" aria-hidden="true"></div>

                {{-- Modal panel --}}
                <div
                    class="inline-block align-bottom bg-white rounded-xl text-left overflow-hidden shadow-warm-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-6 pt-6 pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-semibold text-secondary-900" id="modal-title">
                                    Hapus UMKM
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-secondary-600">
                                        Apakah Anda yakin ingin menghapus UMKM ini? Semua data termasuk produk yang
                                        terkait akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-accent-50 px-6 py-4 sm:flex sm:flex-row-reverse gap-3">
                        <button type="button" wire:click="deleteUmkm"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2.5 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm transition-colors">
                            Hapus
                        </button>
                        <button type="button" wire:click="$set('showDeleteModal', false)"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-accent-300 shadow-sm px-4 py-2.5 bg-white text-base font-medium text-secondary-700 hover:bg-accent-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
