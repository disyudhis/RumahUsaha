<?php

use App\Models\Testimonial;
use App\Models\UmkmProfile;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    public UmkmProfile $umkm;
    public $testimonials;

    public function mount($id)
    {
        $this->umkm = UmkmProfile::with([
            'products' => function ($query) {
                $query->latest()->limit(4);
            },
        ])->findOrFail($id);

        $this->loadTestimonials();
    }

    public function loadTestimonials()
    {
        $this->testimonials = Testimonial::whereHas('product', function ($query) {
            $query->where('umkm_profile_id', $this->umkm->id);
        })
            ->with('product')
            ->latest()
            ->limit(3)
            ->get();
    }

    public function goBack()
    {
        return $this->redirect('/', navigate: true);
    }
}; ?>

<div class="min-h-screen bg-gray-50">
    <!-- Back Navigation -->
    <div class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <button wire:click="goBack" class="flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="text-sm font-medium">Kembali ke Daftar Usaha</span>
            </button>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Profile Header -->
                <div class="bg-white rounded-xl shadow-sm border p-8">
                    <!-- Profile Image Placeholder -->
                    <div
                        class="w-full h-48 bg-gray-100 rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center mb-6">
                        @if ($umkm->logo)
                            <img src="{{ asset('storage/' . $umkm->logo) }}" alt="{{ $umkm->business_name }}"
                                class="w-full h-full object-cover rounded-lg">
                        @else
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Foto Produk atau Foto Lapak</p>
                            </div>
                        @endif
                    </div>

                    <!-- Business Info -->
                    <div class="space-y-4">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Nama Usaha:</label>
                                <span class="text-lg font-semibold text-gray-900">{{ $umkm->business_name }}</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Pemilik:</label>
                                <span class="text-lg font-semibold text-gray-900">{{ $umkm->owner_name }}</span>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <div
                                class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-600">Lokasi:</label>
                                <span class="text-gray-900">{{ $umkm->address }}</span>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3">
                            <div
                                class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div class="flex items-center space-x-4">
                                @if ($umkm->whatsapp)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $umkm->whatsapp) }}"
                                        target="_blank" class="text-green-600 hover:text-green-700 font-medium text-sm">
                                        WA: {{ $umkm->whatsapp }}
                                    </a>
                                @endif
                                @if ($umkm->instagram)
                                    <a href="https://instagram.com/{{ ltrim($umkm->instagram, '@') }}" target="_blank"
                                        class="text-pink-600 hover:text-pink-700 font-medium text-sm">
                                        IG: {{ $umkm->instagram }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Business Description -->
                @if ($umkm->description)
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Deskripsi Usaha
                        </h3>
                        <p class="text-gray-700 leading-relaxed">{{ $umkm->description }}</p>
                    </div>
                @endif

                <!-- Product Gallery -->
                @if ($umkm->products->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 text-orange-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Galeri Produk
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach ($umkm->products as $product)
                                <div class="border rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                                    <!-- Product Image -->
                                    <div class="h-48 bg-gray-100 flex items-center justify-center">
                                        @if ($product->image)
                                            <img src="{{ Storage::url($product->image) }}"
                                                alt="{{ $product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="text-center text-gray-400">
                                                <svg class="w-12 h-12 mx-auto mb-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <p class="text-sm">+ Foto Produk {{ $loop->iteration }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Info -->
                                    <div class="p-4">
                                        <h4 class="font-semibold text-gray-900 mb-1">{{ $product->name }}</h4>
                                        <p class="text-green-600 font-bold text-lg">
                                            Harga: Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>
                                        @if ($product->description)
                                            <p class="text-gray-600 text-sm mt-2 line-clamp-2">
                                                {{ $product->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Customer Testimonials -->
                @if ($testimonials->count() > 0)
                    <div class="bg-white rounded-xl shadow-sm border p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 text-yellow-600 mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                            Testimoni Pelanggan
                        </h3>

                        <div class="space-y-4">
                            @foreach ($testimonials as $testimonial)
                                <div class="border-l-4 border-blue-500 bg-blue-50 p-4 rounded-r-lg">
                                    <div class="flex items-start justify-between mb-2">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $testimonial->customer_name }}</p>
                                            <p class="text-sm text-gray-600">
                                                {{ $testimonial->product->name ?? 'Produk' }}</p>
                                        </div>
                                        @if ($testimonial->rating)
                                            <div class="flex items-center">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $testimonial->rating ? 'text-yellow-400' : 'text-gray-300' }}"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        @endif
                                    </div>
                                    <blockquote class="text-gray-700 italic">"{{ $testimonial->comment }}"
                                    </blockquote>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Hubungi Usaha</h3>
                    <div class="space-y-3">
                        @if ($umkm->whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $umkm->whatsapp) }}"
                                target="_blank"
                                class="flex items-center justify-center w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.890-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" />
                                </svg>
                                Chat WhatsApp
                            </a>
                        @endif

                        @if ($umkm->instagram)
                            <a href="https://instagram.com/{{ ltrim($umkm->instagram, '@') }}" target="_blank"
                                class="flex items-center justify-center w-full bg-gradient-to-r from-purple-500 to-pink-500 text-white px-4 py-2 rounded-lg hover:from-purple-600 hover:to-pink-600 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.73-3.016-1.797L4.27 17.335l-1.5-1.5 2.144-1.163c-.346-.892-.346-1.884 0-2.776L2.77 10.733l1.5-1.5 1.163 2.144c.568-1.067 1.719-1.797 3.016-1.797s2.448.73 3.016 1.797l1.163-2.144 1.5 1.5-2.144 1.163c.346.892.346 1.884 0 2.776l2.144 1.163-1.5 1.5-1.163-2.144c-.568 1.067-1.719 1.797-3.016 1.797z" />
                                </svg>
                                Follow Instagram
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Business Stats -->
                <div class="bg-white rounded-xl shadow-sm border p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Statistik Usaha</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Total Produk</span>
                            <span class="font-semibold text-blue-600">{{ $umkm->products->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Testimoni</span>
                            <span class="font-semibold text-yellow-600">{{ $testimonials->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Bergabung</span>
                            <span class="font-semibold text-gray-900">{{ $umkm->created_at->format('M Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Status</span>
                            <span
                                class="px-2 py-1 rounded-full text-xs font-medium {{ $umkm->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $umkm->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
