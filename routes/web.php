<?php

use Livewire\Volt\Volt;
use Illuminate\Support\Facades\Route;

Route::view('/', 'main.main')->name('home');
Route::view('/detail-umkm/{id}', 'main.detail-umkm')->name('business.show');
Route::view('/formulir-pendaftaran', 'main.formulir-pendaftaran')->name('formulir-pendaftaran.index');
// Route::post('/logout')
// Volt::route('/detail-umkm/{id}', 'main.detail-umkm')->name('business.show');
// Route::get('detail-umkm/{id}', fn)->name('business.show');

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::view('/', 'admin.dashboard')->name('dashboard');
            Route::view('/umkm', 'admin.umkm')->name('umkm');
            Route::view('/event', 'admin.event')->name('event');
            Route::view('/detail-umkm/{id}', 'admin.detail-umkm')->name('detail-umkm');
            // Volt::route('/umkm', 'admin.createumkm')->name('umkm');
            // Volt::route('/events', 'admin.createevent')->name('events');
        });
});
Route::middleware(['auth', 'verified', 'role:pemilik_umkm'])->group(function () {
    Route::prefix('umkm')
        ->name('umkm.')
        ->group(function () {
            Route::view('/', 'umkm.main')->name('dashboard');
            Route::view('/products', 'umkm.product')->name('products');
        });
});

// Route::view('dashboard', 'dashboard')
//     ->middleware(['auth', 'verified'])
//     ->name('dashboard');

// Route::view('profile', 'profile')
//     ->middleware(['auth'])
//     ->name('profile');

require __DIR__ . '/auth.php';
