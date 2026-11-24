<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Tiket;
use App\Models\TiketPaket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TiketController extends Controller
{
    public function show(Tiket $tiket): RedirectResponse
    {
        $tiket->load('pesanan.user');

        $this->pastikanPemilik($tiket);

        $this->pastikanSudahDibayar($tiket);

        return to_route('user.orders', ['pesanan' => $tiket->pesanan_id]);
    }

    public function pdf(Tiket $tiket): Response
    {
        $tiket->load('pesanan.user');

        $this->pastikanPemilik($tiket);

        $this->pastikanSudahDibayar($tiket);
        $tiket->load([
            'pesanan.tiketPaket',
            'pesanan.user',
            'pesanan.pesertaRombongan',
            'pesanan.pengajuanRombongan',
            'peserta',
        ]);

        $pdf = Pdf::loadView('tiket.pdf', [
            'tiket' => $tiket,
            'qr' => $this->qrDataUri($tiket->kode_tiket),
        ]);

        return $pdf->download('tiket-'.$tiket->kode_tiket.'.pdf');
    }

    public function pdfRombongan(Pesanan $pesanan): Response
    {
        abort_unless($pesanan->user_id === auth()->id(), 404);
        abort_unless($pesanan->status === Pesanan::STATUS_LUNAS, 404);
        abort_unless($pesanan->kategori === TiketPaket::KATEGORI_ROMBONGAN, 404);

        $pesanan->load(['tiketPaket', 'user', 'pesertaRombongan', 'pengajuanRombongan', 'tiket']);

        // Cari tiket utama jenis rombongan, atau tiket pertama
        $tiketUtama = $pesanan->tiket->firstWhere('jenis', Tiket::JENIS_ROMBONGAN) ?? $pesanan->tiket->first();

        abort_unless($tiketUtama !== null, 404);

        $pdf = Pdf::loadView('tiket.pdf-rombongan', [
            'pesanan' => $pesanan,
            'tiket' => $tiketUtama,
            'qr' => $this->qrDataUri($tiketUtama->kode_tiket),
        ]);

        return $pdf->download('tiket-rombongan-'.$pesanan->kode_pesanan.'.pdf');
    }

    private function qrDataUri(string $kode): string
    {
        $hasil = QrCode::format('svg')
            ->size(300)
            ->margin(10)
            ->generate($kode);

        return 'data:image/svg+xml;base64,'.base64_encode((string) $hasil);
    }

    private function pastikanPemilik(Tiket $tiket): void
    {
        $this->authorize('view', $tiket->pesanan);
    }

    private function pastikanSudahDibayar(Tiket $tiket): void
    {
        abort_unless($tiket->pesanan->status === Pesanan::STATUS_LUNAS, 404);
    }
}
