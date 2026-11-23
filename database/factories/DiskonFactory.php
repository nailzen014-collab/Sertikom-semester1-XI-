<?php

namespace Database\Factories;

use App\Models\Diskon;
use App\Models\TiketPaket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Diskon>
 */
class DiskonFactory extends Factory
{
    protected $model = Diskon::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'admin_id' => User::factory()->admin(),
            'nama' => 'Promo '.fake()->word(),
            'kategori' => TiketPaket::KATEGORI_UMUM,
            'tipe' => Diskon::TIPE_PERSEN,
            'nilai' => 10,
            'tanggal_mulai' => now()->subDay()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'status_aktif' => true,
        ];
    }
}
