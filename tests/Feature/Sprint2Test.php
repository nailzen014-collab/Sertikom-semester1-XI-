<?php

use App\Models\Diskon;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Models\TanggalTutup;
use App\Models\Tiket;
use App\Models\TiketPaket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function tiketUmum(array $overrides = []): TiketPaket
{
    return TiketPaket::factory()->create([
        'kategori' => TiketPaket::KATEGORI_UMUM,
        'harga' => 10000,
        'kuota_harian' => 50,
        ...$overrides,
    ]);
}

function signatureMidtrans(string $orderId, string $statusCode, string $grossAmount, string $key): string
{
    return hash('sha512', $orderId.$statusCode.$grossAmount.$key);
}

it('membuat pesanan tiket umum dengan total dihitung server', function () {
    $user = User::factory()->create();
    $tiket = tiketUmum();
    $tanggal = now()->addDays(2)->toDateString();

    $this->actingAs($user)
        ->post(route('pesan.store', $tiket), [
            'tanggal_kunjungan' => $tanggal,
            'jumlah_tiket' => 10,
        ])
        ->assertRedirect();

    $pesanan = Pesanan::firstOrFail();

    expect($pesanan->user_id)->toBe($user->id)
        ->and($pesanan->jumlah_tiket)->toBe(10)
        ->and($pesanan->harga_satuan)->toBe(10000)
        ->and($pesanan->potongan_diskon)->toBe(0)
        ->and($pesanan->total_bayar)->toBe(100000)
        ->and($pesanan->status)->toBe(Pesanan::STATUS_MENUNGGU_PEMBAYARAN);
});

it('mengarahkan pesanan umum di atas 10 tiket ke pengajuan rombongan', function () {
    $user = User::factory()->create();
    $tiket = tiketUmum();

    $this->actingAs($user)
        ->post(route('pesan.store', $tiket), [
            'tanggal_kunjungan' => now()->addDay()->toDateString(),
            'jumlah_tiket' => 11,
        ])
        ->assertRedirect(route('pesan.rombongan.create'))
        ->assertSessionHas('message', 'Pesanan lebih dari 10 tiket harus diajukan sebagai rombongan.');

    expect(Pesanan::count())->toBe(0);
});

it('membatasi pesanan umum maksimal lima per akun dan tidak menghitung pesanan rombongan', function () {
    $user = User::factory()->create();
    $tiket = tiketUmum();
    $paketRombongan = TiketPaket::factory()->create([
        'kategori' => TiketPaket::KATEGORI_ROMBONGAN,
    ]);

    Pesanan::factory()->count(4)->create([
        'user_id' => $user->id,
        'tiket_paket_id' => $tiket->id,
        'kategori' => TiketPaket::KATEGORI_UMUM,
    ]);
    Pesanan::factory()->create([
        'user_id' => $user->id,
        'tiket_paket_id' => $paketRombongan->id,
        'kategori' => TiketPaket::KATEGORI_ROMBONGAN,
    ]);

    $payload = [
        'tanggal_kunjungan' => now()->addDays(2)->toDateString(),
        'jumlah_tiket' => 1,
    ];

    $this->actingAs($user)
        ->post(route('pesan.store', $tiket), $payload)
        ->assertRedirect();

    expect(Pesanan::where('user_id', $user->id)->where('kategori', TiketPaket::KATEGORI_UMUM)->count())->toBe(5);

    $this->post(route('pesan.store', $tiket), $payload)
        ->assertSessionHasErrors([
            'jumlah_tiket' => 'Setiap akun maksimal dapat membuat 5 pesanan tiket umum per bulan. Kuota direset setiap tanggal 1.',
        ]);

    expect(Pesanan::where('user_id', $user->id)->where('kategori', TiketPaket::KATEGORI_UMUM)->count())->toBe(5);
});

it('mereset kuota pesanan umum setiap bulan sehingga pesanan bulan lalu tidak terhitung', function () {
    $user = User::factory()->create();
    $tiket = tiketUmum();

    Pesanan::factory()->count(5)->create([
        'user_id' => $user->id,
        'tiket_paket_id' => $tiket->id,
        'kategori' => TiketPaket::KATEGORI_UMUM,
        'created_at' => now()->subMonthNoOverflow()->startOfMonth()->addDay(),
        'updated_at' => now()->subMonthNoOverflow()->startOfMonth()->addDay(),
    ]);

    $this->actingAs($user)
        ->post(route('pesan.store', $tiket), [
            'tanggal_kunjungan' => now()->addDays(2)->toDateString(),
            'jumlah_tiket' => 1,
        ])
        ->assertRedirect();

    expect(Pesanan::where('user_id', $user->id)
        ->where('kategori', TiketPaket::KATEGORI_UMUM)
        ->dibuatBulanIni()
        ->count())->toBe(1);
});

