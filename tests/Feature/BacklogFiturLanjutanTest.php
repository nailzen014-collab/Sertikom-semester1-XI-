<?php

use App\Models\PengajuanRombongan;
use App\Models\Pesanan;
use App\Models\PesertaRombongan;
use App\Models\Tiket;
use App\Models\TiketPaket;
use App\Models\User;

test('admin dapat mengekspor laporan pesanan dalam format excel dan pdf', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.laporan.excel'))
        ->assertOk()
        ->assertHeader('content-disposition');

    $this->actingAs($admin)
        ->get(route('admin.laporan.pdf'))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('pengunjung non-admin tidak dapat mengakses endpoint ekspor laporan', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.laporan.excel'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('admin.laporan.pdf'))
        ->assertForbidden();
});

test('pemesan rombongan dapat mengubah nama dan kelas peserta sebelum hari kunjungan', function () {
    $user = User::factory()->create();
    $paket = TiketPaket::factory()->create(['kategori' => TiketPaket::KATEGORI_ROMBONGAN]);

    $pesanan = Pesanan::factory()->create([
        'user_id' => $user->id,
        'tiket_paket_id' => $paket->id,
        'kategori' => TiketPaket::KATEGORI_ROMBONGAN,
        'tanggal_kunjungan' => now()->addDays(3),
        'jumlah_tiket' => 15,
        'status' => Pesanan::STATUS_MENUNGGU_PEMBAYARAN,
    ]);

    $peserta = PesertaRombongan::create([
        'pesanan_id' => $pesanan->id,
        'nama' => 'Budi Lama',
        'kelas' => '7A',
    ]);

    $this->actingAs($user)
        ->post(route('pesan.rombongan.update-peserta', [$pesanan, $peserta]), [
            'nama' => 'Budi Baru',
            'kelas' => '7B',
        ])
        ->assertRedirect();

    expect($peserta->fresh()->nama)->toBe('Budi Baru')
        ->and($peserta->fresh()->kelas)->toBe('7B');
});

test('pemesan lain tidak dapat mengubah nama peserta pesanan orang lain', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $paket = TiketPaket::factory()->create(['kategori' => TiketPaket::KATEGORI_ROMBONGAN]);

    $pesanan = Pesanan::factory()->create([
        'user_id' => $user1->id,
        'tiket_paket_id' => $paket->id,
        'kategori' => TiketPaket::KATEGORI_ROMBONGAN,
        'tanggal_kunjungan' => now()->addDays(3),
        'jumlah_tiket' => 15,
    ]);

    $peserta = PesertaRombongan::create([
        'pesanan_id' => $pesanan->id,
        'nama' => 'Siswa Asli',
        'kelas' => '8A',
    ]);

    $this->actingAs($user2)
        ->post(route('pesan.rombongan.update-peserta', [$pesanan, $peserta]), [
            'nama' => 'Nama Hacker',
            'kelas' => '99',
        ])
        ->assertForbidden();

    expect($peserta->fresh()->nama)->toBe('Siswa Asli');
});

test('pemesan rombongan dapat mengunduh e-ticket induk rombongan setelah lunas', function () {
    $user = User::factory()->create();
    $paket = TiketPaket::factory()->create(['kategori' => TiketPaket::KATEGORI_ROMBONGAN]);

    $pesanan = Pesanan::factory()->create([
        'user_id' => $user->id,
        'tiket_paket_id' => $paket->id,
        'kategori' => TiketPaket::KATEGORI_ROMBONGAN,
        'tanggal_kunjungan' => now()->addDays(2),
        'jumlah_tiket' => 12,
        'status' => Pesanan::STATUS_LUNAS,
    ]);

    PengajuanRombongan::create([
        'pesanan_id' => $pesanan->id,
        'nama_sekolah' => 'SMP Negeri 1 Rumpin',
        'penanggung_jawab' => 'Ibu Guru',
        'no_hp' => '081234567890',
        'status' => PengajuanRombongan::STATUS_DISETUJUI,
    ]);

    Tiket::create([
        'pesanan_id' => $pesanan->id,
        'kode_tiket' => 'TKT-RMB-001',
        'jenis' => Tiket::JENIS_ROMBONGAN,
        'status' => Tiket::STATUS_AKTIF,
    ]);

    $this->actingAs($user)
        ->get(route('pesanan.pdf-rombongan', $pesanan))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('alur simpan peserta rombongan berhasil membuat pesanan dan pengajuan', function () {
    $user = User::factory()->create();
    $paket = TiketPaket::factory()->create([
        'kategori' => TiketPaket::KATEGORI_ROMBONGAN,
        'harga' => 50000,
        'kuota_harian' => 100,
        'status_aktif' => true,
    ]);

    $draft = [
        'tiket_paket_id' => $paket->id,
        'tanggal_kunjungan' => now()->addDays(5)->toDateString(),
        'jumlah_tiket' => 11,
        'nama_sekolah' => 'SMA Negeri 2 Rumpin',
        'penanggung_jawab' => 'Pak Ahmad',
        'no_hp' => '081234567899',
        'mode_tiket' => 'per_siswa',
    ];

    $peserta = array_map(fn ($i) => [
        'nama' => 'Siswa '.$i,
        'kelas' => '10A',
    ], range(1, 11));

    $response = $this->actingAs($user)
        ->withSession(['pengajuan_rombongan_draft' => $draft])
        ->post(route('pesan.rombongan.simpan-peserta'), [
            'sumber_data' => 'manual',
            'peserta' => $peserta,
        ]);

    $response->assertRedirect();
    $this->assertDatabaseHas('pesanan', [
        'user_id' => $user->id,
        'tiket_paket_id' => $paket->id,
        'kategori' => TiketPaket::KATEGORI_ROMBONGAN,
        'jumlah_tiket' => 11,
        'status' => Pesanan::STATUS_MENUNGGU_PERSETUJUAN,
    ]);
});
