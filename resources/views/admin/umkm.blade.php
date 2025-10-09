@extends('layouts.main')

@section('title', 'Admin Dashboard - BIZHOUSE.ID')
@section('description', 'Dashboard admin untuk mengelola platform UMKM')

@section('content')
    <livewire:admin.list-umkm />
@endsection