it('menerapkan diskon paket secara otomatis', function () {
    $user = User::factory()->create();
    $tiket = tiketUmum();
    $diskon = Diskon::factory()->create([
        'tiket_paket_id' => $tiket->id,
        'tipe' => Diskon::TIPE_PERSEN,
        'nilai' => 10,
    ]);

    $this->actingAs($user)->post(route('pesan.store', $tiket), [
        'tanggal_kunjungan' => now()->addDays(2)->toDateString(),
        'jumlah_tiket' => 2,
    ]);

    $pesanan = Pesanan::firstOrFail();

    expect($pesanan->diskon_id)->toBe($diskon->id)
        ->and($pesanan->potongan_diskon)->toBe(2000)
        ->and($pesanan->total_bayar)->toBe(18000);
});

it('tidak menerapkan diskon paket lain pada pesanan ini', function () {
    $user = User::factory()->create();
    $tiket = tiketUmum();
    $paketLain = tiketUmum();
    Diskon::factory()->create([
        'tiket_paket_id' => $paketLain->id,
        'tipe' => Diskon::TIPE_PERSEN,
        'nilai' => 50,
    ]);

    $this->actingAs($user)->post(route('pesan.store', $tiket), [
        'tanggal_kunjungan' => now()->addDays(2)->toDateString(),
        'jumlah_tiket' => 2,
    ]);

    $pesanan = Pesanan::firstOrFail();

    expect($pesanan->diskon_id)->toBeNull()
        ->and($pesanan->potongan_diskon)->toBe(0)
        ->and($pesanan->total_bayar)->toBe(20000);
});

it('menolak pesanan pada tanggal tutup', function () {
    $user = User::factory()->create();
    $tiket = tiketUmum();
    $tanggal = now()->addDays(3)->toDateString();
    TanggalTutup::factory()->create(['tanggal' => $tanggal]);

    $this->actingAs($user)
        ->post(route('pesan.store', $tiket), [
            'tanggal_kunjungan' => $tanggal,
            'jumlah_tiket' => 2,
        ])
        ->assertSessionHasErrors('tanggal_kunjungan');

    expect(Pesanan::count())->toBe(0);
});

it('menolak pesanan yang melebihi kuota harian', function () {
    $user = User::factory()->create();
    $tiket = tiketUmum(['kuota_harian' => 10]);
    $tanggal = now()->addDays(2)->toDateString();

    Pesanan::factory()->lunas()->create([
        'user_id' => $user->id,
        'tiket_paket_id' => $tiket->id,
        'tanggal_kunjungan' => $tanggal,
        'jumlah_tiket' => 10,
    ]);

    $this->actingAs($user)
        ->post(route('pesan.store', $tiket), [
            'tanggal_kunjungan' => $tanggal,
            'jumlah_tiket' => 1,
        ])
        ->assertSessionHasErrors('jumlah_tiket');
});

it('menolak webhook dengan signature tidak valid', function () {
    config(['services.midtrans.server_key' => 'SB-Mid-server-test']);

    $pesanan = Pesanan::factory()->create(['total_bayar' => 25000]);
    $pembayaran = Pembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'jumlah' => 25000,
    ]);

    $this->postJson(route('payment.webhook'), [
        'order_id' => $pembayaran->order_id_gateway,
        'status_code' => '200',
        'gross_amount' => '25000.00',
        'transaction_status' => 'settlement',
        'signature_key' => 'signature-palsu',
    ])->assertForbidden();

    expect($pembayaran->fresh()->status)->toBe(Pembayaran::STATUS_PENDING);
});

it('webhook berhasil menandai lunas dan menerbitkan tiket', function () {
    $key = 'SB-Mid-server-test';
    config(['services.midtrans.server_key' => $key]);

    $pesanan = Pesanan::factory()->create([
        'jumlah_tiket' => 3,
        'total_bayar' => 75000,
        'status' => Pesanan::STATUS_MENUNGGU_PEMBAYARAN,
    ]);
    $pembayaran = Pembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'jumlah' => 75000,
    ]);

    $payload = [
        'order_id' => $pembayaran->order_id_gateway,
        'status_code' => '200',
        'gross_amount' => '75000.00',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'payment_type' => 'qris',
    ];
    $payload['signature_key'] = signatureMidtrans($payload['order_id'], '200', '75000.00', $key);

    $this->postJson(route('payment.webhook'), $payload)->assertOk();

    expect($pembayaran->fresh()->status)->toBe(Pembayaran::STATUS_BERHASIL)
        ->and($pesanan->fresh()->status)->toBe(Pesanan::STATUS_LUNAS)
        ->and(Tiket::where('pesanan_id', $pesanan->id)->count())->toBe(3);
});

