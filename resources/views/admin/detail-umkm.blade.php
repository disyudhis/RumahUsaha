@extends('layouts.main')

@section('title', 'Detail UMKM | Admin - RumahUsaha.id')
@section('description', 'Dashboard admin untuk mengelola platform UMKM')

@section('content')
    <livewire:admin.detail-umkm :id="$id" />
@endsection
