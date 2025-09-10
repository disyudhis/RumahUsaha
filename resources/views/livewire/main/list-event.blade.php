<?php

use Livewire\Volt\Component;
use App\Models\Event;
use Carbon\Carbon;

new class extends Component {
    public function with(): array
    {
        // Ambil data dari database, jika kosong gunakan data statis
        $events = Event::orderBy('event_date', 'desc')->get();

        // Transform data dari database untuk menambahkan icon dan status
        $transformedEvents = $events->map(function ($event) {
            return [
                'icon' => $this->getEventIcon($event->title),
                'type' => $this->getEventType($event->title),
                'title' => $this->getEventTitle($event->title),
                'event_date' => $event->event_date,
                'status' => $this->getEventStatus($event->event_date),
            ];
        });

        return ['events' => $transformedEvents];
    }

    private function getEventIcon(string $title): string
    {
        if (str_contains(strtolower($title), 'workshop') || str_contains(strtolower($title), 'fotografi')) {
            return '📚';
        }
        if (str_contains(strtolower($title), 'bazaar')) {
            return '🎪';
        }
        if (str_contains(strtolower($title), 'pelatihan') || str_contains(strtolower($title), 'training')) {
            return '📖';
        }
        if (str_contains(strtolower($title), 'kolaborasi') || str_contains(strtolower($title), 'kerjasama')) {
            return '🤝';
        }
        return '📅'; // Default icon
    }

    private function getEventType(string $title): string
    {
        if (str_contains(strtolower($title), 'workshop')) {
            return 'Workshop:';
        }
        if (str_contains(strtolower($title), 'pelatihan')) {
            return 'Pelatihan:';
        }
        return $title; // Return full title if no specific type detected
    }

    private function getEventTitle(string $title): string
    {
        // If title contains workshop or pelatihan, extract the quoted part
        if (preg_match('/"([^"]+)"/', $title, $matches)) {
            return '"' . $matches[1] . '"';
        }

        // If title starts with known types, return empty (type will be shown separately)
        if (str_starts_with(strtolower($title), 'workshop:') || str_starts_with(strtolower($title), 'pelatihan:')) {
            return '';
        }

        return '';
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

        return Carbon::parse($eventDate)->format('d M Y');
    }
}; ?>

<section id="news-activities" class="mb-12 scroll-mt-20">
    <h2 class="text-2xl font-bold text-gray-900 mb-6">BERITA & KEGIATAN KOMUNITAS</h2>
    <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($events as $event)
                <div
                    class="flex items-start p-4 rounded-lg {{ $event['status'] === 'upcoming' ? 'bg-blue-50 border border-blue-200' : 'hover:bg-gray-50' }} transition-colors">
                    <span class="mr-3 text-xl flex-shrink-0" aria-hidden="true">{{ $event['icon'] }}</span>
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-1">
                            @if ($event['type'])
                                <span class="text-sm text-gray-600">{{ $event['type'] }}</span>
                            @endif
                            @if ($event['title'])
                                <span class="font-medium text-gray-900">{{ $event['title'] }}</span>
                            @endif
                        </div>
                        <div class="mt-1 flex items-center gap-2">
                            <time class="text-blue-600 text-sm font-medium">
                                {{ $this->formatEventDate($event['event_date']) }}
                            </time>
                            @if ($event['status'] === 'upcoming')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    Segera
                                </span>
                            @elseif($event['status'] === 'ongoing')
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Berlangsung
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
