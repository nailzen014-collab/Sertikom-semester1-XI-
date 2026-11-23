<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\TanggalTutup;
use App\Models\TiketPaket;
use Carbon\CarbonInterface;

class KuotaService
{
    public function tanggalTutup(CarbonInterface|string $tanggal): ?TanggalTutup
    {
        return TanggalTutup::whereDate('tanggal', $tanggal)->first();
    }

    public function terpakai(TiketPaket $tiketPaket, CarbonInterface|string $tanggal): int
    {
        return (int) Pesanan::where('tiket_paket_id', $tiketPaket->id)
            ->whereDate('tanggal_kunjungan', $tanggal)
            ->lunas()
            ->sum('jumlah_tiket');
    }

    public function sisa(TiketPaket $tiketPaket, CarbonInterface|string $tanggal): int
    {
        return max(0, $tiketPaket->kuota_harian - $this->terpakai($tiketPaket, $tanggal));
    }

    public function cukup(TiketPaket $tiketPaket, CarbonInterface|string $tanggal, int $jumlahTiket): bool
    {
        return $this->sisa($tiketPaket, $tanggal) >= $jumlahTiket;
    }
}
