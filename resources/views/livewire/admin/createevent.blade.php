<?php

// resources/views/livewire/admin/createevent.blade.php
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Event;

new class extends Component {
    use WithFileUploads;

    // Form properties
    public string $title = '';
    public string $categories = '';
    public string $event_date = '';
    public string $description = '';
    public $image;

    // Validation rules
    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'categories' => 'required|in:' . implode(',', array_keys(Event::CATEGORIES)),
            'event_date' => 'required|date|after:today',
            'description' => 'required|string|max:1000',
            'image' => 'nullable|image|max:2048',
        ];
    }

    // Custom validation messages
    protected $messages = [
        'title.required' => 'Judul event wajib diisi.',
        'title.max' => 'Judul event maksimal 255 karakter.',
        'categories.required' => 'Kategori event wajib dipilih.',
        'categories.in' => 'Kategori yang dipilih tidak valid.',
        'event_date.required' => 'Tanggal event wajib diisi.',
        'event_date.after' => 'Tanggal event harus setelah hari ini.',
        'description.required' => 'Deskripsi event wajib diisi.',
        'description.max' => 'Deskripsi event maksimal 1000 karakter.',
        'image.image' => 'File harus berupa gambar.',
        'image.max' => 'Ukuran gambar maksimal 2MB.',
    ];

    // Real-time validation
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // Submit form
    public function submit()
    {
        $this->validate();

        try {
            $data = [
                'title' => $this->title,
                'categories' => $this->categories,
                'event_date' => $this->event_date,
                'description' => $this->description,
            ];

            // Handle image upload
            if ($this->image) {
                $data['image'] = $this->image->store('events', 'public');
            }

            Event::create($data);

            // Reset form
            $this->reset();

            // Show success message
            session()->flash('success', 'Event berhasil dibuat!');

            // Dispatch browser event for additional UI feedback if needed
            $this->dispatch('event-created');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menyimpan event: ' . $e->getMessage());
        }
    }

    // Reset form
    public function resetForm()
    {
        $this->reset();
        $this->resetValidation();
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-primary-50 via-accent-50 to-primary-100 py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header Section -->
        <div class="mb-8 text-center">
            <h1 class="text-4xl font-bold text-fix-100 font-acme mb-2">Tambah Event Baru</h1>
            <p class="text-neutral-600 font-inter">Buat event untuk memberdayakan dan mengembangkan UMKM</p>
        </div>

        <div class="bg-white shadow-warm-xl rounded-2xl overflow-hidden border border-neutral-100">
            <!-- Card Header -->
            <div class="bg-gradient-to-r from-primary-500 to-primary-400 px-8 py-6">
                <h3 class="text-xl font-bold text-white font-inter flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Form Tambah Event
                </h3>
            </div>

            <!-- Success/Error Messages -->
            @if (session()->has('success'))
                <div
                    class="mx-8 mt-6 p-4 bg-success-50 border-l-4 border-success-500 rounded-lg shadow-sm animate-pulse">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-success-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-success-800 font-semibold font-inter">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if (session()->has('error'))
                <div class="mx-8 mt-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-lg shadow-sm">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd" />
                        </svg>
                        <p class="text-red-800 font-semibold font-inter">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <form wire:submit="submit" class="p-8 space-y-6">
                <!-- Title Field -->
                <div>
                    <label for="title" class="block text-sm font-bold text-neutral-700 mb-2 font-inter">
                        Judul Event <span class="text-primary-500">*</span>
                    </label>
                    <input type="text" id="title" wire:model.live="title"
                        placeholder="Contoh: Workshop Digital Marketing untuk UMKM"
                        class="w-full px-4 py-3 border-2 border-neutral-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-all duration-200 font-inter @error('title') border-red-300 focus:ring-red-400 focus:border-red-400 @enderror">
                    @error('title')
                        <p class="mt-2 text-sm text-red-600 flex items-center font-inter">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Categories Field -->
                <div>
                    <label for="categories" class="block text-sm font-bold text-neutral-700 mb-2 font-inter">
                        Kategori Event <span class="text-primary-500">*</span>
                    </label>
                    <select id="categories" wire:model.live="categories"
                        class="w-full px-4 py-3 border-2 border-neutral-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-all duration-200 font-inter @error('categories') border-red-300 focus:ring-red-400 focus:border-red-400 @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach (Event::CATEGORIES as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('categories')
                        <p class="mt-2 text-sm text-red-600 flex items-center font-inter">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                    @if ($categories)
                        <div
                            class="mt-3 inline-flex items-center px-4 py-2 bg-primary-100 text-primary-800 text-sm font-semibold rounded-lg font-inter">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ Event::CATEGORIES[$categories] ?? '' }}
                        </div>
                    @endif
                </div>

                <!-- Event Date Field -->
                <div>
                    <label for="event_date" class="block text-sm font-bold text-neutral-700 mb-2 font-inter">
                        Tanggal & Waktu Event <span class="text-primary-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-neutral-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <input type="datetime-local" id="event_date" wire:model.live="event_date"
                            class="w-full pl-12 pr-4 py-3 border-2 border-neutral-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-all duration-200 font-inter @error('event_date') border-red-300 focus:ring-red-400 focus:border-red-400 @enderror">
                    </div>
                    @error('event_date')
                        <p class="mt-2 text-sm text-red-600 flex items-center font-inter">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Description Field -->
                <div>
                    <label for="description" class="block text-sm font-bold text-neutral-700 mb-2 font-inter">
                        Deskripsi Event <span class="text-primary-500">*</span>
                    </label>
                    <textarea id="description" wire:model.live="description" rows="5"
                        placeholder="Jelaskan detail event, lokasi, manfaat bagi UMKM, dan informasi penting lainnya..."
                        class="w-full px-4 py-3 border-2 border-neutral-200 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-400 focus:border-primary-400 transition-all duration-200 font-inter resize-none @error('description') border-red-300 focus:ring-red-400 focus:border-red-400 @enderror"></textarea>
                    <div class="mt-2 flex justify-between items-center">
                        @error('description')
                            <p class="text-sm text-red-600 flex items-center font-inter">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                {{ $message }}
                            </p>
                        @else
                            <span></span>
                        @enderror
                        <p
                            class="text-xs font-semibold font-inter {{ strlen($description) > 900 ? 'text-primary-600' : 'text-neutral-500' }}">
                            {{ strlen($description) }}/1000 karakter
                        </p>
                    </div>
                </div>

                <!-- Image Upload Field -->
                <div>
                    <label class="block text-sm font-bold text-neutral-700 mb-3 font-inter">
                        Upload Gambar Event
                        <span class="text-neutral-500 font-normal text-xs ml-2">(Opsional)</span>
                    </label>
                    <div
                        class="border-3 border-dashed border-neutral-300 rounded-xl p-8 text-center hover:border-primary-400 hover:bg-primary-50 transition-all duration-300 @error('image') border-red-300 bg-red-50 @enderror">

                        @if ($image)
                            <!-- Preview uploaded image -->
                            <div class="space-y-4">
                                <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                    class="mx-auto h-48 w-auto object-contain rounded-lg shadow-warm-lg">
                                <div class="flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5 text-success-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm text-success-600 font-semibold font-inter">Gambar berhasil
                                        dipilih</p>
                                </div>
                                <button type="button" wire:click="$set('image', null)"
                                    class="inline-flex items-center px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 text-sm font-semibold rounded-lg transition-colors duration-200 font-inter">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Hapus Gambar
                                </button>
                            </div>
                        @else
                            <!-- Upload area -->
                            <div class="space-y-3">
                                <div
                                    class="mx-auto w-16 h-16 bg-gradient-to-br from-primary-100 to-accent-100 rounded-full flex items-center justify-center">
                                    <svg class="w-8 h-8 text-primary-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="text-sm text-neutral-600 font-inter">
                                    <label for="image"
                                        class="cursor-pointer font-bold text-primary-500 hover:text-primary-600 transition-colors">
                                        Klik untuk upload gambar
                                    </label>
                                    <span class="text-neutral-500"> atau drag & drop di sini</span>
                                </div>
                                <p class="text-xs text-neutral-500 font-inter">Format: JPG, PNG • Maksimal 2MB</p>
                            </div>
                        @endif

                        <input id="image" wire:model="image" type="file" class="sr-only" accept="image/*">
                    </div>
                    @error('image')
                        <p class="mt-2 text-sm text-red-600 flex items-center font-inter">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <!-- Loading indicator -->
                <div wire:loading wire:target="image"
                    class="flex items-center justify-center space-x-2 text-primary-600 font-inter">
                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    <span class="text-sm font-semibold">Mengupload gambar...</span>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 pt-6 border-t-2 border-neutral-100">
                    <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                        class="flex-1 bg-gradient-to-r from-primary-500 to-primary-400 hover:from-primary-600 hover:to-primary-500 disabled:from-neutral-300 disabled:to-neutral-300 text-white px-8 py-4 rounded-xl text-base font-bold shadow-warm-lg hover:shadow-warm-xl transform hover:scale-105 transition-all duration-200 font-inter disabled:transform-none disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="submit" class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan Event
                        </span>
                        <span wire:loading wire:target="submit" class="flex items-center justify-center">
                            <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Menyimpan...
                        </span>
                    </button>

                    <button type="button" wire:click="resetForm"
                        class="flex-1 sm:flex-initial bg-neutral-200 hover:bg-neutral-300 text-neutral-700 px-8 py-4 rounded-xl text-base font-bold shadow-sm hover:shadow-md transition-all duration-200 font-inter">
                        <span class="flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Reset Form
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
