{{-- resources/views/umkm/main.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Pemilik UMKM - BIZHOUSE.ID</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-inter bg-gray-50">
    {{-- Header Navigation --}}
    <livewire:layout.main-navigation />

    {{-- Main Container --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{-- Welcome & Getting Started Section --}}
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-900 mb-2">🏪 Selamat Datang di Dashboard UMKM!</h1>
                <p class="text-gray-600">Daftarkan produk Anda untuk ditampilkan di halaman utama BIZHOUSE.ID</p>
            </div>

            {{-- Progress Steps --}}
            {{-- <div class="flex justify-center items-center space-x-4 mb-6">
                <div class="flex items-center">
                    <div
                        class="w-8 h-8 bg-blue-500 text-white rounded-full flex items-center justify-center text-sm font-medium">
                        1</div>
                    <div class="ml-2 text-sm font-medium text-blue-600">Daftar Produk</div>
                </div>
                <div class="w-8 h-0.5 bg-gray-300"></div>
                <div class="flex items-center">
                    <div
                        class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">
                        2</div>
                    <div class="ml-2 text-sm text-gray-500">Kelola Pesanan</div>
                </div>
                <div class="w-8 h-0.5 bg-gray-300"></div>
                <div class="flex items-center">
                    <div
                        class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center text-sm font-medium">
                        3</div>
                    <div class="ml-2 text-sm text-gray-500">Promosi & Marketing</div>
                </div>
            </div> --}}
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-6">
            {{-- Add Product Form - Takes 2 columns --}}
            <livewire:umkm.create-product />

            {{-- Right Sidebar - Tips & Preview --}}
            {{-- <div class="space-y-6"> --}}
                {{-- Tips Card --}}
                {{-- <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-green-800 mb-3 flex items-center">
                        <span class="mr-2">💡</span>
                        Tips Sukses
                    </h3>
                    <div class="space-y-3 text-sm text-green-700">
                        <div class="flex items-start">
                            <span class="mr-2">✅</span>
                            <span>Gunakan foto produk yang jelas dan menarik</span>
                        </div>
                        <div class="flex items-start">
                            <span class="mr-2">✅</span>
                            <span>Tulis deskripsi yang detail dan jujur</span>
                        </div>
                        <div class="flex items-start">
                            <span class="mr-2">✅</span>
                            <span>Cantumkan cara pemesanan yang mudah</span>
                        </div>
                        <div class="flex items-start">
                            <span class="mr-2">✅</span>
                            <span>Harga yang kompetitif dan wajar</span>
                        </div>
                    </div>
                </div> --}}


                {{-- <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <span class="mr-2">👁️</span>
                        Preview di Dashboard Umum
                    </h3>


                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <div class="bg-gray-100 h-32 flex items-center justify-center">
                            <span class="text-gray-400 text-sm">Foto Produk Anda</span>
                        </div>
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Nama Produk</h4>
                            <p class="text-blue-600 font-semibold mb-3">Rp 0</p>
                            <button class="w-full bg-blue-500 text-white py-2 rounded text-sm">
                                Detail Produk
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                        <p class="text-xs text-blue-700">
                            <span class="font-medium">Info:</span> Produk akan muncul di halaman utama setelah
                            disetujui admin (maksimal 24 jam)
                        </p>
                    </div>
                </div> --}}

                {{-- Next Steps Card --}}
                {{-- <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-yellow-800 mb-3 flex items-center">
                        <span class="mr-2">📋</span>
                        Langkah Selanjutnya
                    </h3>
                    <div class="space-y-2 text-sm text-yellow-700">
                        <div class="flex items-center">
                            <span
                                class="w-4 h-4 bg-yellow-300 rounded-full mr-2 text-xs flex items-center justify-center text-yellow-800">2</span>
                            <span>Kelola pesanan yang masuk</span>
                        </div>
                        <div class="flex items-center">
                            <span
                                class="w-4 h-4 bg-yellow-300 rounded-full mr-2 text-xs flex items-center justify-center text-yellow-800">3</span>
                            <span>Gunakan tools marketing</span>
                        </div>
                        <div class="flex items-center">
                            <span
                                class="w-4 h-4 bg-yellow-300 rounded-full mr-2 text-xs flex items-center justify-center text-yellow-800">4</span>
                            <span>Analisis performa penjualan</span>
                        </div>
                    </div>
                </div> --}}
                {{-- </div> --}}
        </div>

        {{-- Empty State Message --}}
        <div class="mt-8 text-center">
            <div class="text-gray-400 mb-2">
                <span class="text-4xl">📦</span>
            </div>
            <p class="text-gray-500 text-sm">
                Belum ada produk yang terdaftar. Mulai dengan menambahkan produk pertama Anda!
            </p>
        </div>
    </div>
</body>

</html>