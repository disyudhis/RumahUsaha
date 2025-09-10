<?php
// resources/views/livewire/components/navigation.blade.php

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
                return [['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'dashboard'], ['label' => 'Profil UMKM', 'route' => 'admin.umkm', 'icon' => 'business'], ['label' => 'Event', 'route' => 'admin.event', 'icon' => 'users']];
            // ['label' => 'Kelola UMKM', 'route' => 'admin.umkm.*', 'icon' => 'business'], ['label' => 'Kelola User', 'route' => 'admin.users.*', 'icon' => 'users'], ['label' => 'Kelola Produk', 'route' => 'admin.products.*', 'icon' => 'products'], ['label' => 'Laporan', 'route' => 'admin.reports.*', 'icon' => 'reports'], ['label' => 'Pengaturan', 'route' => 'admin.settings.*', 'icon' => 'settings']

            case User::ROLE_UMKM_OWNER:
                return [['label' => 'Dashboard', 'route' => 'umkm.dashboard', 'icon' => 'home'], ['label' => 'Products', 'route' => 'umkm.products', 'icon' => 'products']];
            //  ['label' => 'Produk', 'route' => 'umkm.products.*', 'icon' => 'products'], ['label' => 'Pesanan', 'route' => 'umkm.orders.*', 'icon' => 'orders'], ['label' => 'Laporan', 'route' => 'umkm.reports.*', 'icon' => 'reports'], ['label' => 'Profil', 'route' => 'umkm.profile.*', 'icon' => 'profile']
            default:
                // guest/public
                return [['label' => 'Home', 'route' => 'home', 'icon' => 'home']];
            // , ['label' => 'Profil Usaha', 'route' => 'business.*', 'icon' => 'business'], ['label' => 'Produk', 'route' => 'products.*', 'icon' => 'products'], ['label' => 'Berita', 'route' => 'news.*', 'icon' => 'news'], ['label' => 'Gabung', 'route' => 'membership.*', 'icon' => 'membership'], ['label' => 'Kontak', 'route' => 'contact', 'icon' => 'contact']
        }
    }

    public function getBrandText()
    {
        return 'RumahUsaha.id';
    }

    public function isActiveRoute($route)
    {
        return request()->routeIs($route);
    }
}; ?>

<div>
    {{-- Header Navigation --}}
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                {{-- Brand/Logo --}}
                <div class="text-xl font-bold text-gray-900">
                    {{ $this->getBrandText() }}
                </div>

                {{-- Desktop Navigation --}}
                @if ($userType === 'guest')
                    {{-- Public Navigation --}}
                    <nav class="hidden md:flex space-x-8">
                        @foreach ($this->getNavigationItems() as $item)
                            <a href="{{ $item['route'] !== '#' ? route($item['route']) : '#' }}"
                                class="{{ $this->isActiveRoute($item['route']) ? 'bg-gray-100 text-blue-600' : 'text-gray-700 hover:text-blue-600' }} px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        {{-- Auth Links for Guest --}}
                        {{-- <div class="flex space-x-2">
                            <a href="{{ route('login') }}"
                                class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
                                Login
                            </a>
                            <a href="{{ route('register') }}"
                                class="border border-blue-600 text-blue-600 px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-50 transition-colors">
                                Register
                            </a>
                        </div> --}}
                    </nav>
                @elseif($userType === User::ROLE_UMKM_OWNER)
                    {{-- UMKM Owner Navigation (Button Style) --}}
                    <nav class="hidden md:flex space-x-4">
                        @foreach ($this->getNavigationItems() as $item)
                            <a href="{{ $item['route'] !== '#' ? route($item['route']) : '#' }}"
                                class="{{ $this->isActiveRoute($item['route']) ? 'bg-gray-100 text-blue-600' : 'text-gray-700 hover:text-blue-600' }} px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        {{-- User Menu --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-blue-400 hover:text-blue-500 p-2">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                {{-- <a href="{{ route('profile') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a> --}}
                                <button wire:click="logout"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </div>
                        </div>
                    </nav>
                @else
                    {{-- Admin Navigation --}}
                    <nav class="hidden md:flex space-x-8">
                        @foreach ($this->getNavigationItems() as $item)
                            <a href="{{ $item['route'] !== '#' ? route($item['route']) : '#' }}"
                                class="{{ $this->isActiveRoute($item['route']) ? 'bg-gray-100 text-blue-600' : 'text-gray-700 hover:text-blue-600' }} px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                {{ $item['label'] }}
                            </a>
                        @endforeach

                        {{-- Admin User Menu --}}
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="text-gray-300 hover:text-white p-2">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </button>
                            <div x-show="open" @click.away="open = false" x-transition
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                                {{-- <a href="{{ route('profile') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profile</a> --}}
                                <button wire:click="logout"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Logout
                                </button>
                            </div>
                        </div>
                    </nav>
                @endif

                {{-- Mobile menu button --}}
                <button class="md:hidden p-2 text-gray-900" wire:click="toggleMobileMenu">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>

            {{-- Mobile Navigation Menu --}}
            @if ($isMobileMenuOpen)
                <div class="md:hidden border-t {{ $userType !== 'guest' ? 'border-gray-600' : 'border-gray-200' }}">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        @foreach ($this->getNavigationItems() as $item)
                            @if ($userType === User::ROLE_UMKM_OWNER)
                                <button
                                    onclick="window.location.href='{{ $item['route'] !== '#' ? route($item['route']) : '#' }}'"
                                    class="block w-full text-left px-3 py-2 text-base font-medium {{ isset($item['active']) ? 'bg-blue-700 text-white' : 'text-blue-100 hover:text-white hover:bg-blue-700' }} rounded-md">
                                    {{ $item['label'] }}
                                </button>
                            @else
                                <a href="{{ $item['route'] !== '#' ? route($item['route']) : '#' }}"
                                    class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600">
                                    {{ $item['label'] }}
                                </a>
                            @endif
                        @endforeach

                        {{-- @if ($userType === 'guest')
                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <a href="{{ route('login') }}"
                                    class="block px-3 py-2 text-base font-medium text-blue-600 hover:text-blue-800">Login</a>
                                <a href="{{ route('register') }}"
                                    class="block px-3 py-2 text-base font-medium text-blue-600 hover:text-blue-800">Register</a>
                            </div>
                        @else
                            <div
                                class="border-t {{ $userType !== 'guest' ? 'border-gray-600' : 'border-gray-200' }} pt-2 mt-2">
                                <a href="{{ route('profile') }}"
                                    class="block px-3 py-2 text-base font-medium {{ text-gray-700 hover:text-blue-600 }}">Profile</a>
                                <button wire:click="logout"
                                    class="block w-full text-left px-3 py-2 text-base font-medium {{ text-gray-700 hover:text-blue-600 }}">
                                    Logout
                                </button>
                            </div>
                        @endif --}}
                    </div>
                </div>
            @endif
        </div>
    </header>
</div>
