{{-- resources/views/umkm/main.blade.php --}}
@extends('layouts.main')

@section('title', 'Detail Produk - BIZHOUSE.ID')
@section('description', 'Kelola produk dari usaha anda')

@section('content')
    <livewire:umkm.detail-product :slug="$slug" />
@endsection

