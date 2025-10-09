<?php

use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

new #[Layout('layouts.main')] class extends Component {
    use WithPagination;

    #[Url(as: 'search')]
    public $search = '';

    #[Url(as: 'category')]
    public $selectedCategory = '';

    #[Url(as: 'sort')]
    public $sortBy = 'latest';

    public $showDeleteModal = false;
    public $eventToDelete = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'selectedCategory', 'sortBy']);
        $this->resetPage();
    }

    public function confirmDelete($eventId)
    {
        $this->eventToDelete = $eventId;
        $this->showDeleteModal = true;
    }

    public function deleteEvent()
    {
        if ($this->eventToDelete) {
            $event = Event::find($this->eventToDelete);

            if ($event) {
                // Delete image if exists
                if ($event->image && \Storage::disk('public')->exists($event->image)) {
                    \Storage::disk('public')->delete($event->image);
                }

                $event->delete();

                session()->flash('success', 'Event berhasil dihapus!');
            }
        }

        $this->showDeleteModal = false;
        $this->eventToDelete = null;
    }

    public function with(): array
    {
        $query = Event::query();

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply category filter
        if ($this->selectedCategory) {
            $query->where('categories', $this->selectedCategory);
        }

        // Apply sorting
        switch ($this->sortBy) {
            case 'latest':
                $query->latest('created_at');
                break;
            case 'oldest':
                $query->oldest('created_at');
                break;
            case 'event_date_asc':
                $query->orderBy('event_date', 'asc');
                break;
            case 'event_date_desc':
                $query->orderBy('event_date', 'desc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
        }

        return [
            'events' => $query->paginate(12),
            'totalEvents' => Event::count(),
            'categories' => Event::CATEGORIES,
        ];
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-accent-50 via-white to-primary-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-900 mb-2">
                        Kelola Event & Kegiatan
                    </h1>
                    <p class="text-secondary-600">
                        Manage semua event dan kegiatan platform UMKM
                    </p>
                </div>

                <a href="{{ route('admin.event.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-medium rounded-lg shadow-warm hover:from-primary-700 hover:to-primary-600 transition-all duration-200 hover:shadow-warm-lg hover:scale-105">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Event Baru
                </a>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-xl shadow-warm p-6 border border-accent-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-secondary-600 text-sm font-medium">Total Events</p>
                        <p class="text-3xl font-bold text-secondary-900 mt-1">{{ $totalEvents }}</p>
                    </div>
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-warm p-6 border border-accent-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-secondary-600 text-sm font-medium">Hasil Pencarian</p>
                        <p class="text-3xl font-bold text-secondary-900 mt-1">{{ $events->total() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-accent-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-warm p-6 border border-accent-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-secondary-600 text-sm font-medium">Kategori Aktif</p>
                        <p class="text-3xl font-bold text-secondary-900 mt-1">{{ count($categories) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-fix-200/30 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-fix-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-warm p-6 border border-accent-100">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-secondary-600 text-sm font-medium">Halaman</p>
                        <p class="text-3xl font-bold text-secondary-900 mt-1">
                            {{ $events->currentPage() }}/{{ $events->lastPage() }}</p>
                    </div>
                    <div class="w-12 h-12 bg-secondary-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-secondary-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters Section --}}
        <div class="bg-white rounded-xl shadow-warm p-6 mb-6 border border-accent-100">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Search --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                        Cari Event
                    </label>
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari berdasarkan judul atau deskripsi..."
                            class="w-full pl-10 pr-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
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
                    <select wire:model.live="selectedCategory"
                        class="w-full px-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Sort --}}
                <div>
                    <label class="block text-sm font-medium text-secondary-700 mb-2">
                        Urutkan
                    </label>
                    <select wire:model.live="sortBy"
                        class="w-full px-4 py-2.5 border border-accent-200 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                        <option value="latest">Terbaru</option>
                        <option value="oldest">Terlama</option>
                        <option value="event_date_asc">Tanggal Event (Awal)</option>
                        <option value="event_date_desc">Tanggal Event (Akhir)</option>
                        <option value="title_asc">Judul (A-Z)</option>
                        <option value="title_desc">Judul (Z-A)</option>
                    </select>
                </div>
            </div>

            {{-- Active Filters --}}
            @if ($search || $selectedCategory)
                <div class="mt-4 pt-4 border-t border-accent-100 flex items-center justify-between">
                    <div class="flex flex-wrap gap-2">
                        @if ($search)
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-primary-100 text-primary-700">
                                Pencarian: "{{ $search }}"
                            </span>
                        @endif
                        @if ($selectedCategory)
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-accent-100 text-accent-700">
                                {{ $categories[$selectedCategory] }}
                            </span>
                        @endif
                    </div>
                    <button wire:click="clearFilters"
                        class="text-sm text-secondary-600 hover:text-primary-600 font-medium transition-colors">
                        Reset Filter
                    </button>
                </div>
            @endif
        </div>

        {{-- Success Message --}}
        @if (session('success'))
            <div
                class="bg-success-50 border border-success-200 text-success-800 px-4 py-3 rounded-lg mb-6 flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        {{-- Events Grid --}}
        @if ($events->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach ($events as $event)
                    <div
                        class="bg-white rounded-xl shadow-warm hover:shadow-warm-lg transition-all duration-300 overflow-hidden border border-accent-100 group">
                        {{-- Image --}}
                        <div class="relative h-48 bg-gradient-to-br from-accent-100 to-primary-100 overflow-hidden">
                            @if ($event->image)
                                <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-accent-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            {{-- Category Badge --}}
                            <div class="absolute top-3 left-3">
                                <span
                                    class="px-3 py-1 bg-white/90 backdrop-blur-sm text-primary-700 text-xs font-medium rounded-full shadow-sm">
                                    {{ $categories[$event->categories] ?? 'Umum' }}
                                </span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-5">
                            <h3
                                class="text-lg font-bold text-secondary-900 mb-2 line-clamp-2 group-hover:text-primary-600 transition-colors">
                                {{ $event->title }}
                            </h3>

                            <p class="text-secondary-600 text-sm mb-4 line-clamp-2">
                                {{ Str::limit($event->description, 100) }}
                            </p>

                            {{-- Meta Info --}}
                            <div class="space-y-2 mb-4 text-xs text-secondary-500">
                                @if ($event->event_date)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-2 text-primary-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        {{ $event->event_date->format('d M Y, H:i') }}
                                    </div>
                                @endif
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-accent-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Dibuat {{ $event->created_at->diffForHumans() }}
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-2 pt-4 border-t border-accent-100">
                                <a href="{{ route('admin.detail-event', $event->slug) }}"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-primary-50 text-primary-700 text-sm font-medium rounded-lg hover:bg-primary-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                                {{-- <a href="{{ route('admin.events.edit', $event->slug) }}"
                                    class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-accent-50 text-accent-700 text-sm font-medium rounded-lg hover:bg-accent-100 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Edit
                                </a> --}}
                                <button wire:click="confirmDelete({{ $event->id }})"
                                    class="px-3 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="bg-white rounded-xl shadow-warm p-4 border border-accent-100">
                {{ $events->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-xl shadow-warm p-12 text-center border border-accent-100">
                <svg class="w-16 h-16 mx-auto text-secondary-300 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <h3 class="text-lg font-semibold text-secondary-900 mb-2">Tidak ada event ditemukan</h3>
                <p class="text-secondary-600 mb-6">
                    @if ($search || $selectedCategory)
                        Coba ubah filter pencarian Anda atau reset filter untuk melihat semua event.
                    @else
                        Belum ada event yang ditambahkan. Mulai dengan menambahkan event pertama Anda!
                    @endif
                </p>
                @if ($search || $selectedCategory)
                    <button wire:click="clearFilters"
                        class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        Reset Filter
                    </button>
                @else
                    <a href="{{ route('admin.events.create') }}"
                        class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Event Pertama
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    @if ($showDeleteModal)
        <div class="fixed inset-0 bg-secondary-900/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl shadow-warm-xl max-w-md w-full p-6">
                <div class="flex items-center justify-center w-12 h-12 bg-red-100 rounded-full mx-auto mb-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-secondary-900 text-center mb-2">Hapus Event?</h3>
                <p class="text-secondary-600 text-center mb-6">
                    Apakah Anda yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="flex gap-3">
                    <button wire:click="$set('showDeleteModal', false)"
                        class="flex-1 px-4 py-2.5 bg-secondary-100 text-secondary-700 font-medium rounded-lg hover:bg-secondary-200 transition-colors">
                        Batal
                    </button>
                    <button wire:click="deleteEvent"
                        class="flex-1 px-4 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
