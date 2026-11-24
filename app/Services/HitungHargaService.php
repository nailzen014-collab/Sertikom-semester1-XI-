<?php

namespace App\Services;

use App\Models\Diskon;
use App\Models\TiketPaket;
use Carbon\CarbonInterface;

class HitungHargaService
{
    /**
     * @return array{harga_satuan: int, potongan_diskon: int, total_bayar: int}
     */
    public function hitung(TiketPaket $tiketPaket, int $jumlahTiket, ?Diskon $diskon = null): array
    {
        $hargaSatuan = $tiketPaket->harga;
        $subtotal = $hargaSatuan * $jumlahTiket;
        $potongan = $diskon ? $this->potongan($hargaSatuan, $jumlahTiket, $diskon) : 0;
        $potongan = min($potongan, $subtotal);

        return [
            'harga_satuan' => $hargaSatuan,
            'potongan_diskon' => $potongan,
            'total_bayar' => $subtotal - $potongan,
        ];
    }

    public function diskonTerbaik(TiketPaket $tiketPaket, int $jumlahTiket, CarbonInterface $tanggal): ?Diskon
    {
        return Diskon::berlakuPada($tanggal)
            ->untukPaket($tiketPaket->id)
            ->get()
            ->sortByDesc(fn (Diskon $diskon) => $this->potongan($tiketPaket->harga, $jumlahTiket, $diskon))
            ->first();
    }

    private function potongan(int $hargaSatuan, int $jumlahTiket, Diskon $diskon): int
    {
        $perTiket = $diskon->tipe === Diskon::TIPE_PERSEN
            ? (int) round($hargaSatuan * $diskon->nilai / 100)
            : $diskon->nilai;

        return max(0, $perTiket) * $jumlahTiket;
    }
}
