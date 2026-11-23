<?php

namespace Database\Factories;

use App\Models\TanggalTutup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TanggalTutup>
 */
class TanggalTutupFactory extends Factory
{
    protected $model = TanggalTutup::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tanggal' => now()->addDays(5)->toDateString(),
            'keterangan' => 'Tutup sementara',
        ];
    }
}
