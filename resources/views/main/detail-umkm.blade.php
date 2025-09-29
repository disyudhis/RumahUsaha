{{-- resources/views/business/show.blade.php --}}
@extends('layouts.main')

@section('title', 'Detail UMKM - ' . ($business->business_name ?? 'BIZHOUSE.id'))
@section('description', 'Detail profil dan produk ' . ($business->business_name ?? 'UMKM') . ' di BIZHOUSE.id')

@section('content')
    <livewire:main.detail-umkm :slug="$slug" />
@endsection

@push('styles')
    <style>
        .line-clamp-2 {
            overflow: hidden;
            display: -webkit-box;

            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
    </style>
@endpush
