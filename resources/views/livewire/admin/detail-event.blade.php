<?php

use App\Models\Event;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $slug;
    public $event;

    // Form properties
    public $showEditModal = false;
    public $title;
    public $description;
    public $categories;
    public $event_date;
    public $link_url;
    public $image;
    public $newImage;

    public function mount($slug)
    {
        $this->slug = $slug;
        $this->event = Event::where('slug', $slug)->firstOrFail();
    }

    public function getCategoryName($categoryKey)
    {
        return Event::CATEGORIES[$categoryKey] ?? $categoryKey;
    }

    public function getCategoryColor($categoryKey)
    {
        $colors = [
            'kolaborasi-sosial' => 'bg-primary-100 text-primary-700',
            'riset-inovasi' => 'bg-accent-100 text-accent-700',
            'pengembangan-kapasitas' => 'bg-secondary-100 text-secondary-700',
            'kemitraan-strategis' => 'bg-success-100 text-success-700',
            'info-kampus' => 'bg-neutral-100 text-neutral-700',
        ];

        return $colors[$categoryKey] ?? 'bg-neutral-100 text-neutral-700';
    }

    public function back()
    {
        return redirect()->route('admin.event');
    }

    // TAMBAHKAN METHOD INI
    public function openEditModal()
    {
        $this->title = $this->event->title;
        $this->description = $this->event->description;
        $this->categories = $this->event->categories;
        $this->event_date = $this->event->event_date ? $this->event->event_date->format('Y-m-d\TH:i') : null;
        $this->link_url = $this->event->link_url;
        $this->image = $this->event->image;
        $this->newImage = null;

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetValidation();
    }

    public function updateEvent()
    {
        $validated = $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'categories' => 'required|string',
            'event_date' => 'nullable|date',
            'link_url' => 'nullable|url',
            'newImage' => 'nullable|image|max:2048',
        ]);

        try {
            $data = [
                'title' => $this->title,
                'slug' => Str::slug($this->title),
                'description' => $this->description,
                'categories' => $this->categories,
                'event_date' => $this->event_date,
                'link_url' => $this->link_url,
            ];

            // Handle image upload
            if ($this->newImage) {
                // Delete old image
                if ($this->event->image && Storage::exists($this->event->image)) {
                    Storage::delete($this->event->image);
                }

                // Store new image
                $data['image'] = $this->newImage->store('events', 'public');
            }

            $this->event->update($data);

            // Refresh event data
            $this->event->refresh();
            $this->slug = $this->event->slug;

            session()->flash('success', 'Event berhasil diperbarui!');
            $this->closeEditModal();
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal memperbarui event: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            // Hapus gambar jika ada
            if ($this->event->image && Storage::exists($this->event->image)) {
                Storage::delete($this->event->image);
            }

            $this->event->delete();

            session()->flash('success', 'Event berhasil dihapus!');
            return redirect()->route('admin.events.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Gagal menghapus event: ' . $e->getMessage());
        }
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-neutral-50 via-white to-accent-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header Section --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <button wire:click="back"
                    class="inline-flex items-center px-4 py-2 bg-white border border-neutral-300 rounded-lg text-neutral-700 hover:bg-neutral-50 hover:border-primary-400 transition-all duration-300 shadow-sm hover:shadow-warm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </button>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-3">
                        <button wire:click="openEditModal"
                            class="inline-flex items-center px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition-all duration-300 shadow-warm hover:shadow-warm-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Event
                        </button>
                    </div>
                </div>
            </div>

            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-neutral-800 mb-2 font-aleo">
                    Detail Event
                </h1>
                <div class="w-20 h-1 bg-gradient-to-r from-primary-400 to-primary-600 rounded-full"></div>
            </div>
        </div>

        {{-- Flash Messages --}}
        @if (session()->has('success'))
            <div class="mb-6 bg-success-50 border border-success-200 text-success-700 px-6 py-4 rounded-lg shadow-warm"
                role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-lg shadow-warm"
                role="alert">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column - Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Featured Image Card --}}
                @if ($event->image)
                    <div class="bg-white rounded-2xl shadow-warm-lg overflow-hidden">
                        <div class="aspect-video w-full overflow-hidden bg-neutral-100">
                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}"
                                class="w-full h-full object-cover hover:scale-105 transition-transform duration-500">
                        </div>
                    </div>
                @endif

                {{-- Title & Description Card --}}
                <div class="bg-white rounded-2xl shadow-warm-lg p-6 md:p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl md:text-3xl font-bold text-neutral-800 mb-4 font-aleo leading-tight">
                            {{ $event->title }}
                        </h2>

                        <div class="flex flex-wrap items-center gap-4 text-sm text-neutral-600">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $event->formatted_event_date ?? 'Tanggal belum ditentukan' }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ $event->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-neutral-200 pt-6">
                        <h3 class="text-lg font-semibold text-neutral-800 mb-3 font-aleo">
                            Deskripsi Event
                        </h3>
                        <div class="prose prose-neutral max-w-none text-neutral-700 leading-relaxed">
                            {!! nl2br(e($event->description)) !!}
                        </div>
                    </div>

                    @if ($event->link_url)
                        <div class="border-t border-neutral-200 mt-6 pt-6">
                            <a href="{{ $event->link_url }}" target="_blank" rel="noopener noreferrer"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-300 shadow-warm hover:shadow-warm-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                                Kunjungi Link Event
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column - Info Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Category Card --}}
                <div class="bg-white rounded-2xl shadow-warm-lg p-6">
                    <h3 class="text-lg font-semibold text-neutral-800 mb-4 font-aleo flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        Kategori
                    </h3>
                    <div>
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-medium {{ $this->getCategoryColor($event->categories) }}">
                            {{ $this->getCategoryName($event->categories) }}
                        </span>
                    </div>
                </div>

                {{-- Event Details Card --}}
                <div class="bg-white rounded-2xl shadow-warm-lg p-6">
                    <h3 class="text-lg font-semibold text-neutral-800 mb-4 font-aleo flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Informasi Event
                    </h3>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-primary-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-neutral-500 mb-1">Tanggal Event</p>
                                <p class="text-sm font-medium text-neutral-800">
                                    {{ $event->event_date ? $event->event_date->format('d F Y, H:i') : 'Belum ditentukan' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-lg bg-accent-100 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-accent-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-neutral-500 mb-1">Dibuat</p>
                                <p class="text-sm font-medium text-neutral-800">
                                    {{ $event->created_at->format('d F Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-lg bg-secondary-100 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-secondary-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-neutral-500 mb-1">Terakhir Diupdate</p>
                                <p class="text-sm font-medium text-neutral-800">
                                    {{ $event->updated_at->format('d F Y, H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-lg bg-success-100 flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-success-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-neutral-500 mb-1">Slug</p>
                                <p class="text-sm font-medium text-neutral-800 break-all">
                                    {{ $event->slug }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quick Actions Card --}}
                {{-- Quick Actions Card --}}
                <div class="bg-gradient-to-br from-primary-50 to-accent-50 rounded-2xl shadow-warm-lg p-6">
                    <h3 class="text-lg font-semibold text-neutral-800 mb-4 font-aleo">
                        Aksi Cepat
                    </h3>
                    <div class="space-y-3">
                        <button wire:click="openEditModal"
                            class="block w-full px-4 py-3 bg-white text-neutral-700 rounded-lg hover:bg-primary-50 hover:text-primary-700 transition-all duration-300 shadow-sm hover:shadow-warm text-center font-medium inline-flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Event
                        </button>

                        <button wire:click="delete"
                            wire:confirm="Apakah Anda yakin ingin menghapus event ini? Tindakan ini tidak dapat dibatalkan."
                            class="block w-full px-4 py-3 bg-white text-red-600 rounded-lg hover:bg-red-50 hover:text-red-700 transition-all duration-300 shadow-sm hover:shadow-warm text-center font-medium inline-flex items-center justify-center border border-red-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Event
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Edit Modal --}}
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showEditModal') }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div class="fixed inset-0 transition-opacity bg-neutral-500 bg-opacity-75"
                    wire:click="closeEditModal"></div>

                {{-- Modal panel --}}
                <div
                    class="inline-block w-full max-w-3xl px-4 pt-5 pb-4 overflow-hidden text-left align-bottom transition-all transform bg-white rounded-2xl shadow-warm-xl sm:my-8 sm:align-middle sm:p-6">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold text-neutral-800 font-aleo">
                            Edit Event
                        </h3>
                        <button wire:click="closeEditModal"
                            class="text-neutral-400 hover:text-neutral-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form wire:submit.prevent="updateEvent" class="space-y-6">
                        {{-- Title --}}
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                Judul Event <span class="text-red-500">*</span>
                            </label>
                            <input type="text" wire:model="title"
                                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                placeholder="Masukkan judul event">
                            @error('title')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Category --}}
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="categories"
                                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                                <option value="">Pilih Kategori</option>
                                <option value="kolaborasi-sosial">Kolaborasi Sosial</option>
                                <option value="riset-inovasi">Riset & Inovasi</option>
                                <option value="pengembangan-kapasitas">Pengembangan Kapasitas</option>
                                <option value="kemitraan-strategis">Kemitraan Strategis</option>
                                <option value="info-kampus">Info Kampus</option>
                            </select>
                            @error('categories')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                Deskripsi <span class="text-red-500">*</span>
                            </label>
                            <textarea wire:model="description" rows="5"
                                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                placeholder="Masukkan deskripsi event"></textarea>
                            @error('description')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Event Date --}}
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                Tanggal Event
                            </label>
                            <input type="datetime-local" wire:model="event_date"
                                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                            @error('event_date')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Link URL --}}
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                Link Event
                            </label>
                            <input type="url" wire:model="link_url"
                                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all"
                                placeholder="https://example.com">
                            @error('link_url')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Image Upload --}}
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">
                                Gambar Event
                            </label>

                            {{-- Current Image --}}
                            @if ($image && !$newImage)
                                <div class="mb-3">
                                    <p class="text-sm text-neutral-600 mb-2">Gambar saat ini:</p>
                                    <img src="{{ Storage::url($image) }}" alt="Current image"
                                        class="w-full h-48 object-cover rounded-lg">
                                </div>
                            @endif

                            {{-- New Image Preview --}}
                            @if ($newImage)
                                <div class="mb-3">
                                    <p class="text-sm text-neutral-600 mb-2">Preview gambar baru:</p>
                                    <img src="{{ $newImage->temporaryUrl() }}" alt="Preview"
                                        class="w-full h-48 object-cover rounded-lg">
                                </div>
                            @endif

                            <input type="file" wire:model="newImage" accept="image/*"
                                class="w-full px-4 py-3 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-all">
                            <p class="text-xs text-neutral-500 mt-1">Format: JPG, PNG, GIF. Max: 2MB</p>
                            @error('newImage')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror

                            <div wire:loading wire:target="newImage" class="text-sm text-primary-600 mt-2">
                                Uploading...
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-neutral-200">
                            <button type="button" wire:click="closeEditModal"
                                class="px-6 py-3 bg-white border border-neutral-300 text-neutral-700 rounded-lg hover:bg-neutral-50 transition-all duration-300">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-lg hover:from-primary-600 hover:to-primary-700 transition-all duration-300 shadow-warm hover:shadow-warm-lg">
                                <span wire:loading.remove wire:target="updateEvent">Simpan Perubahan</span>
                                <span wire:loading wire:target="updateEvent">Menyimpan...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
</div>
