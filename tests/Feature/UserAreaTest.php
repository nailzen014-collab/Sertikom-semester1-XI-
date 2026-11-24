<?php

use App\Models\Pesanan;
use App\Models\Tiket;
use App\Models\TiketPaket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('directs visitors to login before opening the customer area', function () {
    $this->get(route('user.dashboard'))->assertRedirect(route('login'));
});

it('shows only the signed-in visitor orders on their orders page', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $ownOrder = Pesanan::factory()->create([
        'user_id' => $user->id,
        'kode_pesanan' => 'TNP-OWN-01',
    ]);
    $otherOrder = Pesanan::factory()->create([
        'user_id' => $otherUser->id,
        'kode_pesanan' => 'TNP-OTHER-01',
    ]);

    $this->actingAs($user)
        ->get(route('user.orders'))
        ->assertOk()
        ->assertSee($ownOrder->kode_pesanan)
        ->assertDontSee($otherOrder->kode_pesanan);
});

it('renders the account summary, ticket booking, and orders on separate pages with the same navigation', function () {
    $user = User::factory()->create();
    $package = TiketPaket::factory()->create();
    $order = Pesanan::factory()->create([
        'user_id' => $user->id,
        'kode_pesanan' => 'TNP-ORDERS-PAGE',
    ]);

    $this->actingAs($user)
        ->get(route('user.dashboard'))
        ->assertOk()
        ->assertSee(route('user.booking'))
        ->assertSee(route('user.orders'))
        ->assertDontSee($order->kode_pesanan)
        ->assertDontSee($package->nama);

    $this->get(route('user.booking'))
        ->assertOk()
        ->assertSee($package->nama)
        ->assertSee(route('user.orders'))
        ->assertDontSee($order->kode_pesanan);

    $this->get(route('user.orders'))
        ->assertOk()
        ->assertSee($order->kode_pesanan)
        ->assertSee(route('user.booking'))
        ->assertDontSee('Buat pesanan');
});

it('keeps admins out of the visitor dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(route('user.dashboard'))->assertForbidden();
    $this->get(route('user.booking'))->assertForbidden();
    $this->get(route('user.orders'))->assertForbidden();
});

it('shows the signed-in customers name menu on the public landing page', function () {
    $user = User::factory()->create(['nama' => 'Nail Zen']);

    $this->actingAs($user)
        ->get(route('landing'))
        ->assertOk()
        ->assertSee('Nail Zen')
        ->assertSee('Profil saya')
        ->assertDontSee('Akun saya');
});

it('routes each authenticated role to its own dashboard', function () {
    $visitor = User::factory()->create();
    $admin = User::factory()->admin()->create();

    $this->actingAs($visitor)
        ->get(route('dashboard'))
        ->assertRedirect(route('user.dashboard'));

    $this->actingAs($admin)
        ->get(route('dashboard'))
        ->assertRedirect(route('admin.dashboard'));
});

it('does not reveal another visitors order or ticket', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $order = Pesanan::factory()->lunas()->create(['user_id' => $owner->id]);
    $ticket = Tiket::factory()->create(['pesanan_id' => $order->id]);

    $this->actingAs($user)
        ->get(route('riwayat.show', $order))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('tiket.show', $ticket))
        ->assertForbidden();
});

it('opens an owners order inside the customer dashboard', function () {
    $user = User::factory()->create();
    $order = Pesanan::factory()->lunas()->create(['user_id' => $user->id]);
    $ticket = Tiket::factory()->create(['pesanan_id' => $order->id]);

    $this->actingAs($user)
        ->get(route('riwayat.show', $order))
        ->assertOk()
        ->assertSee($order->kode_pesanan)
        ->assertSee($ticket->kode_tiket);
});

it('keeps ticket selection and order history on the account page', function () {
    $user = User::factory()->create();
    $package = TiketPaket::factory()->create();

    $this->actingAs($user)
        ->get(route('pesan.kategori'))
        ->assertRedirect(route('user.booking'));

    $this->actingAs($user)
        ->get(route('pesan.kategori', ['kategori' => 'rombongan']))
        ->assertRedirect(route('user.booking').'#paket-rombongan');

    $this->actingAs($user)
        ->get(route('pesan.create', $package))
        ->assertRedirect(route('user.booking').'#paket-'.$package->id);

    $this->actingAs($user)
        ->get(route('riwayat.index'))
        ->assertRedirect(route('user.orders'));
});

it('keeps the group application stages on the account page', function () {
    $user = User::factory()->create();
    $package = TiketPaket::factory()->create([
        'kategori' => TiketPaket::KATEGORI_ROMBONGAN,
        'kuota_harian' => 100,
    ]);

    $this->actingAs($user)
        ->get(route('pesan.rombongan.create', ['tiket_paket_id' => $package->id]))
        ->assertRedirect(route('user.booking', [
            'rombongan' => 1,
            'tiket_paket_id' => $package->id,
        ]).'#paket-rombongan');

    $this->get(route('user.booking', ['rombongan' => 1]))
        ->assertOk()
        ->assertSee('Ajukan rombongan')
        ->assertSee('Nama sekolah')
        ->assertSee('Lihat halaman taman')
        ->assertSee(route('landing'))
        ->assertSee('Profil saya');

    $this->post(route('pesan.rombongan.periksa'), [
        'tiket_paket_id' => $package->id,
        'tanggal_kunjungan' => now()->addDays(3)->toDateString(),
        'jumlah_tiket' => 11,
        'nama_sekolah' => 'SMA Rumpin',
        'penanggung_jawab' => 'Nail Zen',
        'no_hp' => '08123456789',
        'mode_tiket' => 'per_peserta',
    ])->assertRedirect(route('user.booking').'#paket-rombongan');

    $this->get(route('user.booking'))
        ->assertOk()
        ->assertSee('Lengkapi daftar peserta')
        ->assertSee('Kirim pengajuan ke admin');
});
