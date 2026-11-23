<?php

namespace Database\Factories;

use App\Models\Pesanan;
use App\Models\TiketPaket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pesanan>
 */
class PesananFactory extends Factory
{
    protected $model = Pesanan::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jumlah = fake()->numberBetween(1, 10);
        $harga = 25000;

        return [
            'kode_pesanan' => 'TNP-'.$this->faker->unique()->numerify('########'),
            'user_id' => User::factory(),
            'tiket_paket_id' => TiketPaket::factory(),
            'diskon_id' => null,
            'kategori' => TiketPaket::KATEGORI_UMUM,
            'tanggal_kunjungan' => now()->addDays(3)->toDateString(),
            'jumlah_tiket' => $jumlah,
            'harga_satuan' => $harga,
            'potongan_diskon' => 0,
            'total_bayar' => $harga * $jumlah,
            'status' => Pesanan::STATUS_MENUNGGU_PEMBAYARAN,
        ];
    }

    public function lunas(): static
    {
        return $this->state(fn () => ['status' => Pesanan::STATUS_LUNAS]);
    }
}
