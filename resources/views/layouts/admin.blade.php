{{-- resources --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - UMKM Platform</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-interbg-gray-50">
    <livewire:layout.main-navigation />

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Events -->
            <div class="bg-white overflow-hidden shadow rounded-lg border border-blue-200">
                <div class="p-5">
                    <div class="text-sm font-medium text-gray-500 mb-2">TOTAL EVENTS</div>
                    <div class="text-3xl font-semibold text-gray-900">24</div>
                    <div class="text-sm text-blue-600 mt-1">+3 bulan ini</div>
                </div>
            </div>

            <!-- UMKM Terdaftar -->
            <div class="bg-white overflow-hidden shadow rounded-lg border border-blue-200">
                <div class="p-5">
                    <div class="text-sm font-medium text-gray-500 mb-2">UMKM TERDAFTAR</div>
                    <div class="text-3xl font-semibold text-gray-900">156</div>
                    <div class="text-sm text-blue-600 mt-1">+12 minggu ini</div>
                </div>
            </div>

            <!-- Event Aktif -->
            <div class="bg-white overflow-hidden shadow rounded-lg border border-blue-200">
                <div class="p-5">
                    <div class="text-sm font-medium text-gray-500 mb-2">EVENT AKTIF</div>
                    <div class="text-3xl font-semibold text-gray-900">8</div>
                    <div class="text-sm text-green-600 mt-1">Berlangsung</div>
                </div>
            </div>

            <!-- Pending Approval -->
            <div class="bg-white overflow-hidden shadow rounded-lg border border-blue-200">
                <div class="p-5">
                    <div class="text-sm font-medium text-gray-500 mb-2">PENDING APPROVAL</div>
                    <div class="text-3xl font-semibold text-gray-900">5</div>
                    <div class="text-sm text-yellow-600 mt-1">Perlu review</div>
                </div>
            </div>
        </div>

        <!-- Data Tables Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Events Terbaru -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">Events Terbaru</h3>
                    <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">Tanggal Post</span>
                </div>
                <div class="divide-y divide-gray-200">
                    <!-- Event 1 -->
                    <div class="px-6 py-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">Workshop Digital Marketing</h4>
                                <p class="text-sm text-gray-500 mt-1">Pelatihan kewirausahaan untuk UMKM</p>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span class="text-sm text-gray-500">29 Aug 2024</span>
                                <div class="flex space-x-2">
                                    <button
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200">Edit</button>
                                    <button
                                        class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded hover:bg-red-200">Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event 2 -->
                    <div class="px-6 py-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">Bazar UMKM Nasional</h4>
                                <p class="text-sm text-gray-500 mt-1">Pameran produk UMKM se Indonesia</p>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span class="text-sm text-gray-500">5 Sep 2024</span>
                                <div class="flex space-x-2">
                                    <button
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200">Edit</button>
                                    <button
                                        class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded hover:bg-red-200">Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Event 3 -->
                    <div class="px-6 py-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">Seminar Keuangan UMKM</h4>
                                <p class="text-sm text-gray-500 mt-1">Tips mengelola keuangan bisnis</p>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span class="text-sm text-gray-500">10 Sep 2024</span>
                                <div class="flex space-x-2">
                                    <button
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200">Edit</button>
                                    <button
                                        class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded hover:bg-red-200">Detail</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- UMKM Profiles -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900">UMKM Profiles</h3>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Status UMKM</span>
                </div>
                <div class="divide-y divide-gray-200">
                    <!-- UMKM 1 -->
                    <div class="px-6 py-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">Warung Mak Nyah</h4>
                                <p class="text-sm text-gray-500 mt-1">Kuliner Traditional - Jko Sel</p>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Aktif</span>
                                <div class="flex space-x-2">
                                    <button
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200">Edit</button>
                                    <button
                                        class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded hover:bg-red-200">View</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- UMKM 2 -->
                    <div class="px-6 py-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">Keripik Singkong Pak Budi</h4>
                                <p class="text-sm text-gray-500 mt-1">Makanan Ringan - Budi Sentoso</p>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded">Pending</span>
                                <div class="flex space-x-2">
                                    <button
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200">Review</button>
                                    <button
                                        class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded hover:bg-red-200">View</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- UMKM 3 -->
                    <div class="px-6 py-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="text-sm font-medium text-gray-900">Tas Rajut Handmade</h4>
                                <p class="text-sm text-gray-500 mt-1">Fashion & Aksesoris - Siti Aminah</p>
                            </div>
                            <div class="flex flex-col items-end space-y-2">
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Aktif</span>
                                <div class="flex space-x-2">
                                    <button
                                        class="px-3 py-1 text-xs bg-blue-100 text-blue-800 rounded hover:bg-blue-200">Edit</button>
                                    <button
                                        class="px-3 py-1 text-xs bg-red-100 text-red-800 rounded hover:bg-red-200">View</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-8">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button
                    class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-4 rounded-lg text-left transition-colors duration-200">
                    <div class="font-medium">➕ Buat Event Baru</div>
                    <div class="text-sm opacity-90 mt-1">Tambah workshop, seminar, atau bazar</div>
                </button>
                <button
                    class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-4 rounded-lg text-left transition-colors duration-200">
                    <div class="font-medium">➕ Daftar UMKM Baru</div>
                    <div class="text-sm opacity-90 mt-1">Registrasi profil UMKM baru</div>
                </button>
            </div>
        </div>

        <!-- Forms Section -->
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
            <!-- Form Tambah Event -->
            <livewire:admin.createevent />

            <!-- Form Tambah UMKM Profile -->
            <livewire:admin.createumkm />
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-center text-sm text-gray-500">
                © 2024 Rumahkaka.id - Platform UMKM Indonesia
            </p>
        </div>
    </footer>
</body>

</html>
