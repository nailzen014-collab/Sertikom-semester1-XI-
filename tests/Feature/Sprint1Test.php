<?php

use App\Models\Diskon;
use App\Models\TiketPaket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function buatTiket(array $overrides = []): TiketPaket
{
    return TiketPaket::create([
        'nama' => 'Tiket Masuk Reguler',
        'kategori' => TiketPaket::KATEGORI_UMUM,
        'harga' => 10000,
        'kuota_harian' => 200,
        'status_aktif' => true,
        ...$overrides,
    ]);
}

it('menampilkan landing page dengan ketentuan pemilihan tiket', function () {
    buatTiket();

    $this->get('/')
        ->assertOk()
        ->assertSee('Ketentuan Pemilihan Tiket')
        ->assertSee('maksimal 10')
        ->assertSee('lebih dari 10')
        ->assertSee('Tiket Masuk Reguler');
});

it('mengarahkan tamu ke login saat membuka area admin', function () {
    $this->get('/admin')->assertRedirect('/login');
});

it('menolak pengunjung mengakses area admin', function () {
    $pengunjung = User::factory()->create();

    expect($pengunjung->role)->toBe(User::ROLE_PENGUNJUNG);

    $this->actingAs($pengunjung)->get('/admin')->assertForbidden();
});

it('mengizinkan admin membuka dashboard admin', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Dashboard Admin');
});

it('mengizinkan admin menambah tiket paket', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->post('/admin/tiket-paket', [
            'nama' => 'Tiket Uji',
            'kategori' => TiketPaket::KATEGORI_UMUM,
            'harga' => 5000,
            'kuota_harian' => 20,
            'status_aktif' => 1,
        ])
        ->assertRedirect(route('admin.tiket-paket.index'));

    $this->assertDatabaseHas('tiket_paket', [
        'nama' => 'Tiket Uji',
        'kategori' => TiketPaket::KATEGORI_UMUM,
        'harga' => 5000,
        'kuota_harian' => 20,
        'status_aktif' => true,
    ]);
});

it('menolak pengunjung menambah tiket paket', function () {
    $pengunjung = User::factory()->create();

    $this->actingAs($pengunjung)
        ->post('/admin/tiket-paket', [
            'nama' => 'Tiket Ilegal',
            'kategori' => TiketPaket::KATEGORI_UMUM,
            'harga' => 5000,
            'kuota_harian' => 20,
            'status_aktif' => 1,
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('tiket_paket', ['nama' => 'Tiket Ilegal']);
});

it('mengizinkan admin menambah diskon untuk paket tertentu', function () {
    $admin = User::factory()->admin()->create();
    $tiket = buatTiket();

    $this->actingAs($admin)
        ->post('/admin/diskon', [
            'nama' => 'Promo Umum Uji',
            'tiket_paket_id' => $tiket->id,
            'tipe' => Diskon::TIPE_PERSEN,
            'nilai' => 10,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'status_aktif' => 1,
            'ganti_diskon' => 0,
        ])
        ->assertRedirect(route('admin.diskon.index'));

    $this->assertDatabaseHas('diskon', [
        'nama' => 'Promo Umum Uji',
        'tiket_paket_id' => $tiket->id,
        'admin_id' => $admin->id,
    ]);
});

it('mengalihkan kembali dengan error saat paket diskon tidak dipilih', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->from(route('admin.diskon.create'))
        ->post('/admin/diskon', [
            'nama' => 'Tanpa Paket',
            'tiket_paket_id' => '',
            'tipe' => Diskon::TIPE_PERSEN,
            'nilai' => 10,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'status_aktif' => 1,
        ])
        ->assertRedirect(route('admin.diskon.create'))
        ->assertSessionHasErrors('tiket_paket_id');

    $this->assertDatabaseMissing('diskon', ['nama' => 'Tanpa Paket']);
});

it('menolak diskon persen lebih dari 100', function () {
    $admin = User::factory()->admin()->create();
    $tiket = buatTiket();

    $this->actingAs($admin)
        ->post('/admin/diskon', [
            'nama' => 'Promo Salah',
            'tiket_paket_id' => $tiket->id,
            'tipe' => Diskon::TIPE_PERSEN,
            'nilai' => 150,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'status_aktif' => 1,
        ])
        ->assertSessionHasErrors('nilai');

    $this->assertDatabaseMissing('diskon', ['nama' => 'Promo Salah']);
});

it('menonaktifkan diskon lama saat admin memilih mengganti diskon pada paket yang sama', function () {
    $admin = User::factory()->admin()->create();
    $tiket = buatTiket();
    $lama = Diskon::factory()->create([
        'nama' => 'Promo Lama',
        'tiket_paket_id' => $tiket->id,
        'status_aktif' => true,
    ]);

    $this->actingAs($admin)
        ->post('/admin/diskon', [
            'nama' => 'Promo Baru',
            'tiket_paket_id' => $tiket->id,
            'tipe' => Diskon::TIPE_PERSEN,
            'nilai' => 20,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'status_aktif' => 1,
            'ganti_diskon' => 1,
        ])
        ->assertRedirect(route('admin.diskon.index'));

    $this->assertDatabaseHas('diskon', [
        'id' => $lama->id,
        'status_aktif' => false,
    ]);

    $this->assertDatabaseHas('diskon', [
        'nama' => 'Promo Baru',
        'tiket_paket_id' => $tiket->id,
        'status_aktif' => true,
    ]);
});

it('mempertahankan diskon lama saat admin memilih tidak mengganti', function () {
    $admin = User::factory()->admin()->create();
    $tiket = buatTiket();
    $lama = Diskon::factory()->create([
        'nama' => 'Promo Lama',
        'tiket_paket_id' => $tiket->id,
        'status_aktif' => true,
    ]);

    $this->actingAs($admin)
        ->post('/admin/diskon', [
            'nama' => 'Promo Tambahan',
            'tiket_paket_id' => $tiket->id,
            'tipe' => Diskon::TIPE_NOMINAL,
            'nilai' => 5000,
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_selesai' => now()->addMonth()->toDateString(),
            'status_aktif' => 1,
            'ganti_diskon' => 0,
        ])
        ->assertRedirect(route('admin.diskon.index'));

    $this->assertDatabaseHas('diskon', [
        'id' => $lama->id,
        'status_aktif' => true,
    ]);

    $this->assertDatabaseHas('diskon', [
        'nama' => 'Promo Tambahan',
        'tiket_paket_id' => $tiket->id,
        'status_aktif' => true,
    ]);
});

it('menampilkan halaman kelola tiket dan diskon untuk admin', function () {
    $admin = User::factory()->admin()->create();
    $tiket = buatTiket();

    $this->actingAs($admin)
        ->get(route('admin.tiket-paket.index'))
        ->assertOk()
        ->assertSee('Tiket Masuk Reguler');

    $this->actingAs($admin)
        ->get(route('admin.tiket-paket.create'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('admin.tiket-paket.edit', $tiket))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('admin.diskon.index'))
        ->assertOk();

    $this->actingAs($admin)
        ->get(route('admin.diskon.create'))
        ->assertOk();
});

it('memberi role pengunjung pada registrasi baru', function () {
    $this->post('/register', [
        'nama' => 'Pengunjung Baru',
        'email' => 'baru@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ])->assertRedirect(route('user.dashboard'));

    expect(User::where('email', 'baru@example.com')->value('role'))
        ->toBe(User::ROLE_PENGUNJUNG);
});
