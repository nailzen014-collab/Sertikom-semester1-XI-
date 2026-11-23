<?php

namespace App\Http\Controllers;

use App\Models\Pesanan;
use App\Models\Tiket;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TiketController extends Controller
{
    public function show(Tiket $tiket): RedirectResponse
    {
        $this->pastikanPemilik($tiket);

        $this->pastikanSudahDibayar($tiket);

        return to_route('user.dashboard', ['pesanan' => $tiket->pesanan_id]);
    }

    public function pdf(Tiket $tiket): Response
    {
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
        abort_unless($tiket->pesanan->user_id === auth()->id(), 404);
    }

    private function pastikanSudahDibayar(Tiket $tiket): void
    {
        abort_unless($tiket->pesanan->status === Pesanan::STATUS_LUNAS, 404);
    }
}
