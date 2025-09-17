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
                ->limit(3) // Tampilkan 3 profil terbaru sesuai desain
                ->get(),
        ];
    }

    /**
     * View UMKM profile
     */
    public function viewProfile($profileId)
    {
        return $this->redirect(route('business.show', ['id' => $profileId]), navigate: true);
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
}; ?>

<div class="mb-8">
    @if ($umkmProfiles->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($umkmProfiles as $profile)
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 overflow-hidden border border-gray-100 group">
                    {{-- Header with Icon --}}
                    <div class="bg-gradient-to-br from-orange-100 to-orange-50 p-6 text-center relative">
                        <div
                            class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm group-hover:shadow-md transition-shadow duration-300">
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
                        <button wire:click="viewProfile({{ $profile->id }})"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-xl transition-colors duration-200 flex items-center justify-center group-hover:bg-orange-600">
                            <span class="mr-2">👁️</span>
                            Lihat Profil
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Show More Button --}}
        {{-- <div class="mt-8 text-center">
            <a href="#"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <span class="mr-2">👥</span>
                Lihat Semua Profil UMKM
            </a>
        </div> --}}
    @else
        {{-- Empty State --}}
        <div class="text-center py-16">
            <div class="mb-6">
                <div
                    class="w-24 h-24 bg-gradient-to-br from-orange-100 to-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <span class="text-4xl">🏪</span>
                </div>
            </div>
            <h4 class="text-xl font-semibold text-gray-900 mb-3">
                Belum Ada Profil UMKM Terdaftar
            </h4>
            <p class="text-gray-500 mb-8 max-w-md mx-auto">
                Jadilah yang pertama mendaftarkan usaha Anda di platform digital UMKM komunitas ini dan raih peluang
                bisnis yang lebih luas!
            </p>
            <a href="{{ route('business.create') }}"
                class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-orange-600 text-white font-semibold rounded-xl hover:from-orange-600 hover:to-orange-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                <span class="mr-2">📝</span>
                Daftarkan Usaha Anda
            </a>
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

