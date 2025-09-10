{{-- resources/views/business/show.blade.php --}}
@extends('layouts.main')

@section('title', 'Detail UMKM - ' . ($business->business_name ?? 'RumahUsaha.id'))
@section('description', 'Detail profil dan produk ' . ($business->business_name ?? 'UMKM') . ' di RumahUsaha.id')

@section('content')
    <livewire:main.detail-umkm :id="$id" />
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
