<header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-4">
            <div class="text-xl font-bold text-gray-900">
                RumahUsaha.id
            </div>
            <nav class="hidden md:flex space-x-8">
                <a href="#"
                   class="{{ request()->routeIs('home') ? 'bg-gray-100' : '' }} text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                   Home
                </a>
                <a href="#"
                   class="{{ request()->routeIs('business.*') ? 'bg-gray-100' : '' }} text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                   Profil Usaha
                </a>
                <a href="#"
                   class="{{ request()->routeIs('products.*') ? 'bg-gray-100' : '' }} text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                   Produk
                </a>
                <a href="#"
                   class="{{ request()->routeIs('news.*') ? 'bg-gray-100' : '' }} text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                   Berita
                </a>
                <a href="#"
                   class="{{ request()->routeIs('membership.*') ? 'bg-gray-100' : '' }} text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                   Gabung
                </a>
                <a href="#"
                   class="{{ request()->routeIs('contact') ? 'bg-gray-100' : '' }} text-gray-700 hover:text-blue-600 px-3 py-2 rounded-md text-sm font-medium">
                   Kontak
                </a>
            </nav>

            {{-- Mobile menu button --}}
            <button class="md:hidden p-2"
                    x-data="{ open: false }"
                    @click="open = !open"
                    id="mobile-menu-button">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        {{-- Mobile Navigation Menu --}}
        <div class="md:hidden" x-data="{ open: false }" x-show="open" x-collapse>
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600">Home</a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600">Profil Usaha</a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600">Produk</a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600">Berita</a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600">Gabung</a>
                <a href="#" class="block px-3 py-2 text-base font-medium text-gray-700 hover:text-blue-600">Kontak</a>
            </div>
        </div>
    </div>
</header>
