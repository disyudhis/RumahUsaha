<?php

use Livewire\Volt\Component;
use App\Models\Event;
use App\Models\UmkmProfile;
use Carbon\Carbon;

new class extends Component {
    public $totalEvents;
    public $totalUmkm;
    public $activeEvents;
    public $pendingApproval;
    public $recentEvents;
    public $recentUmkm;
    public $lastUpdated;

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        // Statistics
        $this->totalEvents = Event::count();
        $this->totalUmkm = UmkmProfile::count();
        $this->activeEvents = Event::where('event_date', '>=', now())->count();
        $this->pendingApproval = UmkmProfile::where('is_active', false)->count();

        // Recent data
        $this->recentEvents = Event::latest()->take(3)->get();
        $this->recentUmkm = UmkmProfile::with('user')->latest()->take(3)->get();

        $this->lastUpdated = now()->format('d M Y, H:i');
    }

    public function refreshData()
    {
        $this->loadDashboardData();
        session()->flash('message', 'Dashboard data berhasil diperbarui!');
    }
}; ?>

<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
                <p class="text-gray-600 mt-1">Kelola platform UMKM dan pantau aktivitas sistem</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Terakhir diperbarui: {{ $lastUpdated }}</span>
                </div>
                <button wire:click="refreshData"
                    class="px-3 py-1 text-sm bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200 transition-colors font-medium">
                    Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Flash Message -->
    @if (session()->has('message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Events -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-blue-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Total Events</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $totalEvents }}</div>
                        <div class="text-sm text-blue-600 mt-1">Total keseluruhan</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- UMKM Terdaftar -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-green-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">UMKM Terdaftar</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $totalUmkm }}</div>
                        <div class="text-sm text-green-600 mt-1">Total terdaftar</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Aktif -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-yellow-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Event Aktif</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $activeEvents }}</div>
                        <div class="text-sm text-green-600 mt-1">Akan datang</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approval -->
        <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-red-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <div class="text-sm font-medium text-gray-500 uppercase tracking-wide">Pending Approval</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $pendingApproval }}</div>
                        <div class="text-sm text-yellow-600 mt-1">Perlu review</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Events Terbaru -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Events Terbaru</h3>
                    <span class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-full font-medium">
                        {{ $recentEvents->count() }} Events
                    </span>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentEvents as $event)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $event->title }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($event->description, 50) }}</p>
                                <div class="flex items-center mt-2">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs text-gray-500">
                                        {{ $event->event_date ? $event->event_date->format('d M Y') : 'Tanggal belum ditentukan' }}
                                    </span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end space-y-2 ml-4">
                                <span class="text-sm text-gray-500">{{ $event->created_at->format('d M Y') }}</span>
                                {{-- <div class="flex space-x-2">
                                    <a href="{{ route('admin.events.edit', $event) }}"
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200 transition-colors font-medium">Edit</a>
                                    <a href="{{ route('admin.events.show', $event) }}"
                                        class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200 transition-colors font-medium">Detail</a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center">
                        <div class="text-gray-400 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Belum ada event yang dibuat</p>
                    </div>
                @endforelse
            </div>
            @if($recentEvents->count() > 0)
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('admin.event') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Lihat semua events →
                    </a>
                </div>
            @endif
        </div>

        <!-- UMKM Profiles -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">UMKM Profiles</h3>
                    <span class="px-3 py-1 text-xs bg-green-100 text-green-800 rounded-full font-medium">
                        {{ $recentUmkm->count() }} UMKM
                    </span>
                </div>
            </div>
            <div class="divide-y divide-gray-200">
                @forelse($recentUmkm as $umkm)
                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">{{ $umkm->business_name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">{{ Str::limit($umkm->description, 40) ?: 'Deskripsi belum tersedia' }}</p>
                                <div class="flex items-center mt-2">
                                    <svg class="w-4 h-4 text-gray-400 mr-1" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-xs text-gray-500">{{ $umkm->owner_name }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end space-y-2 ml-4">
                                <span class="px-2 py-1 text-xs rounded-full font-medium {{ $umkm->is_active ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $umkm->is_active ? 'Aktif' : 'Pending' }}
                                </span>
                                {{-- <div class="flex space-x-2">
                                    <a href="{{ route('admin.umkm.edit', $umkm) }}"
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded-md hover:bg-blue-200 transition-colors font-medium">Edit</a>
                                    <a href="{{ route('admin.umkm.show', $umkm) }}"
                                        class="px-3 py-1 text-xs bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200 transition-colors font-medium">View</a>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-8 text-center">
                        <div class="text-gray-400 mb-2">
                            <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <p class="text-sm text-gray-500">Belum ada UMKM yang terdaftar</p>
                    </div>
                @endforelse
            </div>
            @if($recentUmkm->count() > 0)
                <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                    <a href="{{ route('admin.umkm') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Lihat semua UMKM →
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('admin.event') }}"
                class="bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-6 py-4 rounded-lg text-left transition-all duration-200 transform hover:scale-105 shadow-sm">
                <div class="flex items-center">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-medium">Buat Event Baru</div>
                        <div class="text-sm opacity-90 mt-1">Workshop, seminar, atau bazar</div>
                    </div>
                </div>
            </a>

            <a href="{{ route('admin.umkm') }}"
                class="bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 text-white px-6 py-4 rounded-lg text-left transition-all duration-200 transform hover:scale-105 shadow-sm">
                <div class="flex items-center">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-medium">Daftar UMKM Baru</div>
                        <div class="text-sm opacity-90 mt-1">Registrasi profil UMKM baru</div>
                    </div>
                </div>
            </a>

            {{-- <a href="{{ route('admin.umkm.pending') }}"
                class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white px-6 py-4 rounded-lg text-left transition-all duration-200 transform hover:scale-105 shadow-sm">
                <div class="flex items-center">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-medium">Review Approval</div>
                        <div class="text-sm opacity-90 mt-1">{{ $pendingApproval }} UMKM pending</div>
                    </div>
                </div>
            </a> --}}

            {{-- <a href="{{ route('admin.reports') }}"
                class="bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white px-6 py-4 rounded-lg text-left transition-all duration-200 transform hover:scale-105 shadow-sm">
                <div class="flex items-center">
                    <div class="bg-white bg-opacity-20 rounded-lg p-2 mr-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="font-medium">Laporan & Analytics</div>
                        <div class="text-sm opacity-90 mt-1">Statistik lengkap platform</div>
                    </div>
                </div>
            </a> --}}
        </div>
    </div>
</div>
