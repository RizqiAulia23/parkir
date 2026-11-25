<?php

use App\Http\Controllers\Admin\AreaParkirController;
use App\Http\Controllers\Admin\KendaraanController;
use App\Http\Controllers\Admin\LogAktivitasController;
use App\Http\Controllers\Admin\TarifController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Owner\LaporanController;
use App\Http\Controllers\Petugas\TransaksiController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware('auth')->group(function () {
    Route::redirect('/', '/dashboard');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    /*
    |--------------------------------------------------------------------------
    | Admin (superuser)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class)->except('show');

        Route::resource('tarif', TarifController::class)
            ->except('show')
            ->parameters(['tarif' => 'tarif']);

        Route::resource('area', AreaParkirController::class)
            ->except('show')
            ->parameters(['area' => 'area']);

        Route::resource('kendaraan', KendaraanController::class)->except('show');

        Route::get('log', [LogAktivitasController::class, 'index'])->name('log.index');
    });

    /*
    |--------------------------------------------------------------------------
    | Transaksi parkir (petugas & admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,petugas')->prefix('transaksi')->name('transaksi.')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('index');

        Route::get('masuk', [TransaksiController::class, 'create'])->name('masuk.create');
        Route::post('masuk', [TransaksiController::class, 'store'])->name('masuk.store');

        Route::get('{transaksi}/keluar', [TransaksiController::class, 'keluarForm'])->name('keluar');
        Route::post('{transaksi}/keluar', [TransaksiController::class, 'keluarStore'])->name('keluar.store');

        Route::get('{transaksi}/struk', [TransaksiController::class, 'struk'])->name('struk');
    });

    /*
    |--------------------------------------------------------------------------
    | Laporan (owner & admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin,owner')->prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('cetak', [LaporanController::class, 'cetak'])->name('cetak');
    });
});
