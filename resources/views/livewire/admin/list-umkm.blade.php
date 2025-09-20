<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\UmkmProfile;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    public function mount()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function viewUmkm($umkmId)
    {
        return $this->redirect(route('admin.detail-umkm', ['id' => $umkmId]), navigate: true);
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function toggleStatus($umkmId)
    {
        $umkm = UmkmProfile::findOrFail($umkmId);
        $umkm->update(['is_active' => !$umkm->is_active]);

        session()->flash('message', 'Status UMKM berhasil diubah.');
    }

    public function deleteUmkm($umkmId)
    {
        $umkm = UmkmProfile::findOrFail($umkmId);

        // Delete logo if exists
        if ($umkm->hasLogo()) {
            Storage::delete($umkm->logo);
        }

        $umkm->delete();

        session()->flash('message', 'UMKM berhasil dihapus.');
        $this->resetPage();
    }

    public function with()
    {
        $query = UmkmProfile::query()
            ->with(['user', 'products'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('business_name', 'like', '%' . $this->search . '%')
                        ->orWhere('owner_name', 'like', '%' . $this->search . '%')
                        ->orWhereHas('user', function ($userQuery) {
                            $userQuery->where('email', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                $query->where('is_active', $this->statusFilter === 'active');
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        return [
            'umkmProfiles' => $query->paginate(10),
            'totalUmkm' => UmkmProfile::count(),
            'activeUmkm' => UmkmProfile::where('is_active', true)->count(),
            'inactiveUmkm' => UmkmProfile::where('is_active', false)->count(),
        ];
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        {{-- Modern Header with Gradient --}}
        <div
            class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 shadow-2xl">
            <div class="absolute inset-0 bg-black/20"></div>
            <div class="relative px-8 py-12">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="text-white">
                        <h1 class="text-4xl font-bold tracking-tight mb-3">
                            Kelola UMKM
                            <span class="inline-block w-2 h-8 bg-yellow-400 ml-2 rounded-full animate-pulse"></span>
                        </h1>
                        <p class="text-blue-100 text-lg max-w-2xl leading-relaxed">
                            Platform manajemen UMKM yang membantu mengembangkan bisnis lokal Indonesia
                        </p>
                    </div>
                    <div class="mt-8 lg:mt-0">
                        <button type="button" onclick="toggleCreateForm()"
                            class="group relative inline-flex items-center px-8 py-4 bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl text-white font-semibold hover:bg-white/20 hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-3 group-hover:rotate-90 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah UMKM Baru
                        </button>
                    </div>
                </div>
            </div>
            {{-- Decorative elements --}}
            <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-purple-500/20 rounded-full blur-2xl"></div>
        </div>

        {{-- Modern Stats Cards with Glass Effect --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total UMKM --}}
            <div
                class="group relative overflow-hidden rounded-2xl bg-white/70 backdrop-blur-sm border border-white/50 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-1">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-blue-500/10 to-purple-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                </div>
                <div class="relative p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600 mb-2">Total UMKM</p>
                            <p class="text-4xl font-bold text-slate-900 tracking-tight">{{ number_format($totalUmkm) }}
                            </p>
                            <p class="text-xs text-slate-500 mt-1">Terdaftar di platform</p>
                        </div>
                        <div class="relative">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-blue-500/30 transition-shadow duration-500">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-400 rounded-full animate-bounce">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active UMKM --}}
            <div
                class="group relative overflow-hidden rounded-2xl bg-white/70 backdrop-blur-sm border border-white/50 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-1">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-green-500/10 to-emerald-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                </div>
                <div class="relative p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600 mb-2">UMKM Aktif</p>
                            <p class="text-4xl font-bold text-green-600 tracking-tight">{{ number_format($activeUmkm) }}
                            </p>
                            <p class="text-xs text-slate-500 mt-1">Siap melayani pelanggan</p>
                        </div>
                        <div class="relative">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-green-500/30 transition-shadow duration-500">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full animate-ping"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Inactive UMKM --}}
            <div
                class="group relative overflow-hidden rounded-2xl bg-white/70 backdrop-blur-sm border border-white/50 shadow-lg hover:shadow-2xl transition-all duration-500 hover:-translate-y-1">
                <div
                    class="absolute inset-0 bg-gradient-to-br from-red-500/10 to-pink-500/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                </div>
                <div class="relative p-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-slate-600 mb-2">UMKM Nonaktif</p>
                            <p class="text-4xl font-bold text-red-500 tracking-tight">{{ number_format($inactiveUmkm) }}
                            </p>
                            <p class="text-xs text-slate-500 mt-1">Memerlukan perhatian</p>
                        </div>
                        <div class="relative">
                            <div
                                class="w-16 h-16 bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:shadow-red-500/30 transition-shadow duration-500">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modern Search & Filters --}}
        <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/50 p-8">
            <div
                class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-6 lg:space-y-0 lg:space-x-8">
                {{-- Search Bar --}}
                <div class="flex-1 max-w-2xl">
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400 group-focus-within:text-blue-500 transition-colors duration-200"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari nama bisnis, pemilik, atau email..."
                            class="w-full pl-12 pr-4 py-4 bg-white/50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 text-slate-900 placeholder-slate-500 shadow-sm focus:shadow-lg backdrop-blur-sm">
                        @if ($search)
                            <button wire:click="$set('search', '')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                <svg class="h-5 w-5 text-slate-400 hover:text-slate-600 transition-colors"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Status Filter --}}
                <div class="flex items-center space-x-4">
                    <label for="statusFilter"
                        class="text-sm font-medium text-slate-700 whitespace-nowrap">Status:</label>
                    <select wire:model.live="statusFilter" id="statusFilter"
                        class="bg-white/50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 shadow-sm backdrop-blur-sm min-w-[120px]">
                        <option value="all">Semua</option>
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Flash Message --}}
        @if (session()->has('message'))
            <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-green-500 to-emerald-600 shadow-lg"
                x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0">
                <div class="p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="font-medium">{{ session('message') }}</p>
                        </div>
                        <button @click="show = false"
                            class="text-green-100 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="absolute bottom-0 left-0 h-1 bg-green-400 animate-pulse" style="width: 100%"></div>
            </div>
        @endif

        {{-- Modern UMKM Cards Grid --}}
        <div class="space-y-6">
            @forelse($umkmProfiles as $umkm)
                <div
                    class="group bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/50 hover:shadow-2xl hover:bg-white/80 transition-all duration-500 hover:-translate-y-1 overflow-hidden">
                    <div class="p-8">
                        <div class="flex flex-col lg:flex-row lg:items-center space-y-6 lg:space-y-0 lg:space-x-8">

                            {{-- Business Info --}}
                            <div class="flex-1">
                                <div class="flex items-start space-x-6">
                                    {{-- Logo --}}
                                    <div class="flex-shrink-0 relative">
                                        @if ($umkm->hasLogo())
                                            <div
                                                class="w-20 h-20 rounded-2xl overflow-hidden shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                                                <img class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                                    src="{{ Storage::url($umkm->logo) }}"
                                                    alt="{{ $umkm->business_name }}">
                                            </div>
                                        @else
                                            <div
                                                class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                                                <span class="text-white font-bold text-lg">{{ $umkm->initials }}</span>
                                            </div>
                                        @endif
                                        {{-- Status indicator --}}
                                        <div
                                            class="absolute -top-2 -right-2 w-6 h-6 rounded-full border-2 border-white shadow-lg {{ $umkm->is_active ? 'bg-green-500' : 'bg-red-500' }}">
                                        </div>
                                    </div>

                                    {{-- Business Details --}}
                                    <div class="flex-1 min-w-0">
                                        <h3
                                            class="text-xl font-bold text-slate-900 mb-2 group-hover:text-blue-600 transition-colors duration-300">
                                            {{ $umkm->business_name }}
                                        </h3>
                                        <div class="space-y-2">
                                            <p class="text-slate-700 font-medium flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-slate-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $umkm->owner_name }}
                                            </p>
                                            <p class="text-slate-600 text-sm flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                                </svg>
                                                {{ $umkm->user->email }}
                                            </p>
                                            @if ($umkm->address)
                                                <p class="text-slate-600 text-sm flex items-start">
                                                    <svg class="w-4 h-4 mr-2 mt-0.5 text-slate-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    </svg>
                                                    <span
                                                        class="line-clamp-2">{{ Str::limit($umkm->address, 80) }}</span>
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Stats & Contact --}}
                            <div class="lg:w-80 space-y-6">
                                {{-- Contact Info --}}
                                <div class="bg-slate-50/50 rounded-xl p-4">
                                    <h4 class="text-sm font-semibold text-slate-700 mb-3">Kontak</h4>
                                    <div class="space-y-2">
                                        @if ($umkm->whatsapp)
                                            <a href="{{ $umkm->whatsapp_url }}" target="_blank"
                                                class="inline-flex items-center px-3 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium hover:bg-green-200 transition-colors group">
                                                <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform"
                                                    fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
                                                </svg>
                                                {{ $umkm->whatsapp }}
                                            </a>
                                        @endif

                                        @if ($umkm->instagram)
                                            <a href="{{ $umkm->instagram_url }}" target="_blank"
                                                class="inline-flex items-center px-3 py-2 bg-pink-100 text-pink-700 rounded-lg text-sm font-medium hover:bg-pink-200 transition-colors group">
                                                <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform"
                                                    fill="currentColor" viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                                </svg>
                                                {{ '@' . $umkm->instagram }}
                                            </a>
                                        @endif

                                        @if (!$umkm->hasContact())
                                            <span class="text-slate-500 text-sm italic">Belum ada kontak</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Quick Stats --}}
                                <div class="flex items-center justify-between">
                                    <div class="text-center">
                                        <p class="text-2xl font-bold text-slate-900">{{ $umkm->products_count }}</p>
                                        <p class="text-xs text-slate-500">Produk</p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-sm text-slate-600">{{ $umkm->created_at->format('d M Y') }}</p>
                                        <p class="text-xs text-slate-500">{{ $umkm->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="lg:w-32 flex lg:flex-col space-x-2 lg:space-x-0 lg:space-y-3">
                                {{-- View Details --}}
                                <button wire:click="viewUmkm({{ $umkm->id }})"
                                    class="flex-1 lg:flex-none inline-flex items-center justify-center px-4 py-3 bg-blue-100 text-blue-700 rounded-xl text-sm font-medium hover:bg-blue-200 border border-blue-200 transition-all duration-300 shadow-sm hover:shadow-md transform hover:scale-105"
                                    title="Lihat Detail">
                                    <svg class="w-4 h-4 lg:mr-0 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <span class="lg:hidden">Detail</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                {{-- Empty State --}}
                <div class="text-center py-20">
                    <div class="relative inline-block">
                        <div
                            class="w-32 h-32 bg-gradient-to-br from-slate-200 to-slate-300 rounded-3xl flex items-center justify-center mb-8 shadow-lg">
                            <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div
                            class="absolute -top-4 -right-4 w-8 h-8 bg-yellow-400 rounded-full animate-bounce opacity-50">
                        </div>
                        <div
                            class="absolute -bottom-2 -left-2 w-6 h-6 bg-blue-400 rounded-full animate-pulse opacity-30">
                        </div>
                    </div>

                    <h3 class="text-2xl font-bold text-slate-900 mb-4">
                        @if ($search)
                            Tidak ada hasil untuk "{{ $search }}"
                        @else
                            Belum ada UMKM terdaftar
                        @endif
                    </h3>

                    <p class="text-slate-600 max-w-md mx-auto leading-relaxed mb-8">
                        @if ($search)
                            Coba ubah kata kunci pencarian atau filter untuk menemukan UMKM yang Anda cari.
                        @else
                            Mulai membangun ekosistem UMKM dengan menambahkan bisnis baru ke platform.
                        @endif
                    </p>

                    @if ($search)
                        <button wire:click="$set('search', '')"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-xl font-semibold hover:bg-blue-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Pencarian
                        </button>
                    @else
                        <button onclick="toggleCreateForm()"
                            class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah UMKM Pertama
                        </button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Modern Pagination --}}
        @if ($umkmProfiles->hasPages())
            <div class="flex items-center justify-center">
                <div class="bg-white/70 backdrop-blur-sm rounded-2xl shadow-lg border border-white/50 p-6">
                    {{ $umkmProfiles->links() }}
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            // Enhanced animations and interactions
            document.addEventListener('DOMContentLoaded', function() {
                // Auto-hide flash messages
                setTimeout(function() {
                    const flashMessage = document.querySelector('[x-data="{ show: true }"]');
                    if (flashMessage && flashMessage.__x) {
                        flashMessage.__x.$data.show = false;
                    }
                }, 8000);

                // Add smooth scrolling for better UX
                document.documentElement.style.scrollBehavior = 'smooth';

                // Enhanced card hover effects
                const cards = document.querySelectorAll('.group');
                cards.forEach(card => {
                    card.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateY(-8px) scale(1.02)';
                    });

                    card.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateY(0) scale(1)';
                    });
                });
            });

            // Toggle create form with smooth transitions
            function toggleCreateForm() {
                const createForm = document.getElementById('create-umkm-form');
                const listContainer = document.getElementById('umkm-list-container');

                if (createForm && listContainer) {
                    if (createForm.classList.contains('hidden')) {
                        // Show form with animation
                        createForm.classList.remove('hidden');
                        createForm.style.opacity = '0';
                        createForm.style.transform = 'translateY(20px)';

                        setTimeout(() => {
                            createForm.style.transition = 'all 0.3s ease-out';
                            createForm.style.opacity = '1';
                            createForm.style.transform = 'translateY(0)';
                        }, 10);

                        listContainer.classList.add('hidden');
                    } else {
                        // Hide form with animation
                        createForm.style.transition = 'all 0.3s ease-in';
                        createForm.style.opacity = '0';
                        createForm.style.transform = 'translateY(-20px)';

                        setTimeout(() => {
                            createForm.classList.add('hidden');
                            listContainer.classList.remove('hidden');
                        }, 300);
                    }
                }
            }

            // Listen for Livewire events
            document.addEventListener('livewire:load', function() {
                Livewire.on('registration-success', () => {
                    toggleCreateForm();

                    // Show success animation
                    const successMessage = document.createElement('div');
                    successMessage.className =
                        'fixed top-4 right-4 bg-green-500 text-white px-6 py-4 rounded-xl shadow-xl z-50 transform translate-x-full transition-transform duration-300';
                    successMessage.innerHTML = `
                        <div class="flex items-center">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            UMKM berhasil ditambahkan!
                        </div>
                    `;

                    document.body.appendChild(successMessage);

                    setTimeout(() => {
                        successMessage.style.transform = 'translateX(0)';
                    }, 100);

                    setTimeout(() => {
                        successMessage.style.transform = 'translateX(full)';
                        setTimeout(() => successMessage.remove(), 300);
                    }, 4000);
                });
            });

            // Add loading states for better UX
            document.addEventListener('livewire:load', function() {
                Livewire.hook('message.sent', () => {
                    document.body.style.cursor = 'wait';
                });

                Livewire.hook('message.processed', () => {
                    document.body.style.cursor = 'default';
                });
            });

            // Enhanced search with debounce indicator
            let searchTimeout;
            const searchInput = document.querySelector('input[wire\\:model\\.live\\.debounce\\.300ms="search"]');
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const searchContainer = this.parentElement;
                    searchContainer.classList.add('animate-pulse');

                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => {
                        searchContainer.classList.remove('animate-pulse');
                    }, 500);
                });
            }

            // Add intersection observer for scroll animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe all cards for scroll animations
            setTimeout(() => {
                const cards = document.querySelectorAll('.group');
                cards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(30px)';
                    card.style.transition = `all 0.6s ease-out ${index * 0.1}s`;
                    observer.observe(card);
                });
            }, 100);
        </script>
    @endpush
</div>
