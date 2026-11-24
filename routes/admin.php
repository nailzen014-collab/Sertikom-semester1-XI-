<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DiskonController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PengajuanRombonganController;
use App\Http\Controllers\Admin\PesananController;
use App\Http\Controllers\Admin\TiketPaketController;
use App\Http\Controllers\Admin\ValidasiTiketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('pengajuan', [PengajuanRombonganController::class, 'index'])->name('pengajuan.index');
    Route::post('pengajuan/{pesanan}/setujui', [PengajuanRombonganController::class, 'approve'])->name('pengajuan.approve');
    Route::post('pengajuan/{pesanan}/tolak', [PengajuanRombonganController::class, 'reject'])->name('pengajuan.reject');
    Route::get('validasi-tiket', [ValidasiTiketController::class, 'show'])->name('tiket.validasi');
    Route::post('validasi-tiket', [ValidasiTiketController::class, 'store'])->name('tiket.validasi.store');

    Route::resource('tiket-paket', TiketPaketController::class)
        ->parameters(['tiket-paket' => 'tiketPaket'])
        ->except('show');

    Route::resource('diskon', DiskonController::class)->except('show');

    Route::get('pesanan', [PesananController::class, 'index'])->name('pesanan.index');
    Route::get('pesanan/{pesanan}', [PesananController::class, 'show'])->name('pesanan.show');

    Route::get('laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');
    Route::get('laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
});
