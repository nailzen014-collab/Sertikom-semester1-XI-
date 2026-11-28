<?php

namespace Database\Seeders;

use App\Models\Diskon;
use App\Models\TiketPaket;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@thenicepark.test'],
            [
                'nama' => 'Admin The Nice Park',
                'password' => Hash::make('password'),
            ],
        );

        if (! $admin->isSuperadmin()) {
            $admin->role = User::ROLE_SUPERADMIN;
            $admin->save();
        }

        User::firstOrCreate(
            ['email' => 'pengunjung@example.com'],
            [
                'nama' => 'Pengunjung Demo',
                'no_hp' => '081234567890',
                'password' => Hash::make('password'),
            ],
        );

        $tiketPaket = [
            [
                'nama' => 'Tiket Masuk Reguler',
                'kategori' => TiketPaket::KATEGORI_UMUM,
                'deskripsi' => 'Tiket masuk harian untuk pengunjung umum.',
                'fasilitas' => 'Akses area taman, kolam renang, dan playground.',
                'harga' => 10000,
                'kuota_harian' => 200,
                'status_aktif' => true,
            ],
            [
                'nama' => 'Tiket Terusan Keluarga',
                'kategori' => TiketPaket::KATEGORI_UMUM,
                'deskripsi' => 'Tiket masuk plus wahana air untuk keluarga.',
                'fasilitas' => 'Semua fasilitas reguler, wahana air, dan kursi santai.',
                'harga' => 25000,
                'kuota_harian' => 100,
                'status_aktif' => true,
            ],
            [
                'nama' => 'Paket Edukasi Sekolah',
                'kategori' => TiketPaket::KATEGORI_ROMBONGAN,
                'deskripsi' => 'Paket kunjungan sekolah dengan pemandu dan kegiatan edukasi.',
                'fasilitas' => 'Pemandu, area makan rombongan, kegiatan edukasi, dan tiket masuk.',
                'harga' => 15000,
                'kuota_harian' => 150,
                'status_aktif' => true,
            ],
        ];

        $paketPerNama = [];
        foreach ($tiketPaket as $data) {
            $paket = TiketPaket::firstOrCreate(['nama' => $data['nama']], $data);
            $paketPerNama[$data['nama']] = $paket;
        }

        $diskon = [
            [
                'nama' => 'Promo Pengunjung Umum',
                'paket' => 'Tiket Masuk Reguler',
                'tipe' => Diskon::TIPE_PERSEN,
                'nilai' => 10,
            ],
            [
                'nama' => 'Diskon Rombongan Sekolah',
                'paket' => 'Paket Edukasi Sekolah',
                'tipe' => Diskon::TIPE_NOMINAL,
                'nilai' => 2000,
            ],
        ];

        foreach ($diskon as $data) {
            $paket = $paketPerNama[$data['paket']];

            Diskon::firstOrCreate(['nama' => $data['nama']], [
                'admin_id' => $admin->id,
                'tiket_paket_id' => $paket->id,
                'tipe' => $data['tipe'],
                'nilai' => $data['nilai'],
                'tanggal_mulai' => now()->subWeek()->toDateString(),
                'tanggal_selesai' => now()->addMonth()->toDateString(),
                'status_aktif' => true,
            ]);
        }
    }
}
