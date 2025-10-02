{{-- resources/views/main/list-event.blade.php --}}
@extends('layouts.main')

@section('title', 'Berita & Kegiatan - BIZHOUSE.id')
@section('description', 'Jelajahi berbagai kegiatan, pelatihan, dan berita terkini dari komunitas UMKM BIZHOUSE.id')

@section('content')
    <livewire:main.list-event :category="$category ?? null" :showCategoryFilter="false" />
@endsection
