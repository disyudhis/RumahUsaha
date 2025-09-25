@extends('layouts.main')

@section('title', 'List UMKM - ' . ($business->business_name ?? 'BIZHOUSE.ID'))
@section('description', 'Daftar UMKM ' . ($business->business_name ?? 'UMKM') . ' di BIZHOUSE.ID')

@section('content')
  <livewire:main.list-umkm
            :show-all="true"
            :limit="4"
            :show-pagination="false"
            :show-header="false"
            header-title="PROFIL UMKM ANGGOTA TERBARU"
            :show-view-all-button="false"
        />
@endsection