it('webhook ganda tidak menerbitkan tiket ganda', function () {
    $key = 'SB-Mid-server-test';
    config(['services.midtrans.server_key' => $key]);

    $pesanan = Pesanan::factory()->create([
        'jumlah_tiket' => 2,
        'total_bayar' => 50000,
    ]);
    $pembayaran = Pembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'jumlah' => 50000,
    ]);

    $payload = [
        'order_id' => $pembayaran->order_id_gateway,
        'status_code' => '200',
        'gross_amount' => '50000.00',
        'transaction_status' => 'settlement',
        'fraud_status' => 'accept',
        'payment_type' => 'qris',
    ];
    $payload['signature_key'] = signatureMidtrans($payload['order_id'], '200', '50000.00', $key);

    $this->postJson(route('payment.webhook'), $payload)->assertOk();
    $this->postJson(route('payment.webhook'), $payload)->assertOk();

    expect(Tiket::where('pesanan_id', $pesanan->id)->count())->toBe(2);
});

it('mengarahkan halaman pembayaran ke pesanan pada dashboard tanpa membuat transaksi', function () {
    $user = User::factory()->create();
    $pesanan = Pesanan::factory()->create([
        'user_id' => $user->id,
        'jumlah_tiket' => 2,
        'total_bayar' => 50000,
    ]);

    $this->actingAs($user)
        ->get(route('pembayaran.show', $pesanan))
        ->assertRedirect(route('user.orders', ['pesanan' => $pesanan->id]));

    expect(Pembayaran::count())->toBe(0);
});

it('pembayaran kedaluwarsa tidak menerbitkan tiket', function () {
    $key = 'SB-Mid-server-test';
    config(['services.midtrans.server_key' => $key]);

    $pesanan = Pesanan::factory()->create(['jumlah_tiket' => 2, 'total_bayar' => 20000]);
    $pembayaran = Pembayaran::factory()->create([
        'pesanan_id' => $pesanan->id,
        'jumlah' => 20000,
    ]);

    $payload = [
        'order_id' => $pembayaran->order_id_gateway,
        'status_code' => '202',
        'gross_amount' => '20000.00',
        'transaction_status' => 'expire',
    ];
    $payload['signature_key'] = signatureMidtrans($payload['order_id'], '202', '20000.00', $key);

    $this->postJson(route('payment.webhook'), $payload)->assertOk();

    expect($pembayaran->fresh()->status)->toBe(Pembayaran::STATUS_KEDALUWARSA)
        ->and($pesanan->fresh()->status)->toBe(Pesanan::STATUS_KEDALUWARSA)
        ->and(Tiket::where('pesanan_id', $pesanan->id)->count())->toBe(0);
});

it('pengunjung tidak dapat melihat pesanan atau tiket milik orang lain', function () {
    $pemilik = User::factory()->create();
    $lain = User::factory()->create();
    $pesanan = Pesanan::factory()->create(['user_id' => $pemilik->id]);
    $tiket = Tiket::factory()->create(['pesanan_id' => $pesanan->id]);

    $this->actingAs($lain)->get(route('riwayat.show', $pesanan))->assertForbidden();
    $this->actingAs($lain)->get(route('pembayaran.show', $pesanan))->assertForbidden();
    $this->actingAs($lain)->get(route('tiket.show', $tiket))->assertForbidden();
});

it('pengunjung tidak dapat mengakses pemantauan pesanan admin', function () {
    $pengunjung = User::factory()->create();

    $this->actingAs($pengunjung)->get(route('admin.pesanan.index'))->assertForbidden();
});

it('riwayat mengarah ke halaman yang hanya memuat pesanan milik sendiri', function () {
    $a = User::factory()->create();
    $b = User::factory()->create();

    $pesananA = Pesanan::factory()->create(['user_id' => $a->id]);
    $pesananB = Pesanan::factory()->create(['user_id' => $b->id]);

    $this->actingAs($a)
        ->get(route('riwayat.index'))
        ->assertRedirect(route('user.orders'));

    $this->actingAs($a)
        ->get(route('user.orders'))
        ->assertOk()
        ->assertSee($pesananA->kode_pesanan)
        ->assertDontSee($pesananB->kode_pesanan);
});

it('admin dapat memantau pesanan dan pembayaran', function () {
    $admin = User::factory()->admin()->create();
    $pesanan = Pesanan::factory()->create();

    $this->actingAs($admin)
        ->get(route('admin.pesanan.index'))
        ->assertOk()
        ->assertSee($pesanan->kode_pesanan);

    $this->actingAs($admin)
        ->get(route('admin.pesanan.show', $pesanan))
        ->assertOk()
        ->assertSee($pesanan->user->nama);
});

it('pengunjung dapat mengunduh e-ticket pdf', function () {
    $user = User::factory()->create();
    $pesanan = Pesanan::factory()->lunas()->create(['user_id' => $user->id, 'jumlah_tiket' => 1]);
    $tiket = Tiket::factory()->create(['pesanan_id' => $pesanan->id]);

    $this->actingAs($user)
        ->get(route('tiket.pdf', $tiket))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});
