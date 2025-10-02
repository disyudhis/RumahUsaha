<?php

use Livewire\Volt\Component;
use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $selectedCategory = 'all';
    public $search = '';
    public $showAll = true; // Untuk membedakan tampilan dashboard vs index
    public $limit = 6; // Limit untuk dashboard
    public $showHero = false; // Show hero section di index
    public $showSearch = true; // Show search bar
    public $showCategoryFilter = true; // Show category pills
    public $showHeader = true; // Show section header
    public $headerTitle = 'Berita & Kegiatan';
    public $headerSubtitle = 'Informasi terkini seputar event, kolaborasi, dan pengembangan UMKM';

    public function mount($category = null)
    {
        // Set kategori dari URL parameter jika ada
        if ($category && array_key_exists($category, Event::CATEGORIES)) {
            $this->selectedCategory = $category;
            $this->showHero = true; // Show hero jika ada kategori spesifik
        } else {
            $this->selectedCategory = 'all';
            $this->showHero = true; // Show hero di halaman utama events
        }
    }

    public function getCategoryDescription($category)
    {
        $descriptions = [
            'all' => 'Temukan berbagai event kolaborasi, workshop, dan kegiatan pemberdayaan UMKM dari semua kategori',
            'kolaborasi-sosial' => 'Berisi liputan kegiatan yang fokus pada penguatan kapasitas UMKM melalui pendampingan, advokasi, maupun program pemberdayaan yang dilakukan bersama komunitas, perguruan tinggi, dan masyarakat.',
            'riset-inovasi' => 'Memuat berita dan publikasi hasil penelitian, uji coba, maupun inovasi teknologi yang diterapkan untuk mendukung peningkatan daya saing dan keberlanjutan UMKM.',
            'pengembangan-kapasitas' => 'Rubrik pelatihan, workshop, mentoring, hingga seminar yang ditujukan untuk meningkatkan keterampilan, literasi digital, serta strategi bisnis UMKM agar lebih siap menghadapi tantangan pasar.',
            'kemitraan-strategis' => 'Menginformasikan kerja sama formal maupun non-formal antara UMKM, perguruan tinggi, pemerintah, lembaga keuangan, dan stakeholder lainnya dalam bentuk MoU, joint program, hingga event kolaboratif.',
            'info-kampus' => 'Menampilkan kegiatan kampus yang terhubung dengan dunia usaha, seperti expo, inkubator bisnis, studentpreneur, hingga sinergi mahasiswa-dosen dengan UMKM dalam berbagai program nyata.',
        ];

        return $descriptions[$category] ?? 'Temukan berbagai event kolaborasi, workshop, dan kegiatan pemberdayaan UMKM';
    }

    public function with(): array
    {
        $query = Event::query()->orderBy('event_date', 'desc');

        // Filter berdasarkan kategori
        if ($this->selectedCategory !== 'all') {
            $query->where('categories', $this->selectedCategory);
        }

        // Filter berdasarkan pencarian
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        // Apply limit atau pagination
        $events = $this->showAll ? $query->paginate(9) : $query->limit($this->limit)->get();

        return [
            'events' => $events,
            'categories' => Event::CATEGORIES,
            'categoryDescription' => $this->getCategoryDescription($this->selectedCategory),
        ];
    }

    public function selectCategory($category)
    {
        $this->selectedCategory = $category;
        $this->resetPage();

        // Update URL jika di halaman full view
        if ($this->showAll) {
            $url = $category === 'all' ? route('events.index') : route('events.index', ['category' => $category]);

            $this->dispatch('update-url', url: $url);
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
    }
}; ?>

