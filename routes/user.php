<?php

use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\PesananController;
use App\Http\Controllers\RiwayatController;
use App\Http\Controllers\RombonganController;
use App\Http\Controllers\TiketController;
use App\Http\Controllers\User\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'pengunjung'])->group(function () {
    Route::get('/akun', DashboardController::class)->name('user.dashboard');

    Route::get('/pesan', [PesananController::class, 'kategori'])->name('pesan.kategori');
    Route::get('/pesan/rombongan', [RombonganController::class, 'create'])->name('pesan.rombongan.create');
    Route::post('/pesan/rombongan/periksa', [RombonganController::class, 'periksa'])->name('pesan.rombongan.periksa');
    Route::get('/pesan/rombongan/peserta', [RombonganController::class, 'peserta'])->name('pesan.rombongan.peserta');
    Route::post('/pesan/rombongan/peserta', [RombonganController::class, 'simpanPeserta'])->name('pesan.rombongan.simpan-peserta');
    Route::get('/pesan/rombongan/template', [RombonganController::class, 'template'])->name('pesan.rombongan.template');

    Route::get('/pesan/{tiketPaket}', [PesananController::class, 'create'])->name('pesan.create');
    Route::post('/pesan/{tiketPaket}', [PesananController::class, 'store'])->name('pesan.store');

    Route::get('/pembayaran/{pesanan}', [PembayaranController::class, 'show'])->name('pembayaran.show');
    Route::post('/pembayaran/{pesanan}', [PembayaranController::class, 'process'])->name('pembayaran.process');
    Route::get('/pembayaran/{pesanan}/selesai', [PembayaranController::class, 'selesai'])->name('pembayaran.selesai');

    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
    Route::get('/riwayat/{pesanan}', [RiwayatController::class, 'show'])->name('riwayat.show');

    Route::get('/tiket/{tiket}', [TiketController::class, 'show'])->name('tiket.show');
    Route::get('/tiket/{tiket}/pdf', [TiketController::class, 'pdf'])->name('tiket.pdf');
});
