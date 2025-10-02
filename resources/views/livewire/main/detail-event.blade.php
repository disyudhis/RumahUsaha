<?php

use App\Models\Event;
use Livewire\Volt\Component;

new class extends Component {
    public Event $event;

    public function mount($slug)
    {
        $this->event = Event::where('slug', $slug)->firstOrFail();
    }

    public function with(): array
    {
        return [
            'relatedEvents' => Event::where('id', '!=', $this->event->id)->latest()->take(3)->get(),
        ];
    }
}; ?>

<div class="min-h-screen bg-gradient-to-br from-neutral-50 via-accent-50 to-primary-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center text-sm font-medium text-neutral-700 hover:text-primary-600 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                            </path>
                        </svg>
                        Dashboard
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-neutral-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        {{-- <a href="{{ route('events.index') }}"
                            class="ml-1 text-sm font-medium text-neutral-700 hover:text-primary-600 transition-colors md:ml-2">Event</a> --}}
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-neutral-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-neutral-500 md:ml-2">Detail</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Event Detail - Main Column -->
            <div class="lg:col-span-2 space-y-6">

                <!-- Event Image Card -->
                <div x-data="{ open: false }" class="bg-white rounded-2xl shadow-warm-lg overflow-hidden">
                    <div class="relative h-96 bg-gradient-to-br from-primary-100 to-accent-100">
                        @if ($event->image)
                            <!-- Event Image -->
                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}"
                                class="w-full h-full object-cover cursor-zoom-in transition-transform duration-500 hover:scale-105"
                                @click="open = true">
                        @else
                            <div class="flex items-center justify-center h-full">
                                <div class="text-center">
                                    <svg class="w-24 h-24 mx-auto text-primary-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <p class="mt-4 text-primary-600 font-medium">Event Image</p>
                                </div>
                            </div>
                        @endif

                        <!-- Date Badge -->
                        <div class="absolute top-6 left-6">
                            <div class="bg-white rounded-xl shadow-warm-lg p-4 text-center min-w-[80px]">
                                <div class="text-3xl font-bold text-primary-600">
                                    {{ $event->event_date->format('d') }}
                                </div>
                                <div class="text-sm font-medium text-neutral-600 uppercase">
                                    {{ $event->event_date->format('M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Zoom -->
                    <div x-show="open" x-transition
                        class="fixed inset-0 bg-black/80 flex items-center justify-center z-50"
                        @click.self="open = false">
                        <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}"
                            class="max-h-[90vh] max-w-[90vw] rounded-lg shadow-2xl">
                    </div>
                </div>



                <!-- Event Info Card -->
                <div class="bg-white rounded-2xl shadow-warm-lg p-8">
                    <div class="space-y-6">

                        <!-- Title -->
                        <div>
                            <h1 class="text-4xl font-bold text-neutral-900 mb-4 font-aleo">
                                {{ $event->title }}
                            </h1>

                            <!-- Meta Info -->
                            <div class="flex flex-wrap gap-4 text-sm">
                                <div class="flex items-center text-neutral-600">
                                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                    <span class="font-medium">{{ $event->formatted_event_date }}</span>
                                </div>

                                <div class="flex items-center text-neutral-600">
                                    <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">{{ $event->event_date->format('H:i') }} WIB</span>
                                </div>

                                <!-- Status Badge -->
                                @if ($event->event_date->isFuture())
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-success-100 text-success-700">
                                        <span class="w-2 h-2 mr-2 bg-success-500 rounded-full animate-pulse"></span>
                                        Akan Datang
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-neutral-100 text-neutral-700">
                                        <span class="w-2 h-2 mr-2 bg-neutral-500 rounded-full"></span>
                                        Selesai
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-neutral-200"></div>

                        <!-- Description -->
                        <div>
                            <h2 class="text-xl font-bold text-neutral-900 mb-4 font-aleo">
                                Tentang Event
                            </h2>
                            <div class="prose prose-neutral max-w-none">
                                <p class="text-neutral-700 leading-relaxed whitespace-pre-line">
                                    {{ $event->description }}
                                </p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        {{-- <div class="flex flex-wrap gap-4 pt-4">
                            <button type="button"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white font-semibold rounded-xl shadow-warm transition-all duration-300 transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                Daftar Event
                            </button>

                            <button type="button"
                                class="inline-flex items-center px-6 py-3 bg-white hover:bg-neutral-50 text-neutral-700 font-semibold rounded-xl shadow-warm border-2 border-neutral-200 transition-all duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z">
                                    </path>
                                </svg>
                                Bagikan
                            </button>
                        </div> --}}
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-6">

                <!-- Event Details Card -->
                <div class="bg-white rounded-2xl shadow-warm-lg p-6 sticky top-8">
                    <h3 class="text-lg font-bold text-neutral-900 mb-4 font-aleo">
                        Informasi Event
                    </h3>

                    <div class="space-y-4">
                        <!-- Date Info -->
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-neutral-500">Tanggal</p>
                                <p class="text-sm font-semibold text-neutral-900">
                                    {{ $event->event_date->isoFormat('dddd, D MMMM Y') }}
                                </p>
                            </div>
                        </div>

                        <!-- Time Info -->
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-accent-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-neutral-500">Waktu</p>
                                <p class="text-sm font-semibold text-neutral-900">
                                    {{ $event->event_date->format('H:i') }} WIB
                                </p>
                            </div>
                        </div>

                        <!-- Created At -->
                        <div class="flex items-start">
                            <div
                                class="flex-shrink-0 w-10 h-10 bg-secondary-100 rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-neutral-500">Dibuat</p>
                                <p class="text-sm font-semibold text-neutral-900">
                                    {{ $event->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Countdown (if future event) -->
                    @if ($event->event_date->isFuture())
                        <div class="mt-6 pt-6 border-t border-neutral-200">
                            <div class="bg-gradient-to-br from-primary-50 to-accent-50 rounded-xl p-4 text-center">
                                <p class="text-sm font-medium text-neutral-600 mb-2">Dimulai dalam</p>
                                <p class="text-2xl font-bold text-primary-600">
                                    {{ $event->event_date->diffForHumans(['parts' => 2]) }}
                                </p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Related Events -->
                @if ($relatedEvents->count() > 0)
                    <div class="bg-white rounded-2xl shadow-warm-lg p-6">
                        <h3 class="text-lg font-bold text-neutral-900 mb-4 font-aleo">
                            Event Lainnya
                        </h3>

                        <div class="space-y-4">
                            @foreach ($relatedEvents as $related)
                                <a href="{{ route('events.show', $related->slug) }}" class="block group">
                                    <div class="flex gap-3 p-3 rounded-xl hover:bg-neutral-50 transition-colors">
                                        <div
                                            class="flex-shrink-0 w-16 h-16 bg-gradient-to-br from-primary-100 to-accent-100 rounded-lg overflow-hidden">
                                            @if ($related->image)
                                                <img src="{{ Storage::url($related->image) }}"
                                                    alt="{{ $related->title }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-primary-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4
                                                class="text-sm font-semibold text-neutral-900 group-hover:text-primary-600 transition-colors line-clamp-2">
                                                {{ $related->title }}
                                            </h4>
                                            <p class="text-xs text-neutral-500 mt-1">
                                                {{ $related->event_date->format('d M Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
