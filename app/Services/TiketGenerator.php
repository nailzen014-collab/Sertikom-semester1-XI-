<?php

namespace App\Services;

use App\Models\PengajuanRombongan;
use App\Models\Pesanan;
use App\Models\Tiket;
use App\Models\TiketPaket;
use Illuminate\Support\Str;

class TiketGenerator
{
    public function kodePesanan(): string
    {
        do {
            $kode = 'TNP-'.now()->format('Ymd').'-'.strtoupper(Str::random(6));
        } while (Pesanan::where('kode_pesanan', $kode)->exists());

        return $kode;
    }

    public function kodeTiket(): string
    {
        do {
            $kode = 'TKT-'.strtoupper(Str::random(10));
        } while (Tiket::where('kode_tiket', $kode)->exists());

        return $kode;
    }

    public function buatUntukPesanan(Pesanan $pesanan): void
    {
        if ($pesanan->tiket()->exists()) {
            return;
        }

        if ($pesanan->kategori === TiketPaket::KATEGORI_ROMBONGAN) {
            $pesanan->loadMissing(['pengajuanRombongan', 'pesertaRombongan']);

            if ($pesanan->pengajuanRombongan?->mode_tiket === PengajuanRombongan::MODE_PER_ROMBONGAN) {
                $pesanan->tiket()->create([
                    'kode_tiket' => $this->kodeTiket(),
                    'jenis' => Tiket::JENIS_ROMBONGAN,
                    'status' => Tiket::STATUS_AKTIF,
                ]);

                return;
            }

            foreach ($pesanan->pesertaRombongan as $peserta) {
                $pesanan->tiket()->create([
                    'peserta_id' => $peserta->id,
                    'kode_tiket' => $this->kodeTiket(),
                    'jenis' => Tiket::JENIS_INDIVIDU,
                    'status' => Tiket::STATUS_AKTIF,
                ]);
            }

            return;
        }

        for ($i = 0; $i < $pesanan->jumlah_tiket; $i++) {
            $pesanan->tiket()->create([
                'kode_tiket' => $this->kodeTiket(),
                'jenis' => Tiket::JENIS_INDIVIDU,
                'status' => Tiket::STATUS_AKTIF,
            ]);
        }
    }
}
