@extends('layouts.main')

@section('title', 'Detail UMKM | Admin - BIZHOUSE.ID')
@section('description', 'Dashboard admin untuk mengelola platform UMKM')

@section('content')
    <livewire:admin.detail-event :slug="$slug" />
@endsection
