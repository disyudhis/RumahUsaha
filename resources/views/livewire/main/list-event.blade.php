<?php

use Livewire\Volt\Component;
use App\Models\Event;
use Carbon\Carbon;

new class extends Component {
    public function with(): array
    {
        // Ambil data dari database, jika kosong gunakan data statis
        $events = Event::orderBy('event_date', 'desc')->limit(6)->get();

        // Jika database kosong, gunakan data dummy untuk demo
        if ($events->isEmpty()) {
            $dummyEvents = collect([
                [
                    'id' => 1,
                    'title' => 'Workshop "Foto Produk HP"',
                    'event_date' => '2024-07-28',
                    'description' => 'Belajar teknik fotografi produk menggunakan smartphone untuk meningkatkan kualitas foto produk UMKM Anda.',
                ],
                [
                    'id' => 2,
                    'title' => 'Bazaar UMKM Komunitas',
                    'event_date' => '2024-08-15',
                    'description' => 'Pameran dan penjualan produk-produk unggulan dari anggota komunitas UMKM.',
                ],
                [
                    'id' => 3,
                    'title' => 'Pelatihan Digital Marketing',
                    'event_date' => '2024-09-05',
                    'description' => 'Workshop lengkap tentang strategi pemasaran digital untuk UMKM di era modern.',
                ],
                [
                    'id' => 4,
                    'title' => 'Kolaborasi Bisnis Antar UMKM',
                    'event_date' => '2024-09-20',
                    'description' => 'Session networking dan diskusi peluang kolaborasi bisnis antar anggota komunitas.',
                ],
            ]);

            $events = $dummyEvents;
        }

        // Transform data untuk menambahkan icon dan status
        $transformedEvents = $events->map(function ($event) {
            return [
                'id' => $event['id'] ?? $event->id,
                'icon' => $this->getEventIcon($event['title'] ?? $event->title),
                'type' => $this->getEventType($event['title'] ?? $event->title),
                'title' => $this->getEventTitle($event['title'] ?? $event->title),
                'full_title' => $event['title'] ?? $event->title,
                'event_date' => $event['event_date'] ?? $event->event_date,
                'description' => $event['description'] ?? ($event->description ?? ''),
                'status' => $this->getEventStatus($event['event_date'] ?? $event->event_date),
            ];
        });

        return ['events' => $transformedEvents];
    }

    private function getEventIcon(string $title): string
    {
        if (str_contains(strtolower($title), 'workshop') || str_contains(strtolower($title), 'fotografi')) {
            return '📸';
        }
        if (str_contains(strtolower($title), 'bazaar') || str_contains(strtolower($title), 'pameran')) {
            return '🎪';
        }
        if (str_contains(strtolower($title), 'pelatihan') || str_contains(strtolower($title), 'training')) {
            return '📚';
        }
        if (str_contains(strtolower($title), 'kolaborasi') || str_contains(strtolower($title), 'kerjasama')) {
            return '🤝';
        }
        if (str_contains(strtolower($title), 'digital') || str_contains(strtolower($title), 'marketing')) {
            return '💻';
        }
        return '📅'; // Default icon
    }

    private function getEventType(string $title): string
    {
        if (str_contains(strtolower($title), 'workshop')) {
            return 'Workshop';
        }
        if (str_contains(strtolower($title), 'pelatihan')) {
            return 'Pelatihan';
        }
        if (str_contains(strtolower($title), 'bazaar')) {
            return 'Bazaar';
        }
        if (str_contains(strtolower($title), 'kolaborasi')) {
            return 'Kolaborasi';
        }
        return 'Acara'; // Default type
    }

    private function getEventTitle(string $title): string
    {
        // Extract quoted content if exists
        if (preg_match('/"([^"]+)"/', $title, $matches)) {
            return $matches[1];
        }

        // Remove type prefix if exists
        $title = preg_replace('/^(workshop|pelatihan|bazaar|kolaborasi):\s*/i', '', $title);

        return $title;
    }

    private function getEventStatus($eventDate): string
    {
        if (!$eventDate) {
            return 'ongoing';
        }

        $now = Carbon::now();
        $event = Carbon::parse($eventDate);

        if ($event->isFuture()) {
            return 'upcoming';
        } elseif ($event->isToday()) {
            return 'ongoing';
        } else {
            return 'completed';
        }
    }

    private function formatEventDate($eventDate): string
    {
        if (!$eventDate) {
            return 'Ongoing';
        }

        $date = Carbon::parse($eventDate);
        $months = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        return $date->day . ' ' . $months[$date->month];
    }

    public function viewEvent($eventId)
    {
        // Implement event detail view later
        // return $this->redirect(route('event.show', ['id' => $eventId]), navigate: true);
    }
}; ?>

