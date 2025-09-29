{{-- resources/views/umkm/main.blade.php --}}
@extends('layouts.main')

@section('title', 'Dashboard UMKM - BIZHOUSE.ID')
@section('description', 'Kelola produk dan bisnis UMKM Anda di platform BIZHOUSE.ID')

@section('content')
    <livewire:umkm.dashboard />
@endsection

