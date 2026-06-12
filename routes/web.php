<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\GrafikMahasiswaController;
// route default untuk halaman utama
// Route::get('/', [GrafikController::class, 'index']);
// Route::get('/get-chart-kecamatan', [GrafikController::class, 'getChartKecamatan']);
// Route::get('/praktikum-eda', [GrafikMahasiswaController::class, 'index'])->name('praktikum.eda');

Route::get('/eda', [GrafikMahasiswaController::class, 'index']);