<div class="mb-8">
    @if ($events->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($events as $event)
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 group">
                    {{-- Header --}}
                    <div class="p-6 border-b border-gray-100">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center">
                                {{-- Icon --}}
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-orange-100 to-orange-50 rounded-xl flex items-center justify-center mr-4 group-hover:from-orange-200 group-hover:to-orange-100 transition-colors duration-300">
                                    <span class="text-xl">{{ $event['icon'] }}</span>
                                </div>

                                {{-- Event Info --}}
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span
                                            class="bg-orange-100 text-orange-700 px-2 py-1 rounded-full text-xs font-medium">
                                            {{ $event['type'] }}
                                        </span>
                                        @if ($event['status'] === 'upcoming')
                                            <span
                                                class="bg-green-100 text-green-700 px-2 py-1 rounded-full text-xs font-medium">
                                                Segera
                                            </span>
                                        @elseif($event['status'] === 'ongoing')
                                            <span
                                                class="bg-blue-100 text-blue-700 px-2 py-1 rounded-full text-xs font-medium">
                                                Berlangsung
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="font-bold text-gray-900 text-base leading-tight">
                                        {{ $event['title'] ? $event['title'] : $event['full_title'] }}
                                    </h3>
                                </div>
                            </div>

                            {{-- Date --}}
                            <div class="text-right flex-shrink-0 ml-4">
                                <div class="bg-gray-50 rounded-lg px-3 py-2 text-center min-w-[60px]">
                                    <div class="text-lg font-bold text-gray-900">
                                        {{ $this->formatEventDate($event['event_date']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-6">
                        {{-- Description --}}
                        @if ($event['description'])
                            <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                                {{ $event['description'] }}
                            </p>
                        @endif

                        {{-- Status Indicator --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-sm text-gray-500">
                                <span class="mr-1">📅</span>
                                <span>{{ Carbon::parse($event['event_date'])->format('d M Y') }}</span>
                            </div>

                            {{-- Action Button --}}
                            <button wire:click="viewEvent({{ $event['id'] }})"
                                class="bg-primary-400 hover:bg-primary-300 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                                Detail
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Show More Button --}}
        {{-- <div class="mt-8 text-center">
            <a href="#"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <span class="mr-2">📰</span>
                Lihat Semua Berita & Kegiatan
            </a>
        </div> --}}
    @else
        {{-- Empty State --}}
        <div class="text-center py-16">
            <div class="mb-6">
                <div
                    class="w-24 h-24 bg-gradient-to-br from-orange-100 to-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">📰</span>
                </div>
            </div>
            <h4 class="text-xl font-semibold text-gray-900 mb-3">
                Belum Ada Berita & Kegiatan
            </h4>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                Stay tuned! Kami akan segera menghadirkan berbagai kegiatan menarik dan berita terbaru untuk komunitas
                UMKM.
            </p>
            <a href="#"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <span class="mr-2">📝</span>
                Buat Kegiatan Baru
            </a>
        </div>
    @endif
    @push('styles')
        <style>
            .line-clamp-3 {
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>
    @endpush
</div>
