@extends('layouts.main')

@section('title', 'Admin Dashboard - RumahUsaha.id')
@section('description', 'Dashboard admin untuk mengelola platform UMKM')

@section('content')
    <div class="max-w-7xl mx-auto p-4 space-y-6">
        {{-- Main Container for List UMKM --}}
        <div id="umkm-list-container">
            <livewire:admin.list-umkm />
        </div>

        {{-- Create UMKM Form (Hidden by default) --}}
        <div id="create-umkm-form" class="hidden">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Tambah UMKM Baru</h2>
                        <p class="text-sm text-gray-600 mt-1">Daftarkan UMKM baru ke platform</p>
                    </div>
                    <button onclick="toggleCreateForm()" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
            <livewire:admin.createumkm />
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        // Toggle between list and create form
        function toggleCreateForm() {
            const createForm = document.getElementById('create-umkm-form');
            const listContainer = document.getElementById('umkm-list-container');

            if (createForm.classList.contains('hidden')) {
                // Show form, hide list
                createForm.classList.remove('hidden');
                listContainer.classList.add('hidden');
                // Scroll to top when showing form
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            } else {
                // Hide form, show list
                createForm.classList.add('hidden');
                listContainer.classList.remove('hidden');
                // Scroll to top when returning to list
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            }
        }

        // Listen for successful registration to go back to list
        document.addEventListener('DOMContentLoaded', function() {
            // Listen for Livewire events
            Livewire.on('registration-success', () => {
                // Add small delay to allow flash message to show
                setTimeout(() => {
                    toggleCreateForm(); // Go back to list view
                }, 2000);
            });
        });
    </script>
@endpush
