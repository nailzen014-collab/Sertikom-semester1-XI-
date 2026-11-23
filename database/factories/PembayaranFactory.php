<?php

namespace Database\Factories;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pembayaran>
 */
class PembayaranFactory extends Factory
{
    protected $model = Pembayaran::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pesanan_id' => Pesanan::factory(),
            'order_id_gateway' => function (array $attributes) {
                return Pesanan::find($attributes['pesanan_id'])->kode_pesanan;
            },
            'metode' => 'qris',
            'jumlah' => fn (array $attributes) => Pesanan::find($attributes['pesanan_id'])->total_bayar,
            'status' => Pembayaran::STATUS_PENDING,
            'dibayar_pada' => null,
        ];
    }
}
