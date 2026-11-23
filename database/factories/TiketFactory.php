<?php

namespace Database\Factories;

use App\Models\Pesanan;
use App\Models\Tiket;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tiket>
 */
class TiketFactory extends Factory
{
    protected $model = Tiket::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pesanan_id' => Pesanan::factory()->lunas(),
            'peserta_id' => null,
            'divalidasi_oleh' => null,
            'kode_tiket' => 'TKT-'.Str::upper(Str::random(10)),
            'jenis' => Tiket::JENIS_INDIVIDU,
            'status' => Tiket::STATUS_AKTIF,
            'dipakai_pada' => null,
        ];
    }
}
