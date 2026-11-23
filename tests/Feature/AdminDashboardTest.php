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

it('keeps every admin sidebar destination inside the dashboard page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('href="#ringkasan"', false)
        ->assertSee('href="#pengajuan"', false)
        ->assertSee('href="#pesanan"', false)
        ->assertSee('href="#tiket-paket"', false)
        ->assertSee('href="#diskon"', false)
        ->assertSee('href="#validasi-tiket"', false)
        ->assertSee('id="pengajuan"', false)
        ->assertSee('id="pesanan"', false)
        ->assertSee('id="tiket-paket"', false)
        ->assertSee('id="diskon"', false)
        ->assertSee('id="validasi-tiket"', false);
});
