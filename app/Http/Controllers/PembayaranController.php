<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Services\PembayaranService;
use Illuminate\Http\RedirectResponse;

class PembayaranController extends Controller
{
    public function show(Pesanan $pesanan): RedirectResponse
    {
        $this->pastikanPemilik($pesanan);

        if ($redirect = $this->periksaStatus($pesanan)) {
            return $redirect;
        }

        return to_route('user.orders', ['pesanan' => $pesanan->id]);
    }

    public function process(Pesanan $pesanan, PembayaranService $pembayaran): RedirectResponse
    {
        $this->pastikanPemilik($pesanan);

        if ($redirect = $this->periksaStatus($pesanan)) {
            return $redirect;
        }

        try {
            $url = $pembayaran->redirectUrl($pesanan);
        } catch (\Throwable $e) {
            report($e);

            return to_route('user.orders', ['pesanan' => $pesanan->id])
                ->with('error', 'Gagal menghubungi payment gateway. Silakan coba lagi.');
        }

        return redirect()->away($url);
    }

    public function selesai(Pesanan $pesanan, PembayaranService $pembayaran): RedirectResponse
    {
        $this->pastikanPemilik($pesanan);

        $pembayaran->sinkronkanDariGateway($pesanan);

        return to_route('user.orders', ['pesanan' => $pesanan->id])
            ->with('message', 'Status pembayaran telah diperbarui.');
    }

    private function periksaStatus(Pesanan $pesanan): ?RedirectResponse
    {
        if ($pesanan->status === Pesanan::STATUS_LUNAS) {
            return to_route('user.orders', ['pesanan' => $pesanan->id]);
        }

        if ($pesanan->status !== Pesanan::STATUS_MENUNGGU_PEMBAYARAN) {
            return to_route('user.orders', ['pesanan' => $pesanan->id])
                ->with('error', 'Pesanan ini tidak dapat dibayar.');
        }

        return null;
    }

    private function pastikanPemilik(Pesanan $pesanan): void
    {
        $this->authorize('view', $pesanan);
    }
}
