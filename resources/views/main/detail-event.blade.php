{{-- resources/views/business/show.blade.php --}}
@extends('layouts.main')

@section('title', 'Detail Event - ' . ($business->business_name ?? 'BIZHOUSE.id'))
@section('description', 'Detail Event ' . ($business->business_name ?? 'UMKM') . ' di BIZHOUSE.id')

@section('content')
    <livewire:main.detail-event :slug="$slug" />
@endsection

