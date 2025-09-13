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

<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola UMKM</h1>
                <p class="mt-2 text-sm text-gray-600">Kelola profil UMKM yang terdaftar di platform</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <button type="button" onclick="toggleCreateForm()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors inline-flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah UMKM
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total UMKM</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($totalUmkm) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">UMKM Aktif</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($activeUmkm) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">UMKM Nonaktif</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($inactiveUmkm) }}</p>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters & Search --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0 sm:space-x-4">
            {{-- Search --}}
            <div class="flex-1 max-w-md">
                <div class="relative">
                    <input type="text" wire:model.live.debounce.300ms="search"
                        placeholder="Cari nama bisnis, pemilik, atau email..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            {{-- Status Filter --}}
            <div class="flex items-center space-x-2">
                <label for="statusFilter" class="text-sm font-medium text-gray-700">Status:</label>
                <select wire:model.live="statusFilter" id="statusFilter"
                    class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="all">Semua</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session()->has('message'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md" x-data="{ show: true }"
            x-show="show" x-transition>
            <div class="flex items-center justify-between">
                <p>{{ session('message') }}</p>
                <button @click="show = false" class="text-green-600 hover:text-green-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    {{-- UMKM Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('business_name')"
                                class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Bisnis</span>
                                @if ($sortBy === 'business_name')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('owner_name')"
                                class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Pemilik</span>
                                @if ($sortBy === 'owner_name')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Produk</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('is_active')"
                                class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Status</span>
                                @if ($sortBy === 'is_active')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('created_at')"
                                class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Bergabung</span>
                                @if ($sortBy === 'created_at')
                                    <svg class="w-4 h-4 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($umkmProfiles as $umkm)
                        <tr class="hover:bg-gray-50 transition-colors">
                            {{-- Business Info --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12">
                                        @if ($umkm->hasLogo())
                                            <img class="h-12 w-12 rounded-lg object-cover border"
                                                src="{{ Storage::url($umkm->logo) }}" alt="{{ $umkm->business_name }}">
                                        @else
                                            <div
                                                class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center border">
                                                <span
                                                    class="text-blue-600 font-semibold text-sm">{{ $umkm->initials }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $umkm->business_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $umkm->user->email }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Owner --}}
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $umkm->owner_name }}</div>
                                @if ($umkm->address)
                                    <div class="text-sm text-gray-500 truncate max-w-xs"
                                        title="{{ $umkm->address }}">
                                        {{ Str::limit($umkm->address, 40) }}
                                    </div>
                                @endif
                            </td>

                            {{-- Contact --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-1">
                                    @if ($umkm->whatsapp)
                                        <a href="{{ $umkm->whatsapp_url }}" target="_blank"
                                            class="text-green-600 hover:text-green-800 text-sm inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
                                            </svg>
                                            {{ $umkm->whatsapp }}
                                        </a>
                                    @endif

                                    @if ($umkm->instagram)
                                        <a href="{{ $umkm->instagram_url }}" target="_blank"
                                            class="text-pink-600 hover:text-pink-800 text-sm inline-flex items-center">
                                            {{ '@' . $umkm->instagram }}
                                        </a>
                                    @endif

                                    @if (!$umkm->hasContact())
                                        <span class="text-gray-400 text-sm">Tidak ada kontak</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Products Count --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                           {{ $umkm->products_count > 0 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ $umkm->products_count }} produk
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                <button wire:click="toggleStatus({{ $umkm->id }})"
                                    wire:confirm="Yakin ingin mengubah status UMKM ini?"
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium transition-colors
                                               {{ $umkm->is_active
                                                   ? 'bg-green-100 text-green-800 hover:bg-green-200'
                                                   : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                    {{ $umkm->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>

                            {{-- Created Date --}}
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900">{{ $umkm->created_at->format('d M Y') }}</div>
                                <div class="text-sm text-gray-500">{{ $umkm->created_at->diffForHumans() }}</div>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    {{-- View Details --}}
                                    <button class="text-blue-600 hover:text-blue-800 p-1 rounded"
                                        title="Lihat Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    {{-- Edit --}}
                                    <button class="text-gray-600 hover:text-gray-800 p-1 rounded" title="Edit UMKM">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    {{-- Delete --}}
                                    <button wire:click="deleteUmkm({{ $umkm->id }})"
                                        wire:confirm="Yakin ingin menghapus UMKM ini? Data yang dihapus tidak dapat dikembalikan."
                                        class="text-red-600 hover:text-red-800 p-1 rounded" title="Hapus UMKM">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada UMKM</h3>
                                    <p class="text-gray-500">
                                        @if ($search)
                                            Tidak ada UMKM yang cocok dengan pencarian "{{ $search }}"
                                        @else
                                            Belum ada UMKM yang terdaftar. Mulai dengan menambah UMKM baru.
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
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $umkmProfiles->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
    <script>
        // Auto-hide flash messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const flashMessage = document.querySelector('[x-data="{ show: true }"]');
                if (flashMessage && flashMessage.__x) {
                    flashMessage.__x.$data.show = false;
                }
            }, 5000);
        });

        // Toggle create form visibility
        function toggleCreateForm() {
            const createForm = document.getElementById('create-umkm-form');
            const listContainer = document.getElementById('umkm-list-container');

            if (createForm.classList.contains('hidden')) {
                // Show form, hide list
                createForm.classList.remove('hidden');
                listContainer.classList.add('hidden');
            } else {
                // Hide form, show list
                createForm.classList.add('hidden');
                listContainer.classList.remove('hidden');
            }
        }

        // Listen for successful registration to go back to list
        document.addEventListener('livewire:load', function() {
            Livewire.on('registration-success', () => {
                toggleCreateForm(); // Go back to list view
            });
        });
    </script>
@endpush
