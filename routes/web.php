<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\PembayaranController;
use App\Http\Controllers\Guru\DashboardGuruController;
use App\Http\Controllers\Ortu\DashboardOrtuController;
use App\Http\Controllers\Ortu\PembayaranOrtuController;

Route::get('/', fn() => redirect('/login'));

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── ADMIN ────────────────────────────────────────────
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');
        Route::resource('siswa', SiswaController::class);
        Route::resource('kelas', KelasController::class);

        Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
            Route::get('/',                          [PembayaranController::class, 'index'])->name('index');
            Route::get('/{pembayaran}',              [PembayaranController::class, 'show'])->name('show');
            Route::patch('/{pembayaran}/konfirmasi', [PembayaranController::class, 'konfirmasi'])->name('konfirmasi');
            Route::patch('/{pembayaran}/tolak',      [PembayaranController::class, 'tolak'])->name('tolak');
            Route::get('/rekap/tunggakan',           [PembayaranController::class, 'tunggakan'])->name('tunggakan');
        });
    });

    // ── GURU ─────────────────────────────────────────────
    Route::prefix('guru')->name('guru.')->middleware('role:admin,guru')->group(function () {
        Route::get('/dashboard', [DashboardGuruController::class, 'index'])->name('dashboard');
    });

    // ── ORANG TUA ─────────────────────────────────────────
    Route::prefix('ortu')->name('ortu.')->middleware('role:ortu')->group(function () {
        Route::get('/dashboard', [DashboardOrtuController::class, 'index'])->name('dashboard');

        // SPP tidak kena gate — supaya bisa bayar walau terkunci
        Route::prefix('spp')->name('spp.')->group(function () {
            Route::get('/',       [PembayaranOrtuController::class, 'index'])->name('index');
            Route::get('/bayar',  [PembayaranOrtuController::class, 'create'])->name('create');
            Route::post('/bayar', [PembayaranOrtuController::class, 'store'])->name('store');
        });

        // Fitur akademik — kena SPP gate
        Route::middleware('spp.gate')->group(function () {
            // tugas, nilai, dll ditambah di sini nanti
        });
    });

});

require __DIR__.'/auth.php';