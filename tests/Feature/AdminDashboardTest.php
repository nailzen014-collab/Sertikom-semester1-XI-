<?php

use App\Models\Pembayaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows aggregated successful payments in the monthly revenue chart', function () {
    $admin = User::factory()->admin()->create();

    Pembayaran::factory()->create([
        'jumlah' => 25000,
        'status' => Pembayaran::STATUS_BERHASIL,
        'dibayar_pada' => now(),
    ]);
    Pembayaran::factory()->create([
        'jumlah' => 20000,
        'status' => Pembayaran::STATUS_BERHASIL,
        'dibayar_pada' => now(),
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Rp 45.000');
});

it('shows admin navigation as separate routes while keeping the dashboard focused', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('href="'.route('admin.dashboard').'"', false)
        ->assertSee('href="'.route('admin.pengajuan.index').'"', false)
        ->assertSee('href="'.route('admin.pesanan.index').'"', false)
        ->assertSee('href="'.route('admin.tiket-paket.index').'"', false)
        ->assertSee('href="'.route('admin.diskon.index').'"', false)
        ->assertSee('href="'.route('admin.tiket.validasi').'"', false)
        ->assertDontSee('Pengajuan rombongan</h2>')
        ->assertDontSee('Daftar pesanan</th>');

    $this->actingAs($admin)
        ->get(route('admin.pengajuan.index'))
        ->assertOk()
        ->assertSee('Pengajuan Rombongan')
        ->assertSee('class="admin-sidebar-nav"', false);
});
