@extends('layouts.main')

@section('title', 'List Product - ' . ($business->business_name ?? 'BIZHOUSE.ID'))
@section('description', 'Daftar Product ' . ($business->business_name ?? 'Products') . ' di BIZHOUSE.ID')

@section('content')
    <livewire:main.list-product :show-hero="false" :show-all="true" :show-pagination="true" :show-header="true"
        header-title="SEMUA PRODUK" :show-view-all-button="false" :per-page="16" />

@endsection
