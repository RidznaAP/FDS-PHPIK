<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerencanaanController;
use App\Http\Controllers\PelaksanaanController;
use App\Http\Controllers\LaboratoriumController;
use App\Http\Controllers\EvaluasiController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\DokumenSeminarController;
use App\Http\Controllers\NotifikasiController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman awal: redirect ke login jika belum login
Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes (Login, Logout — Register dinonaktifkan, akun dibuat oleh Pusat)
Auth::routes(['register' => false]);

// Redirect /register ke login dengan pesan informasi
Route::get('/register', function () {
    return redirect()->route('login')->with('info', 'Pendaftaran akun mandiri tidak diizinkan. Hubungi Admin Pusat untuk pembuatan akun.');
})->name('register');
Route::post('/register', function () {
    return redirect()->route('login')->with('info', 'Pendaftaran akun mandiri tidak diizinkan.');
});

// ========================================
// SEMUA ROUTE DI BAWAH INI BUTUH LOGIN
// ========================================
Route::middleware('auth')->group(function () {

    // Dashboard (setelah login)
   Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // --- Notifikasi ---
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::get('/notifikasi/{id}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');
    Route::post('/notifikasi/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('notifikasi.baca-semua');
    Route::get('/notifikasi/jumlah', [NotifikasiController::class, 'jumlah'])->name('notifikasi.jumlah');
    Route::delete('/notifikasi/{id}/hapus', [NotifikasiController::class, 'hapus'])->name('notifikasi.hapus');

    // --- Profil User ---
    Route::get('/profil', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profil/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

    // --- Modul Perencanaan ---
    Route::get('/perencanaan', [PerencanaanController::class, 'index'])->name('perencanaan.index');
    Route::get('/perencanaan/export', [PerencanaanController::class, 'export'])->name('perencanaan.export');
    Route::get('/perencanaan/template', [PerencanaanController::class, 'downloadTemplate'])->name('perencanaan.template');
    Route::post('/perencanaan/bulk-delete', [PerencanaanController::class, 'bulkDelete'])->name('perencanaan.bulk-delete');
    
    // Modul Perencanaan: BKHIT & BBKHIT bisa tambah/edit
    Route::middleware('role:bkhit,bbkhit,pusat')->group(function () {
        Route::post('/perencanaan/import', [PerencanaanController::class, 'import'])->name('perencanaan.import');
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
        Route::post('/perencanaan/reject/{id}', [PerencanaanController::class, 'reject'])->name('perencanaan.reject');

        // Penetapan Evaluasi (Warna Map)
        Route::get('/perencanaan/{id}/evaluasi', [EvaluasiController::class, 'create'])->name('evaluasi.create');
        Route::post('/perencanaan/evaluasi', [EvaluasiController::class, 'store'])->name('evaluasi.store');
    });

    // Daftar Evaluasi Penetapan (semua role)
    Route::get('/evaluasi-data', [EvaluasiController::class, 'index'])->name('evaluasi.data.index');

    // Detail Perencanaan (semua role bisa lihat)
    Route::get('/perencanaan/{id}', [PerencanaanController::class, 'show'])->name('perencanaan.show');

    // --- Modul Pelaksanaan ---
    Route::get('/pelaksanaan', [PelaksanaanController::class, 'index'])->name('pelaksanaan.index');
    Route::post('/pelaksanaan/bulk-delete', [PelaksanaanController::class, 'bulkDelete'])->name('pelaksanaan.bulk-delete');
    Route::delete('/pelaksanaan/hapus/{id}', [PelaksanaanController::class, 'destroy'])->name('pelaksanaan.destroy');
    // Detail Pelaksanaan (semua role bisa lihat)
    Route::get('/pelaksanaan/{id}/detail', [PelaksanaanController::class, 'show'])->name('pelaksanaan.show');
    Route::middleware('role:bkhit,bbkhit,pusat')->group(function () {
        Route::get('/pelaksanaan/tambah/{id}', [PelaksanaanController::class, 'create'])->name('pelaksanaan.create');
        Route::post('/pelaksanaan/simpan', [PelaksanaanController::class, 'store'])->name('pelaksanaan.store');
        // Edit Pelaksanaan
        Route::get('/pelaksanaan/{id}/edit', [PelaksanaanController::class, 'edit'])->name('pelaksanaan.edit');
        Route::put('/pelaksanaan/{id}/update', [PelaksanaanController::class, 'update'])->name('pelaksanaan.update');
    });

    // --- Modul Laboratorium ---
    // Semua bisa lihat, tapi input dibatasi
    Route::get('/laboratorium', [LaboratoriumController::class, 'index'])->name('laboratorium.index');
    Route::get('/laboratorium/{id}/detail', [LaboratoriumController::class, 'show'])->name('laboratorium.show');
    Route::post('/laboratorium/bulk-delete', [LaboratoriumController::class, 'bulkDelete'])->name('laboratorium.bulk-delete');
    Route::delete('/laboratorium/hapus/{id}', [LaboratoriumController::class, 'destroy'])->name('laboratorium.destroy');

    Route::middleware('role:bkhit,bbkhit,pusat')->group(function () {
        Route::get('/laboratorium/input/{id}', [LaboratoriumController::class, 'create'])->name('laboratorium.create');
        Route::post('/laboratorium/simpan', [LaboratoriumController::class, 'store'])->name('laboratorium.store');
        Route::get('/laboratorium/{id}/edit', [LaboratoriumController::class, 'edit'])->name('laboratorium.edit');
        Route::put('/laboratorium/{id}/update', [LaboratoriumController::class, 'update'])->name('laboratorium.update');
    });

    // --- Modul Pelaporan (Upload Seminar) ---
    Route::get('/pelaporan', function () {
        return redirect()->route('seminar.index', 'pelaporan');
    })->name('pelaporan.index');

    // --- Modul Evaluasi Penetapan (Daftar Evaluasi) ---
    Route::get('/evaluasi', [EvaluasiController::class, 'index'])->name('evaluasi.index');

    // --- Modul Evaluasi Seminar (Upload Dokumen) ---
    Route::get('/evaluasi-seminar', function () {
        return redirect()->route('seminar.index', 'evaluasi');
    })->name('evaluasi.seminar');

    // Seminar: route bersama untuk index, store, download & hapus
    // PENTING: route spesifik (download, hapus) harus SEBELUM {modul} wildcard
    Route::get('/seminar/download/{id}', [DokumenSeminarController::class, 'download'])->name('seminar.download');
    Route::delete('/seminar/hapus/{id}', [DokumenSeminarController::class, 'destroy'])->name('seminar.destroy');
    Route::get('/seminar/{modul}', [DokumenSeminarController::class, 'index'])->whereIn('modul', ['pelaporan', 'evaluasi'])->name('seminar.index');
    Route::post('/seminar/{modul}/upload', [DokumenSeminarController::class, 'store'])->whereIn('modul', ['pelaporan', 'evaluasi'])->name('seminar.store');
    // --- Modul Peta Pemantauan ---
    Route::middleware('role:pusat')->group(function () {
        Route::get('/peta', [\App\Http\Controllers\PetaController::class, 'index'])->name('peta.index');
    });

    // --- Modul Laporan & Ekspor ---
    Route::get('/laporan', [\App\Http\Controllers\LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export/perencanaan', [\App\Http\Controllers\LaporanController::class, 'exportPerencanaan'])->name('laporan.export.perencanaan');
    Route::get('/laporan/export/pelaksanaan', [\App\Http\Controllers\LaporanController::class, 'exportPelaksanaan'])->name('laporan.export.pelaksanaan');
    // #13 & #14: PDF print + per wilayah
    Route::get('/laporan/pdf', [\App\Http\Controllers\LaporanController::class, 'exportPdf'])->name('laporan.pdf');
    Route::get('/laporan/formulir', [\App\Http\Controllers\LaporanController::class, 'exportFormulir'])->name('laporan.formulir');

    // --- Audit Log (Pusat) ---
    Route::middleware('role:pusat')->group(function () {
        Route::get('/pengaturan/audit-log', [\App\Http\Controllers\AuditLogController::class, 'index'])->name('audit.index');
    });

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

    // --- Master Data (hanya Pusat) ---
    Route::middleware('role:pusat')->group(function () {
        // Media Pembawa Actions
        Route::get('master/media-pembawa/export', [\App\Http\Controllers\MediaPembawaController::class, 'export'])->name('master.media-pembawa.export');
        Route::get('master/media-pembawa/template', [\App\Http\Controllers\MediaPembawaController::class, 'downloadTemplate'])->name('master.media-pembawa.template');
        Route::post('master/media-pembawa/import', [\App\Http\Controllers\MediaPembawaController::class, 'import'])->name('master.media-pembawa.import');
        Route::post('master/media-pembawa/bulk-delete', [\App\Http\Controllers\MediaPembawaController::class, 'bulkDelete'])->name('master.media-pembawa.bulk-delete');

        Route::resource('master/media-pembawa', \App\Http\Controllers\MediaPembawaController::class)
            ->names('master.media-pembawa')
            ->parameters(['media-pembawa' => 'mediaPembawa']);

        // Jenis Penyakit Actions
        Route::get('master/jenis-penyakit/export', [\App\Http\Controllers\JenisPenyakitController::class, 'export'])->name('master.jenis-penyakit.export');
        Route::get('master/jenis-penyakit/template', [\App\Http\Controllers\JenisPenyakitController::class, 'downloadTemplate'])->name('master.jenis-penyakit.template');
        Route::post('master/jenis-penyakit/import', [\App\Http\Controllers\JenisPenyakitController::class, 'import'])->name('master.jenis-penyakit.import');
        Route::post('master/jenis-penyakit/bulk-delete', [\App\Http\Controllers\JenisPenyakitController::class, 'bulkDelete'])->name('master.jenis-penyakit.bulk-delete');

        Route::resource('master/jenis-penyakit', \App\Http\Controllers\JenisPenyakitController::class)
            ->names('master.jenis-penyakit')
            ->parameters(['jenis-penyakit' => 'jenisPenyakit']);
    });
});
