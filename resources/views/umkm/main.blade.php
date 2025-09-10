{{-- resources/views/umkm/main.blade.php --}}
@extends('layouts.main')

@section('title', 'Dashboard UMKM - RumahUsaha.id')
@section('description', 'Kelola produk dan bisnis UMKM Anda di platform RumahUsaha.id')

@section('content')
    <livewire:umkm.dashboard />
@endsection