@if ($showAll)
    {{-- FULL PAGE VIEW (Index) --}}
    <div class="min-h-screen bg-gradient-to-br from-primary-50 via-accent-50 to-primary-100 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($showHero)
                {{-- Hero Section --}}
                <div class="mb-12 text-center">
                    <h1 class="text-4xl md:text-5xl font-bold text-secondary-900 mb-4 font-aleo">
                        @if ($selectedCategory !== 'all')
                            {{ $categories[$selectedCategory] }}
                        @else
                            Berita & Kegiatan UMKM
                        @endif
                    </h1>
                    <p class="text-lg text-secondary-700 max-w-3xl mx-auto font-inter leading-relaxed">
                        {{ $categoryDescription }}
                    </p>
                    <div class="mt-6 w-24 h-1 bg-gradient-to-r from-primary-400 to-primary-600 mx-auto rounded-full">
                    </div>
                </div>
            @endif

            @if ($showCategoryFilter)
                {{-- Category Pills --}}
                <div class="mb-8 overflow-x-auto">
                    <div class="flex gap-3 justify-center flex-wrap min-w-max px-4">
                        <button wire:click="selectCategory('all')"
                            class="px-6 py-2.5 rounded-full font-medium transition-all duration-200 font-inter text-sm
                            {{ $selectedCategory === 'all'
                                ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-warm'
                                : 'bg-white text-secondary-700 hover:bg-primary-50 hover:text-primary-600 border border-primary-200' }}">
                            Semua Event
                        </button>
                        @foreach ($categories as $key => $label)
                            <button wire:click="selectCategory('{{ $key }}')"
                                class="px-6 py-2.5 rounded-full font-medium transition-all duration-200 font-inter text-sm whitespace-nowrap
                                {{ $selectedCategory === $key
                                    ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-warm'
                                    : 'bg-white text-secondary-700 hover:bg-primary-50 hover:text-primary-600 border border-primary-200' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($showSearch)
                {{-- Search Bar --}}
                <div class="mb-10">
                    <div class="max-w-xl mx-auto">
                        <div class="relative">
                            <input type="text" wire:model.live.debounce.300ms="search"
                                placeholder="Cari event berdasarkan judul atau deskripsi..."
                                class="w-full px-6 py-4 pl-12 text-secondary-900 bg-white border-2 border-primary-200 rounded-xl focus:ring-2 focus:ring-primary-400 focus:border-primary-400 shadow-warm transition-all duration-200 font-inter">
                            <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-secondary-400"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Events Grid --}}
            @if ($events->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
                    @foreach ($events as $event)
                        <div
                            class="bg-white rounded-2xl shadow-warm overflow-hidden hover:shadow-warm-xl transition-all duration-300 transform hover:-translate-y-1">
                            {{-- Event Image --}}
                            <div
                                class="h-48 bg-gradient-to-br from-primary-400 via-primary-500 to-accent-500 relative overflow-hidden">
                                @if ($event->image)
                                    <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="flex items-center justify-center h-full">
                                        <svg class="w-20 h-20 text-white opacity-70" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif

                                {{-- Category Badge --}}
                                <div class="absolute top-4 left-4">
                                    <span
                                        class="px-3 py-1 bg-white/95 backdrop-blur-sm text-xs font-semibold text-secondary-800 rounded-full shadow-md font-inter">
                                        {{ $categories[$event->categories] ?? 'Uncategorized' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Event Content --}}
                            <div class="p-6">
                                {{-- Event Date --}}
                                <div class="flex items-center text-sm text-secondary-600 mb-3">
                                    <svg class="w-4 h-4 mr-2 text-primary-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="font-inter">{{ $event->formatted_event_date }}</span>
                                </div>

                                {{-- Event Title --}}
                                <h3 class="text-xl font-bold text-secondary-900 mb-3 line-clamp-2 font-aleo">
                                    {{ $event->title }}
                                </h3>

                                {{-- Event Description --}}
                                <p class="text-secondary-700 text-sm mb-4 line-clamp-3 font-inter leading-relaxed">
                                    {{ $event->description }}
                                </p>

                                {{-- View Details Button --}}
                                <a href="{{ route('main.events.show', $event->slug) }}"
                                    class="inline-flex items-center text-primary-600 hover:text-primary-700 font-semibold text-sm transition-colors duration-200 group font-inter">
                                    Lihat Detail
                                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform duration-200"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-8">
                    {{ $events->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-16 bg-white rounded-2xl shadow-warm max-w-2xl mx-auto">
                    <svg class="mx-auto h-24 w-24 text-primary-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <h3 class="mt-4 text-xl font-semibold text-secondary-900 font-aleo">Tidak ada event ditemukan</h3>
                    <p class="mt-2 text-secondary-600 font-inter">
                        @if ($search)
                            Tidak ada event yang cocok dengan pencarian "{{ $search }}"
                        @else
                            Belum ada event tersedia untuk kategori ini
                        @endif
                    </p>
                    @if ($search || $selectedCategory !== 'all')
                        <button wire:click="selectCategory('all')" onclick="@this.set('search', '')"
                            class="mt-6 px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-warm hover:shadow-warm-lg font-inter font-medium">
                            Lihat Semua Event
                        </button>
                    @endif
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('update-url', (event) => {
                    window.history.pushState({}, '', event.url);
                });
            });
        </script>
    @endpush
@else
    {{-- DASHBOARD VIEW (Compact) --}}
    <section class="py-12 bg-gradient-to-br from-white via-accent-50/30 to-primary-50/30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if ($showHeader)
                {{-- Section Header --}}
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-bold text-secondary-900 font-aleo mb-2">
                            {{ $headerTitle }}
                        </h2>
                        <p class="text-secondary-600 font-inter text-base md:text-lg">
                            {{ $headerSubtitle }}
                        </p>
                    </div>
                    <a href="{{ route('events.index') }}"
                        class="hidden md:inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-warm hover:shadow-warm-lg font-inter font-medium group">
                        Lihat Semua
                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-200"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @endif

            @if ($events->count() > 0)
                {{-- Grid Container --}}
                <div class="relative">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($events as $event)
                            <article
                                class="bg-white rounded-xl shadow-warm hover:shadow-warm-xl transition-all duration-300 overflow-hidden group">
                                {{-- Compact Image --}}
                                <div
                                    class="relative h-44 bg-gradient-to-br from-primary-400 to-accent-500 overflow-hidden">
                                    @if ($event->image)
                                        <img src="{{ asset('storage/' . $event->image) }}" alt="{{ $event->title }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="flex items-center justify-center h-full">
                                            <svg class="w-16 h-16 text-white opacity-60" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif

                                    {{-- Category Badge --}}
                                    <div class="absolute top-3 left-3">
                                        <span
                                            class="px-2.5 py-1 bg-white/95 backdrop-blur-sm text-xs font-semibold text-secondary-800 rounded-lg shadow-sm font-inter">
                                            {{ $categories[$event->categories] ?? 'Event' }}
                                        </span>
                                    </div>

                                    {{-- Date Badge --}}
                                    <div class="absolute bottom-3 right-3">
                                        <div class="bg-white/95 backdrop-blur-sm rounded-lg px-2.5 py-1.5 shadow-sm">
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-3.5 h-3.5 text-primary-600" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-xs font-medium text-secondary-800 font-inter">
                                                    {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Compact Content --}}
                                <div class="p-5">
                                    <h3
                                        class="text-lg font-bold text-secondary-900 mb-2 line-clamp-2 font-aleo leading-tight group-hover:text-primary-600 transition-colors duration-200">
                                        {{ $event->title }}
                                    </h3>

                                    <p class="text-secondary-600 text-sm mb-4 line-clamp-2 font-inter leading-relaxed">
                                        {{ $event->description }}
                                    </p>

                                    <a href="{{ route('main.events.show', $event->slug) }}"
                                        class="inline-flex items-center text-primary-600 hover:text-primary-700 font-semibold text-sm transition-colors duration-200 group/link font-inter">
                                        Selengkapnya
                                        <svg class="w-4 h-4 ml-1 group-hover/link:translate-x-1 transition-transform duration-200"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>

                {{-- Mobile View All Button --}}
                <div class="mt-8 text-center md:hidden">
                    <a href="{{ route('events.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 text-white rounded-xl hover:from-primary-600 hover:to-primary-700 transition-all duration-200 shadow-warm hover:shadow-warm-lg font-inter font-medium">
                        Lihat Semua Event
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                </div>
            @else
                {{-- Empty State Compact --}}
                <div class="text-center py-12 bg-white/50 rounded-xl border-2 border-dashed border-primary-200">
                    <svg class="mx-auto h-16 w-16 text-primary-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p class="mt-4 text-secondary-600 font-inter">Belum ada event tersedia saat ini</p>
                </div>
            @endif

        </div>
    </section>
@endif
