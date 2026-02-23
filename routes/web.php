<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerencanaanController;
use App\Http\Controllers\PelaksanaanController;
use App\Http\Controllers\LaboratoriumController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;

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

    // --- Profil User ---
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profil/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // --- Modul Perencanaan ---
    Route::get('/perencanaan', [PerencanaanController::class, 'index'])->name('perencanaan.index');
    // Hanya UPT yang bisa tambah perencanaan & ajukan validasi
    Route::middleware('role:bkhit')->group(function () {
        Route::get('/perencanaan/tambah', [PerencanaanController::class, 'create'])->name('perencanaan.create');
        Route::post('/perencanaan/simpan', [PerencanaanController::class, 'store'])->name('perencanaan.store');
        Route::post('/perencanaan/submit/{id}', [PerencanaanController::class, 'submit'])->name('perencanaan.submit');
        // #3 Edit & #4 Hapus (Draft only)
        Route::get('/perencanaan/edit/{id}', [PerencanaanController::class, 'edit'])->name('perencanaan.edit');
        Route::put('/perencanaan/update/{id}', [PerencanaanController::class, 'update'])->name('perencanaan.update');
        Route::delete('/perencanaan/hapus/{id}', [PerencanaanController::class, 'destroy'])->name('perencanaan.destroy');
    });

    // Validasi (approve) oleh BBKHIT/Pusat
    Route::middleware('role:bbkhit,pusat')->group(function () {
        Route::post('/perencanaan/approve/{id}', [PerencanaanController::class, 'approve'])->name('perencanaan.approve');
    });

    // --- Modul Pelaksanaan ---
    Route::get('/pelaksanaan', [PelaksanaanController::class, 'index'])->name('pelaksanaan.index');
    Route::middleware('role:bkhit')->group(function () {
        Route::get('/pelaksanaan/tambah/{id}', [PelaksanaanController::class, 'create'])->name('pelaksanaan.create');
        Route::post('/pelaksanaan/simpan', [PelaksanaanController::class, 'store'])->name('pelaksanaan.store');
    });

    // --- Modul Laboratorium ---
    // Semua bisa lihat, tapi input dibatasi
    Route::get('/laboratorium', [LaboratoriumController::class, 'index'])->name('laboratorium.index');
    Route::middleware('role:bkhit,bbkhit')->group(function () {
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

    // --- Manajemen Pengguna (hanya Pusat) ---
    Route::middleware('role:pusat')->group(function () {
        Route::get('/pengguna', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/pengguna/buat', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/pengguna/simpan', [UserManagementController::class, 'store'])->name('users.store');
        Route::delete('/pengguna/hapus/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::put('/pengguna/reset-password/{id}', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        // #5: Edit Pengguna
        Route::get('/pengguna/edit/{id}', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/pengguna/update/{id}', [UserManagementController::class, 'update'])->name('users.update');
    });
});
