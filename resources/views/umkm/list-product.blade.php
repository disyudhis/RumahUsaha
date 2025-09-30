{{-- resources/views/umkm/main.blade.php --}}
@extends('layouts.main')

@section('title', 'List Product - BIZHOUSE.ID')
@section('description', 'Kelola produk anda di platform BIZHOUSE.ID')

@section('content')
<div class="p-4">
    <livewire:umkm.list-product :view-mode="'full'" :show-header="true" :show-filters="true" :show-pagination="true" />
</div>
@endsection
