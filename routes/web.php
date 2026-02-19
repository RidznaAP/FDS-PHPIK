<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerencanaanController;
use App\Http\Controllers\PelaksanaanController;
use App\Http\Controllers\HomeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman awal: redirect ke login jika belum login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes (Login, Register, Logout, dll)
Auth::routes();

// ========================================
// SEMUA ROUTE DI BAWAH INI BUTUH LOGIN
// ========================================
Route::middleware('auth')->group(function () {

    // Dashboard (setelah login)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // --- Modul Perencanaan ---
    // Semua role bisa lihat daftar perencanaan
    Route::get('/perencanaan', [PerencanaanController::class, 'index'])->name('perencanaan.index');

    // Hanya UPT yang bisa tambah perencanaan
    Route::middleware('role:upt')->group(function () {
        Route::get('/perencanaan/tambah', [PerencanaanController::class, 'create'])->name('perencanaan.create');
        Route::post('/perencanaan/simpan', [PerencanaanController::class, 'store'])->name('perencanaan.store');
    });

    // --- Modul Pelaksanaan ---
    // Semua role bisa lihat daftar pelaksanaan
    Route::get('/pelaksanaan', [PelaksanaanController::class, 'index'])->name('pelaksanaan.index');

    // Hanya UPT yang bisa input pelaksanaan
    Route::middleware('role:upt')->group(function () {
        Route::get('/pelaksanaan/tambah/{id}', [PelaksanaanController::class, 'create'])->name('pelaksanaan.create');
        Route::post('/pelaksanaan/simpan', [PelaksanaanController::class, 'store'])->name('pelaksanaan.store');
    });
});
