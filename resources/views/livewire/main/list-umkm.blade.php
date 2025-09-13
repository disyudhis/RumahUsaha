<?php

// resources/views/livewire/main/list-umkm.blade.php
use Livewire\Volt\Component;
use App\Models\UmkmProfile;

new class extends Component {
    /**
     * Get latest UMKM profiles
     */
    public function with()
    {
        return [
            'umkmProfiles' => UmkmProfile::with('user')
                ->orderBy('created_at', 'desc')
                ->limit(5) // Tampilkan 5 profil terbaru
                ->get(),
        ];
    }

    /**
     * View UMKM profile (you can implement routing later)
     */
    // In your list-umkm component, update the viewProfile method:

    public function viewProfile($profileId)
    {
        // Use the correct route name
        return $this->redirect(route('business.show', ['id' => $profileId]), navigate: true);
    }
}; ?>

<div class="mb-8">
    <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
        <span class="mr-2">👥</span>
        PROFIL UMKM ANGGOTA TERBARU
    </h3>

    @if ($umkmProfiles->count() > 0)
    <div class="space-y-4">
        @foreach ($umkmProfiles as $profile)
        <div
            class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    {{-- Logo/Avatar --}}
                    <div class="flex-shrink-0 mr-4">
                        @if ($profile->logo)
                        <img src="{{ asset('storage/' . $profile->logo )) }}" alt="{{ $profile->business_name }}"
                            class="w-12 h-12 rounded-lg object-cover">
                        @else
                        <div
                            class="bg-gradient-to-br from-blue-500 to-purple-600 w-12 h-12 rounded-lg flex items-center justify-center">
                            <span class="text-white font-semibold text-lg">
                                {{ substr($profile->business_name, 0, 1) }}
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Business Info --}}
                    <div class="flex-grow">
                        <h4 class="font-semibold text-gray-900 text-base">
                            {{ $profile->business_name }}
                        </h4>
                        <p class="text-sm text-gray-500 mt-1">
                            <span class="mr-1">👤</span>
                            {{ $profile->owner_name }}
                        </p>
                        @if ($profile->address)
                        <p class="text-sm text-gray-500 mt-1">
                            <span class="mr-1">📍</span>
                            {{ Str::limit($profile->address, 50) }}
                        </p>
                        @endif
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="flex-shrink-0 ml-4">
                    <button wire:click="viewProfile({{ $profile->id }})"
                        class="bg-blue-500 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                        Lihat Profil
                    </button>
                </div>
            </div>

            {{-- Quick Contact Info --}}
            @if ($profile->whatsapp || $profile->instagram)
            <div class="mt-3 pt-3 border-t border-gray-100">
                <div class="flex items-center space-x-4 text-sm">
                    @if ($profile->whatsapp)
                    <a href="https://wa.me/{{ $profile->whatsapp }}" target="_blank"
                        class="flex items-center text-green-600 hover:text-green-700">
                        <span class="mr-1">📱</span>
                        WhatsApp
                    </a>
                    @endif
                    @if ($profile->instagram)
                    <a href="https://instagram.com/{{ $profile->instagram }}" target="_blank"
                        class="flex items-center text-pink-600 hover:text-pink-700">
                        <span class="mr-1">📸</span>
                        Instagram
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Show More Button --}}
    {{-- <div class="mt-6 text-center">
        <a href="#"
            class="inline-flex items-center px-6 py-3 bg-gray-100 text-gray-700 font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200">
            <span class="mr-2">👥</span>
            Lihat Semua Profil UMKM
        </a>
    </div> --}}
    @else
    {{-- Empty State --}}
    <div class="text-center py-12">
        <div class="mb-4">
            <span class="text-6xl">🏪</span>
        </div>
        <h4 class="text-lg font-medium text-gray-900 mb-2">
            Belum Ada Profil UMKM
        </h4>
        <p class="text-gray-500 mb-6">
            Jadilah yang pertama mendaftarkan usaha Anda di komunitas ini!
        </p>
        <a href="#"
            class="inline-flex items-center px-6 py-3 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors duration-200">
            <span class="mr-2">📝</span>
            Daftarkan Usaha Anda
        </a>
    </div>
    @endif
</div>