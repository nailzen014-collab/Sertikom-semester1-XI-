<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ValidasiTiketRequest;
use App\Models\Pesanan;
use App\Models\Tiket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ValidasiTiketController extends Controller
{
    public function show(): View
    {
        $tiket = session('tiket_validasi_id')
            ? Tiket::with(['pesanan.tiketPaket', 'peserta'])
                ->find(session('tiket_validasi_id'))
            : null;

        return view('admin.tiket.validasi', [
            'tiket' => $tiket,
        ]);
    }

    public function store(ValidasiTiketRequest $request): RedirectResponse
    {
        $tiket = DB::transaction(function () use ($request): Tiket {
            $tiket = Tiket::with('pesanan')
                ->where('kode_tiket', mb_strtoupper(trim($request->validated('kode_tiket'))))
                ->lockForUpdate()
                ->first();

            if (! $tiket) {
                throw ValidationException::withMessages([
                    'kode_tiket' => 'Kode tiket tidak ditemukan.',
                ]);
            }

            if ($tiket->pesanan->status !== Pesanan::STATUS_LUNAS) {
                throw ValidationException::withMessages([
                    'kode_tiket' => 'Tiket belum aktif karena pembayaran belum terverifikasi.',
                ]);
            }

            if (! $tiket->pesanan->tanggal_kunjungan->isToday()) {
                throw ValidationException::withMessages([
                    'kode_tiket' => 'Tiket hanya berlaku pada tanggal kunjungan yang tertera.',
                ]);
            }

            if ($tiket->status !== Tiket::STATUS_AKTIF) {
                throw ValidationException::withMessages([
                    'kode_tiket' => 'Tiket ini sudah pernah digunakan.',
                ]);
            }

            $tiket->update([
                'status' => Tiket::STATUS_DIPAKAI,
                'dipakai_pada' => now(),
                'divalidasi_oleh' => $request->user()->id,
            ]);

            return $tiket;
        });

        return redirect()->to(route('admin.dashboard').'#validasi-tiket')
            ->with('tiket_validasi_id', $tiket->id)
            ->with('message', 'Tiket valid. Statusnya sudah ditandai dipakai.');
    }
}
