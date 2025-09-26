<?php
// resources/views/livewire/main/list-umkm.blade.php
use Livewire\Volt\Component;
use Livewire\WithPagination;
use App\Models\UmkmProfile;

new class extends Component {
    use WithPagination;

    /**
     * Component properties
     */
    public $showAll = false;
    public $limit = 4;
    public $showPagination = false;
    public $showHeader = true;
    public $headerTitle = 'PROFIL UMKM ANGGOTA TERBARU';
    public $showViewAllButton = true;
    public $searchTerm = '';
    public $selectedCategory = '';

    /**
     * Available business categories
     */
    public $categories = [
        '' => 'Semua Kategori',
        'kuliner' => 'Kuliner',
        'fashion' => 'Fashion',
        'kerajinan' => 'Kerajinan',
        'jasa' => 'Jasa',
        'teknologi' => 'Teknologi',
        'kesehatan' => 'Kesehatan',
        'pendidikan' => 'Pendidikan',
        'otomotif' => 'Otomotif',
        'pertanian' => 'Pertanian',
    ];

    /**
     * Initialize component
     */
    public function mount($showAll = false, $limit = 4, $showPagination = false, $showHeader = true, $headerTitle = null, $showViewAllButton = true)
    {
        $this->showAll = $showAll;
        $this->limit = $limit;
        $this->showPagination = $showPagination;
        $this->showHeader = $showHeader;
        $this->headerTitle = $headerTitle ?? 'PROFIL UMKM ANGGOTA TERBARU';
        $this->showViewAllButton = $showViewAllButton;
    }

    /**
     * Get UMKM profiles with filtering and pagination
     */
    public function with()
    {
        $query = UmkmProfile::with('user')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if (!empty($this->searchTerm)) {
            $query->where(function ($q) {
                $q->where('business_name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('owner_name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('business_description', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('address', 'like', '%' . $this->searchTerm . '%');
            });
        }

        // Apply category filter
        if (!empty($this->selectedCategory)) {
            $query->where('business_category', $this->selectedCategory);
        }

        if ($this->showAll) {
            if ($this->showPagination) {
                $umkmProfiles = $query->paginate(12);
            } else {
                $umkmProfiles = $query->get();
            }
        } else {
            $umkmProfiles = $query->limit($this->limit)->get();
        }

        return [
            'umkmProfiles' => $umkmProfiles,
            'totalCount' => UmkmProfile::where('is_approved', true)->count(),
        ];
    }

    /**
     * View UMKM profile
     */
    public function viewProfile($profileId)
    {
        return $this->redirect(route('main.umkm.show', ['slug' => $profileId]), navigate: true);
    }

    /**
     * Get business category icon
     */
    private function getCategoryIcon($category = null)
    {
        $icons = [
            'kuliner' => '☕',
            'fashion' => '👔',
            'kerajinan' => '🧶',
            'jasa' => '🔧',
            'teknologi' => '💻',
            'kesehatan' => '🏥',
            'pendidikan' => '📚',
            'otomotif' => '🚗',
            'pertanian' => '🌾',
            'default' => '🏪',
        ];

        return $icons[strtolower($category ?? 'default')] ?? $icons['default'];
    }

    /**
     * Navigate to all UMKM page
     */
    public function viewAllUmkm()
    {
        return $this->redirect(route('main.umkm.index'), navigate: true);
    }

    /**
     * Clear search and filters
     */
    public function clearFilters()
    {
        $this->searchTerm = '';
        $this->selectedCategory = '';
        $this->resetPage();
    }

    /**
     * Updated search term
     */
    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    /**
     * Updated category filter
     */
    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }
}; ?>

<div class="px-4 sm:px-6 mb-8">
    {{-- Header Section --}}
    @if ($showHeader)
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
            <h2 class="text-2xl font-bold text-gray-900">{{ $headerTitle }}</h2>
            @if ($showViewAllButton && !$showAll)
                <button wire:click='viewAllUmkm' class="text-orange-600 hover:text-orange-700 text-sm font-medium">
                    Lihat Semua →
                </button>
            @endif
        </div>
    @endif

    {{-- Search and Filter Section (only show when showAll is true) --}}
    @if ($showAll)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                {{-- Search Input --}}
                <div class="flex-1">
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="searchTerm"
                            placeholder="Cari nama bisnis, pemilik, atau deskripsi..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200"
                        >
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400">🔍</span>
                        </div>
                    </div>
                </div>

                {{-- Category Filter --}}
                <div class="md:w-64">
                    <select
                        wire:model.live="selectedCategory"
                        class="w-full py-3 px-4 border border-gray-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors duration-200 bg-white"
                    >
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Clear Filters Button --}}
                @if ($searchTerm || $selectedCategory)
                    <button
                        wire:click="clearFilters"
                        class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl transition-colors duration-200 flex items-center space-x-2 whitespace-nowrap"
                    >
                        <span>✕</span>
                        <span>Reset</span>
                    </button>
                @endif
            </div>

            {{-- Results Count --}}
            @if ($showAll)
                <div class="mt-4 text-sm text-gray-600">
                    @if (is_countable($umkmProfiles))
                        Menampilkan {{ $umkmProfiles->count() }} dari {{ $totalCount }} UMKM
                    @else
                        Total {{ $totalCount }} UMKM terdaftar
                    @endif
                </div>
            @endif
        </div>
    @endif

    {{-- UMKM Grid --}}
    @if (is_countable($umkmProfiles) ? $umkmProfiles->count() > 0 : $umkmProfiles->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($umkmProfiles as $profile)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 group">
                    {{-- Header with Icon --}}
                    <div class="bg-gradient-to-br from-orange-100 to-orange-50 p-6 text-center relative">
                        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm group-hover:shadow-md transition-shadow duration-300">
                            @if ($profile->logo)
                                <img src="{{ asset('storage/' . $profile->logo) }}" alt="{{ $profile->business_name }}"
                                    class="w-16 h-16 rounded-full object-cover">
                            @else
                                <span class="text-3xl">
                                    {{ $this->getCategoryIcon($profile->business_category) }}
                                </span>
                            @endif
                        </div>

                        <h3 class="font-bold text-gray-900 text-lg mb-1">
                            {{ $profile->business_name }}
                        </h3>

                        <p class="text-gray-600 text-sm">
                            {{ $profile->owner_name }}
                        </p>
                    </div>

                    {{-- Content --}}
                    <div class="p-6">
                        {{-- Address --}}
                        @if ($profile->address)
                            <div class="flex items-start text-gray-600 text-sm mb-4">
                                <span class="mr-2 mt-0.5">📍</span>
                                <span>{{ Str::limit($profile->address, 60) }}</span>
                            </div>
                        @endif

                        {{-- Business Category --}}
                        @if ($profile->business_category)
                            <div class="flex items-center text-gray-600 text-sm mb-4">
                                <span class="mr-2">🏷️</span>
                                <span class="bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-xs font-medium">
                                    {{ ucfirst($profile->business_category) }}
                                </span>
                            </div>
                        @endif

                        {{-- Description --}}
                        @if ($profile->business_description)
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ Str::limit($profile->business_description, 100) }}
                            </p>
                        @endif

                        {{-- Quick Contact --}}
                        @if ($profile->whatsapp || $profile->instagram)
                            <div class="flex items-center space-x-4 mb-4 pb-4 border-b border-gray-100">
                                @if ($profile->whatsapp)
                                    <a href="https://wa.me/{{ $profile->whatsapp }}" target="_blank"
                                        class="flex items-center text-green-600 hover:text-green-700 text-sm transition-colors duration-200">
                                        <span class="mr-1">📱</span>
                                        <span>WhatsApp</span>
                                    </a>
                                @endif

                                @if ($profile->instagram)
                                    <a href="https://instagram.com/{{ $profile->instagram }}" target="_blank"
                                        class="flex items-center text-pink-600 hover:text-pink-700 text-sm transition-colors duration-200">
                                        <span class="mr-1">📸</span>
                                        <span>Instagram</span>
                                    </a>
                                @endif
                            </div>
                        @endif

                        {{-- Action Button --}}
                        <button wire:click='viewProfile("{{ $profile->slug }}")'
                            class="w-full bg-primary-400 hover:bg-primary-300 text-white font-semibold py-3 px-4 rounded-xl transition-colors duration-200 flex items-center justify-center group-hover:bg-primary-400">
                            Lihat Profil
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination (only show when showPagination is true) --}}
        @if ($showPagination && method_exists($umkmProfiles, 'links'))
            <div class="mt-8">
                {{ $umkmProfiles->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-16">
            <div class="mb-6">
                <div class="w-24 h-24 bg-gradient-to-br from-orange-100 to-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">🏪</span>
                </div>
            </div>
            <h4 class="text-xl font-semibold text-gray-900 mb-3">
                @if ($searchTerm || $selectedCategory)
                    Tidak Ada UMKM Yang Cocok
                @else
                    Belum Ada Profil UMKM Terdaftar
                @endif
            </h4>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                @if ($searchTerm || $selectedCategory)
                    Coba gunakan kata kunci atau kategori yang berbeda untuk menemukan UMKM yang Anda cari.
                @else
                    Jadilah yang pertama mendaftarkan usaha Anda di platform digital UMKM komunitas ini dan raih peluang bisnis yang lebih luas!
                @endif
            </p>
            @if ($searchTerm || $selectedCategory)
                <button wire:click="clearFilters"
                    class="inline-flex items-center px-6 py-3 bg-orange-500 text-white font-medium rounded-xl hover:bg-orange-600 transition-colors duration-200">
                    <span class="mr-2">🔄</span>
                    Reset Filter
                </button>
            @endif
        </div>
    @endif

    @push('styles')
        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>
    @endpush
</div>
