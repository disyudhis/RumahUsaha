<?php

// resources/views/livewire/admin/createevent.blade.php
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use App\Models\Event;

new class extends Component {
    use WithFileUploads;

    // Form properties
    public string $title = '';
    public string $event_date = '';
    public string $description = '';
    public $image;

    // Validation rules
    protected $rules = [
        'title' => 'required|string|max:255',
        'event_date' => 'required|date|after:today',
        'description' => 'required|string|max:1000',
        'image' => 'nullable|image|max:2048', // max 2MB
    ];

    // Custom validation messages
    protected $messages = [
        'title.required' => 'Judul event wajib diisi.',
        'title.max' => 'Judul event maksimal 255 karakter.',
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
            session()->flash('error', 'Terjadi kesalahan saat menyimpan event.');
        }
    }

    // Reset form
    public function resetForm()
    {
        $this->reset();
        $this->resetValidation();
    }
}; ?>

<div class="p-4 max-w-5xl mx-auto">
    <div class="bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Form Tambah Event</h3>
        </div>

        <!-- Success/Error Messages -->
        @if (session()->has('success'))
            <div class="mx-6 mt-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-md">
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mx-6 mt-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-md">
                {{ session('error') }}
            </div>
        @endif

        <form wire:submit="submit" class="p-6 space-y-4">
            <!-- Title Field -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                    Judul Event <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" wire:model.live="title" placeholder="Masukkan judul event"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('title') border-red-300 @enderror">
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Event Date Field -->
            <div>
                <label for="event_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Tanggal Event <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" id="event_date" wire:model.live="event_date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('event_date') border-red-300 @enderror">
                @error('event_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description Field -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                    Deskripsi Event <span class="text-red-500">*</span>
                </label>
                <textarea id="description" wire:model.live="description" rows="3"
                    placeholder="Jelaskan detail event, lokasi, dan manfaat bagi UMKM"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200 @error('description') border-red-300 @enderror"></textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">{{ strlen($description) }}/1000 karakter</p>
            </div>

            <!-- Image Upload Field -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Gambar Event</label>
                <div
                    class="border-2 border-gray-300 border-dashed rounded-lg p-6 text-center hover:border-gray-400 transition-colors duration-200 @error('image') border-red-300 @enderror">

                    @if ($image)
                        <!-- Preview uploaded image -->
                        <div class="mb-4">
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview"
                                class="mx-auto h-32 w-32 object-cover rounded-lg">
                            <p class="mt-2 text-sm text-green-600">Gambar berhasil dipilih</p>
                            <button type="button" wire:click="$set('image', null)"
                                class="mt-1 text-xs text-red-600 hover:underline">
                                Hapus gambar
                            </button>
                        </div>
                    @else
                        <!-- Upload area -->
                        <div class="space-y-2">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                viewBox="0 0 48 48">
                                <path
                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="text-sm text-gray-600">
                                <label for="image"
                                    class="cursor-pointer font-medium text-blue-600 hover:text-blue-500">
                                    Klik untuk upload gambar
                                </label>
                                <span class="text-gray-500"> atau drag & drop di sini</span>
                            </div>
                            <p class="text-xs text-gray-500">Format: JPG, PNG, maksimal 2MB</p>
                        </div>
                    @endif

                    <input id="image" wire:model="image" type="file" class="sr-only" accept="image/*">
                </div>
                @error('image')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Loading indicator -->
            <div wire:loading wire:target="image" class="text-sm text-blue-600">
                Mengupload gambar...
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-3 pt-4">
                <button type="submit" wire:loading.attr="disabled" wire:target="submit"
                    class="bg-blue-500 hover:bg-blue-600 disabled:bg-blue-300 text-white px-6 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    <span wire:loading.remove wire:target="submit">Simpan Event</span>
                    <span wire:loading wire:target="submit">Menyimpan...</span>
                </button>

                <button type="button" wire:click="resetForm"
                    class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md text-sm font-medium transition-colors duration-200">
                    Reset
                </button>
            </div>
        </form>
    </div>
</div>
