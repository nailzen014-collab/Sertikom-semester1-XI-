<?php

namespace Database\Factories;

use App\Models\TiketPaket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TiketPaket>
 */
class TiketPaketFactory extends Factory
{
    protected $model = TiketPaket::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'Tiket '.fake()->unique()->word(),
            'kategori' => TiketPaket::KATEGORI_UMUM,
            'deskripsi' => fake()->sentence(),
            'fasilitas' => 'Area playground, mini zoo',
            'harga' => 25000,
            'kuota_harian' => 100,
            'status_aktif' => true,
        ];
    }

    public function rombongan(): static
    {
        return $this->state(fn () => ['kategori' => TiketPaket::KATEGORI_ROMBONGAN]);
    }
}
