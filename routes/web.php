<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Guru\DashboardGuruController;
use App\Http\Controllers\Ortu\AbsensiOrtuController;
use App\Http\Controllers\Ortu\DashboardOrtuController;
use App\Http\Controllers\Ortu\NilaiOrtuController;
use App\Http\Controllers\Ortu\PembayaranOrtuController;
use App\Http\Controllers\Ortu\TugasOrtuController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect('/login'));

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── GURU ─────────────────────────────────────────────
    Route::prefix('guru')->name('guru.')->middleware('role:admin,guru')->group(function () {
        Route::get('/dashboard', [DashboardGuruController::class, 'index'])->name('dashboard');
    });

    // ── ORANG TUA ─────────────────────────────────────────
    Route::prefix('ortu')->name('ortu.')->middleware('role:ortu')->group(function () {
        Route::get('/dashboard', [DashboardOrtuController::class, 'index'])->name('dashboard');
        Route::get('/absensi', [AbsensiOrtuController::class, 'index'])->name('absensi.index');
         
        Route::prefix('spp')->name('spp.')->group(function () {
            Route::get('/',       [PembayaranOrtuController::class, 'index'])->name('index');
            Route::get('/bayar',  [PembayaranOrtuController::class, 'create'])->name('create');
            Route::post('/bayar', [PembayaranOrtuController::class, 'store'])->name('store');
        });
        Route::middleware('spp.gate')->group(function () {
            Route::get('/tugas', [TugasOrtuController::class, 'index'])->name('tugas.index');
            Route::get('/tugas/{tugas}', [TugasOrtuController::class, 'show'])->name('tugas.show');
            Route::get('/nilai', [NilaiOrtuController::class, 'index'])->name('nilai.index');
        });
    });

});

require __DIR__.'/auth.php';