<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerencanaanController;
use App\Http\Controllers\PelaksanaanController;
use App\Http\Controllers\LaboratoriumController;
use App\Http\Controllers\EvaluasiController;
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
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // --- Modul Perencanaan ---
    Route::get('/perencanaan', [PerencanaanController::class, 'index'])->name('perencanaan.index');
    // Hanya UPT yang bisa tambah perencanaan & ajukan validasi
    Route::middleware('role:upt')->group(function () {
        Route::get('/perencanaan/tambah', [PerencanaanController::class, 'create'])->name('perencanaan.create');
        Route::post('/perencanaan/simpan', [PerencanaanController::class, 'store'])->name('perencanaan.store');
        Route::post('/perencanaan/submit/{id}', [PerencanaanController::class, 'submit'])->name('perencanaan.submit');
    });

    // Validasi (approve) oleh BBKHIT/Pusat
    Route::middleware('role:bbkhit,pusat')->group(function () {
        Route::post('/perencanaan/approve/{id}', [PerencanaanController::class, 'approve'])->name('perencanaan.approve');
    });

    // --- Modul Pelaksanaan ---
    Route::get('/pelaksanaan', [PelaksanaanController::class, 'index'])->name('pelaksanaan.index');
    Route::middleware('role:upt')->group(function () {
        Route::get('/pelaksanaan/tambah/{id}', [PelaksanaanController::class, 'create'])->name('pelaksanaan.create');
        Route::post('/pelaksanaan/simpan', [PelaksanaanController::class, 'store'])->name('pelaksanaan.store');
    });

    // --- Modul Laboratorium ---
    // Semua bisa lihat, tapi input dibatasi
    Route::get('/laboratorium', [LaboratoriumController::class, 'index'])->name('laboratorium.index');
    Route::middleware('role:upt,bbkhit')->group(function () {
        Route::get('/laboratorium/input/{id}', [LaboratoriumController::class, 'create'])->name('laboratorium.create');
        Route::post('/laboratorium/simpan', [LaboratoriumController::class, 'store'])->name('laboratorium.store');
    });

    // --- Modul Evaluasi ---
    // Semua bisa lihat hasil evaluasi
    Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi.index');
    // Hanya BBKHIT & Pusat yang boleh evaluasi status akhir
    Route::middleware('role:bbkhit,pusat')->group(function () {
        Route::get('/evaluasi/input/{id}', [EvaluasiController::class, 'create'])->name('evaluasi.create');
        Route::post('/evaluasi/simpan', [EvaluasiController::class, 'store'])->name('evaluasi.store');
    });
    // --- Modul Peta GIS ---
    Route::get('/peta', [\App\Http\Controllers\PetaController::class, 'index'])->name('peta.index');

    // --- Modul Laporan & Ekspor ---
    Route::get('/laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export/perencanaan', [\App\Http\Controllers\LaporanController::class, 'exportPerencanaan'])->name('laporan.export.perencanaan');
    Route::get('/laporan/export/pelaksanaan', [\App\Http\Controllers\LaporanController::class, 'exportPelaksanaan'])->name('laporan.export.pelaksanaan');
});
