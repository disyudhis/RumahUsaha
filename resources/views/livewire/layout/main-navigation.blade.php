<?php
// resources/views/livewire/components/main-navigation.blade.php

use Livewire\Volt\Component;
use App\Livewire\Actions\Logout;
use App\Models\User;

new class extends Component {
    public $userType = null;
    public $isMobileMenuOpen = false;

    public function mount()
    {
        $this->userType = auth()->check() ? auth()->user()->user_type : 'guest';
    }

    public function logout(Logout $logout): void
    {
        $logout();
        $this->redirect('/', navigate: true);
    }

    public function toggleMobileMenu()
    {
        $this->isMobileMenuOpen = !$this->isMobileMenuOpen;
    }

    public function getNavigationItems()
    {
        switch ($this->userType) {
            case User::ROLE_ADMIN:
                return [
                    ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
                    ['label' => 'Profil UMKM', 'route' => 'admin.umkm'],
                    ['label' => 'Event', 'route' => 'admin.event'],
                ];

            case User::ROLE_UMKM_OWNER:
                return [
                    ['label' => 'Dashboard', 'route' => 'umkm.dashboard'],
                    ['label' => 'Products', 'route' => 'umkm.list-product'],
                    ['label' => 'Profile', 'route' => 'umkm.profile'],
                ];

            default:
                return [['label' => 'Home', 'route' => 'home']];
        }
    }

    public function getBrandText()
    {
        return 'BIZHOUSE.ID';
    }

    public function isActiveRoute($route)
    {
        return request()->routeIs($route);
    }
}; ?>

<div>
    {{-- Header Navigation --}}
    <header class="bg-white shadow-sm sticky top-0 z-50 border-b border-accent-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                {{-- Brand/Logo --}}
                <div class="flex-shrink-0">
                    <a href="{{ $userType === User::ROLE_ADMIN ? route('admin.dashboard') : ($userType === User::ROLE_UMKM_OWNER ? route('umkm.dashboard') : route('home')) }}"
                        class="flex items-center text-xl font-bold text-secondary-800 hover:text-primary-600 transition-colors">
                        <div class="w-8 h-8 bg-fix-400 rounded-lg flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                            </svg>
                        </div>
                        {{ $this->getBrandText() }}
                    </a>
                </div>

                <div class="flex items-center space-x-4">
                    {{-- Desktop Navigation --}}
                    <nav class="hidden md:flex space-x-1" aria-label="Main navigation">
                        @foreach ($this->getNavigationItems() as $item)
                            <a href="{{ route($item['route']) }}"
                                class="@if ($this->isActiveRoute($item['route'])) bg-primary-50 text-primary-700 border-primary-200 @else text-secondary-700 hover:text-primary-600 hover:bg-accent-50 border-transparent @endif px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200 border"
                                @if ($this->isActiveRoute($item['route'])) aria-current="page" @endif>
                                {{ $item['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    {{-- User Menu Dropdown --}}
                    <div class="hidden md:block relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="flex items-center space-x-2 px-3 py-2 text-secondary-700 hover:text-primary-600 hover:bg-accent-50 rounded-lg transition-all duration-200"
                            aria-expanded="false" aria-haspopup="true">
                            <div class="w-8 h-8 bg-gradient-to-r from-primary-600 to-primary-500 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <svg class="w-4 h-4 transform transition-transform" :class="{ 'rotate-180': open }"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-warm-lg border border-accent-100 py-1 z-50"
                            style="display: none;">

                            {{-- User Info --}}
                            <div class="px-4 py-3 border-b border-accent-100">
                                <p class="text-sm font-medium text-secondary-800">{{ auth()->user()->name ?? 'User' }}</p>
                                <p class="text-xs text-secondary-500 truncate">{{ auth()->user()->email ?? '' }}</p>
                            </div>

                            {{-- Menu Items --}}
                            <div class="py-1">
                                @if ($userType === User::ROLE_UMKM_OWNER)
                                    <a href="{{ route('umkm.profile') }}"
                                        class="flex items-center px-4 py-2 text-sm text-secondary-700 hover:bg-accent-50 hover:text-primary-600 transition-colors">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        My Profile
                                    </a>
                                @endif

                                <button wire:click="logout"
                                    class="flex items-center w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile menu button --}}
                    <button type="button" class="md:hidden p-2 rounded-lg text-secondary-400 hover:text-secondary-500 hover:bg-accent-50 transition-colors"
                        wire:click="toggleMobileMenu"
                        aria-expanded="{{ $isMobileMenuOpen ? 'true' : 'false' }}"
                        aria-controls="mobile-menu"
                        aria-label="Toggle navigation menu">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            @if ($isMobileMenuOpen)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            @endif
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Navigation Menu --}}
            @if ($isMobileMenuOpen)
                <div class="md:hidden border-t border-accent-200" id="mobile-menu">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        {{-- Navigation Items --}}
                        @foreach ($this->getNavigationItems() as $item)
                            <a href="{{ route($item['route']) }}"
                                class="@if ($this->isActiveRoute($item['route'])) bg-primary-50 text-primary-700 @else text-secondary-700 hover:text-primary-600 hover:bg-accent-50 @endif block px-3 py-2 rounded-lg text-base font-medium transition-colors"
                                wire:click="toggleMobileMenu">
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        {{-- User Section --}}
                        <div class="border-t border-accent-200 pt-4 mt-4">
                            {{-- User Info --}}
                            <div class="px-3 py-2 mb-2">
                                <p class="text-sm font-medium text-secondary-800">{{ auth()->user()->name ?? 'User' }}</p>
                                <p class="text-xs text-secondary-500">{{ auth()->user()->email ?? '' }}</p>
                            </div>

                            {{-- Profile Link for UMKM Owner --}}
                            @if ($userType === User::ROLE_UMKM_OWNER)
                                <a href="{{ route('umkm.profile') }}"
                                    class="flex items-center px-3 py-2 text-base font-medium text-secondary-700 hover:text-primary-600 hover:bg-accent-50 rounded-lg transition-colors"
                                    wire:click="toggleMobileMenu">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    My Profile
                                </a>
                            @endif

                            {{-- Logout Button --}}
                            <button wire:click="logout"
                                class="flex items-center w-full text-left px-3 py-2 text-base font-medium text-red-600 hover:bg-red-50 rounded-lg transition-colors mt-1">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                Logout
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </header>
</div>
