<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PerencanaanController;
use App\Http\Controllers\PelaksanaanController;

Route::get('/perencanaan/tambah', [PerencanaanController::class, 'create'])->name('perencanaan.create');
Route::post('/perencanaan/simpan', [PerencanaanController::class, 'store'])->name('perencanaan.store');
Route::get('/perencanaan', [PerencanaanController::class, 'index'])->name('perencanaan.index');


Route::get('/pelaksanaan/tambah/{id}', [PelaksanaanController::class, 'create']);
Route::post('/pelaksanaan/simpan', [PelaksanaanController::class, 'store']);

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});