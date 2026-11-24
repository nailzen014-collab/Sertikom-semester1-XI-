<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Pesanan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class PembayaranService
{
    public function __construct(
        private TiketGenerator $tiketGenerator,
    ) {}

    public function pastikanPembayaran(Pesanan $pesanan): Pembayaran
    {
        return $pesanan->pembayaran()->firstOrCreate(
            ['order_id_gateway' => $pesanan->kode_pesanan],
            [
                'jumlah' => $pesanan->total_bayar,
                'status' => Pembayaran::STATUS_PENDING,
            ],
        );
    }

    public function redirectUrl(Pesanan $pesanan): string
    {
        $pesanan->load(['user', 'tiketPaket']);

        $pembayaran = $this->pastikanPembayaran($pesanan);
        $this->konfigurasi();

        $transaksi = Snap::createTransaction([
            'transaction_details' => [
                'order_id' => $pembayaran->order_id_gateway,
                'gross_amount' => (int) $pesanan->total_bayar,
            ],
            'customer_details' => [
                'first_name' => $pesanan->user->nama,
                'email' => $pesanan->user->email,
                'phone' => $pesanan->user->no_hp,
            ],
            'item_details' => [[
                'id' => (string) $pesanan->tiket_paket_id,
                'price' => (int) ($pesanan->harga_satuan - intdiv(
                    $pesanan->potongan_diskon,
                    max(1, $pesanan->jumlah_tiket)
                )),
                'quantity' => (int) $pesanan->jumlah_tiket,
                'name' => Str::limit($pesanan->tiketPaket->nama, 50),
            ]],
            'callbacks' => [
                'finish' => route('pembayaran.selesai', $pesanan),
            ],
        ]);

        return $transaksi->redirect_url;
    }

    public function sinkronkanDariGateway(Pesanan $pesanan): void
    {
        $pembayaran = $this->pastikanPembayaran($pesanan);

        if ($pembayaran->sudahBerhasil()) {
            return;
        }

        try {
            $this->konfigurasi();
            $hasil = Transaction::status($pembayaran->order_id_gateway);
        } catch (\Throwable $e) {
            report($e);

            return;
        }

        $this->proses($pembayaran, (array) $hasil);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function proses(Pembayaran $pembayaran, array $payload): Pembayaran
    {
        return DB::transaction(function () use ($pembayaran, $payload) {
            $pembayaran = Pembayaran::whereKey($pembayaran->getKey())->lockForUpdate()->first();

            if ($pembayaran->sudahBerhasil()) {
                return $pembayaran;
            }

            $status = $this->petakanStatus(
                $payload['transaction_status'] ?? 'pending',
                $payload['fraud_status'] ?? 'accept',
            );

            $pembayaran->fill([
                'status' => $status,
                'metode' => $payload['payment_type'] ?? $pembayaran->metode,
                'dibayar_pada' => $status === Pembayaran::STATUS_BERHASIL ? now() : $pembayaran->dibayar_pada,
            ])->save();

            $pesanan = $pembayaran->pesanan;

            if ($status === Pembayaran::STATUS_BERHASIL) {
                $pesanan->update(['status' => Pesanan::STATUS_LUNAS]);
                $this->tiketGenerator->buatUntukPesanan($pesanan);
            } elseif ($status === Pembayaran::STATUS_KEDALUWARSA) {
                $pesanan->update(['status' => Pesanan::STATUS_KEDALUWARSA]);
            }

            return $pembayaran;
        });
    }

    public function konfigurasi(): void
    {
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = (bool) config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    private function petakanStatus(string $transactionStatus, string $fraudStatus): string
    {
        return match (true) {
            $transactionStatus === 'settlement' => Pembayaran::STATUS_BERHASIL,
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => Pembayaran::STATUS_BERHASIL,
            $transactionStatus === 'expire' => Pembayaran::STATUS_KEDALUWARSA,
            $transactionStatus === 'deny', $transactionStatus === 'cancel' => Pembayaran::STATUS_GAGAL,
            default => Pembayaran::STATUS_PENDING,
        };
    }
}
