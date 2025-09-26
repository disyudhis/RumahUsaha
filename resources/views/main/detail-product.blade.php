{{-- resources/views/business/show.blade.php --}}
@extends('layouts.main')

@section('title', 'Detail Product - ' . ($business->business_name ?? 'BIZHOUSE.ID'))
@section('description', 'Detail produk ' . ($business->business_name ?? 'PRODUCT') . ' di BIZHOUSE.ID')

@section('content')
    <livewire:main.detail-product :slug="$slug" />
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
