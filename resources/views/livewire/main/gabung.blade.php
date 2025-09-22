<?php

use Livewire\Volt\Component;

new class extends Component {
    public function openForm()
    {
        return $this->redirect(route('formulir-pendaftaran.index'), navigate: true);
    }
}; ?>

<div>
    <div class="relative z-10">
        {{-- Main Header --}}
        <div class="mb-10">
            <div
                class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-6 backdrop-blur-sm">
                <span class="text-3xl" aria-hidden="true">🤝</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-aleo font-bold mb-4">
                GABUNG SEBAGAI ANGGOTA
            </h2>
            <p class="text-lg text-primary-100 mb-8 max-w-3xl mx-auto leading-relaxed">
                Bergabunglah dengan <strong class="text-white">BizHouse.id</strong>, platform digital terdepan untuk UMKM
                rumahan!
                Kembangkan usaha Anda dan raih pasar yang lebih luas bersama komunitas yang solid.
            </p>
        </div>

        {{-- Website Purpose & Description --}}
        <div class="bg-white bg-opacity-10 rounded-2xl p-6 md:p-8 mb-10 backdrop-blur-sm">
            <h3 class="text-2xl font-bold text-white mb-6 text-center">Tujuan Platform BizHouse.id</h3>
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white bg-opacity-15 rounded-xl p-6 text-center">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-accent-400 to-accent-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-3">Profil Usaha & Produk</h4>
                    <p class="text-primary-100 text-sm leading-relaxed">
                        Menyediakan profil usaha dan katalog produk digital yang profesional untuk setiap anggota
                        komunitas.
                    </p>
                </div>

                <div class="bg-white bg-opacity-15 rounded-xl p-6 text-center">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-secondary-500 to-secondary-700 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M7 4V2C7 1.45 7.45 1 8 1S9 1.45 9 2V4H15V2C15 1.45 15.45 1 16 1S17 1.45 17 2V4H20C21.1 4 22 4.9 22 6V20C22 21.1 21.1 22 20 22H4C2.9 22 2 21.1 2 20V6C2 4.9 2.9 4 4 4H7Z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-3">Media Informasi & Promosi</h4>
                    <p class="text-primary-100 text-sm leading-relaxed">
                        Memperluas jangkauan pasar dari tingkat perumahan hingga nasional melalui media digital
                        terintegrasi.
                    </p>
                </div>

                <div class="bg-white bg-opacity-15 rounded-xl p-6 text-center">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-primary-400 to-primary-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H17l-2.99 4.01V16h2v6h4zm-7.5-10.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5S11 9.17 11 10s.67 1.5 1.5 1.5z" />
                        </svg>
                    </div>
                    <h4 class="text-lg font-semibold text-white mb-3">Jejaring & Kolaborasi</h4>
                    <p class="text-primary-100 text-sm leading-relaxed">
                        Memperkuat jejaring antar pelaku usaha melalui pelatihan, pendampingan, dan pemasaran
                        kolaboratif.
                    </p>
                </div>
            </div>
        </div>

        {{-- Benefits Section - Updated --}}
        <div class="bg-white bg-opacity-10 rounded-2xl p-6 md:p-8 mb-10 backdrop-blur-sm">
            <h3 class="text-2xl md:text-3xl font-bold text-white mb-8 text-center">Manfaat Bergabung di BizHouse.id</h3>

            {{-- UMKM Rumahan Benefits --}}
            <div class="mb-8">
                <div class="flex items-center mb-6">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-accent-400 to-accent-600 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white">Bagi UMKM Rumahan</h4>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white bg-opacity-10 rounded-xl p-5 backdrop-blur-sm">
                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-accent-500 rounded-lg flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-semibold text-white mb-1">Etalase Digital Terpadu</h5>
                                <p class="text-primary-100 text-sm">Profil usaha dan produk tampil online sehingga mudah
                                    ditemukan pembeli.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-10 rounded-xl p-5 backdrop-blur-sm">
                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-accent-500 rounded-lg flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-semibold text-white mb-1">Jangkauan Pasar Luas</h5>
                                <p class="text-primary-100 text-sm">Produk lokal dapat dikenal hingga tingkat kota
                                    bahkan nasional.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-10 rounded-xl p-5 backdrop-blur-sm">
                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-accent-500 rounded-lg flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M7 4V2C7 1.45 7.45 1 8 1S9 1.45 9 2V4H15V2C15 1.45 15.45 1 16 1S17 1.45 17 2V4H20C21.1 4 22 4.9 22 6V20C22 21.1 21.1 22 20 22H4C2.9 22 2 21.1 2 20V6C2 4.9 2.9 4 4 4H7Z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-semibold text-white mb-1">Promosi & Penjualan Efisien</h5>
                                <p class="text-primary-100 text-sm">Dukungan media digital dan kampanye bersama menekan
                                    biaya promosi.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-10 rounded-xl p-5 backdrop-blur-sm">
                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-accent-500 rounded-lg flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-semibold text-white mb-1">Pelatihan & Pendampingan</h5>
                                <p class="text-primary-100 text-sm">Akses ke pelatihan bisnis, pemasaran, dan teknologi
                                    untuk peningkatan kapasitas.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Community Benefits --}}
            <div>
                <div class="flex items-center mb-6">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-secondary-500 to-secondary-700 rounded-lg flex items-center justify-center mr-3">
                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H17l-2.99 4.01V16h2v6h4zm-7.5-10.5c.83 0 1.5-.67 1.5-1.5s-.67-1.5-1.5-1.5S11 9.17 11 10s.67 1.5 1.5 1.5z" />
                        </svg>
                    </div>
                    <h4 class="text-xl font-bold text-white">Bagi Komunitas UMKM</h4>
                </div>
                <div class="grid md:grid-cols-2 gap-4">
                    <div class="bg-white bg-opacity-10 rounded-xl p-5 backdrop-blur-sm">
                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-secondary-600 rounded-lg flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M16.5 3c-1.74 0-3.41.81-4.5 2.09C10.91 3.81 9.24 3 7.5 3 4.42 3 2 5.42 2 8.5c0 3.78 3.4 6.86 8.55 11.54L12 21.35l1.45-1.32C18.6 15.36 22 12.28 22 8.5 22 5.42 19.58 3 16.5 3z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-semibold text-white mb-1">Jejaring Kolaboratif</h5>
                                <p class="text-primary-100 text-sm">Memperkuat hubungan antaranggota melalui pemasaran
                                    dan acara bersama.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-10 rounded-xl p-5 backdrop-blur-sm">
                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-secondary-600 rounded-lg flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-semibold text-white mb-1">Pemberdayaan Ekonomi Lokal</h5>
                                <p class="text-primary-100 text-sm">Mendorong pertumbuhan ekonomi berbasis komunitas
                                    yang berkelanjutan.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-10 rounded-xl p-5 backdrop-blur-sm">
                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-secondary-600 rounded-lg flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-semibold text-white mb-1">Eksposur Kolektif</h5>
                                <p class="text-primary-100 text-sm">Komunitas mendapat citra positif sebagai pusat
                                    produk lokal berkualitas.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white bg-opacity-10 rounded-xl p-5 backdrop-blur-sm">
                        <div class="flex items-start">
                            <div
                                class="w-8 h-8 bg-secondary-600 rounded-lg flex items-center justify-center mr-3 mt-0.5 flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M9.5 3A6.5 6.5 0 0 1 16 9.5c0 1.61-.59 3.09-1.56 4.23l.27.27h.79l5 5-1.5 1.5-5-5v-.79l-.27-.27A6.516 6.516 0 0 1 9.5 16 6.5 6.5 0 0 1 3 9.5 6.5 6.5 0 0 1 9.5 3m0 2C7 5 5 7 5 9.5S7 14 9.5 14 14 12 14 9.5 12 5 9.5 5z" />
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-semibold text-white mb-1">Inovasi & Pertukaran Ide</h5>
                                <p class="text-primary-100 text-sm">Wadah berbagi pengalaman, inovasi produk, dan
                                    strategi bisnis.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Call to Action Buttons --}}
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <button wire:click='openForm' type="button"
                class="inline-flex items-center bg-white text-primary-700 px-8 py-4 rounded-lg hover:bg-gray-50 font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <svg class="w-6 h-6 mr-3" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Daftar Sekarang
            </button>

            {{-- <button type="button"
                class="inline-flex items-center border-2 border-white text-white px-8 py-4 rounded-lg hover:bg-white hover:text-primary-700 font-medium transition-all duration-300"
                onclick="document.querySelector('.bg-white.bg-opacity-10:nth-child(2)').scrollIntoView({behavior: 'smooth'})">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Pelajari Lebih Lanjut
            </button> --}}
        </div>
    </div>
</div>
