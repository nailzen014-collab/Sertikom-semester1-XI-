<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\Tiket;
use App\Models\TiketPaket;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $pesanan = $user->pesanan();

        return view('user.dashboard', [
            'page' => 'dashboard',
            'pageTitle' => 'Akun Pengunjung - The Nice Park',
            'totalPesanan' => (clone $pesanan)->count(),
            'pesananMenunggu' => (clone $pesanan)
                ->whereIn('status', [
                    Pesanan::STATUS_MENUNGGU_PERSETUJUAN,
                    Pesanan::STATUS_MENUNGGU_PEMBAYARAN,
                ])
                ->count(),
            'tiketTerbit' => Tiket::whereHas('pesanan', fn ($query) => $query
                ->where('user_id', $user->id)
                ->where('status', Pesanan::STATUS_LUNAS))
                ->count(),
        ]);
    }

    public function booking(Request $request): View
    {
        $user = $request->user();
        $rombonganDraft = $request->session()->get('pengajuan_rombongan_draft');

        return view('user.dashboard', [
            'page' => 'booking',
            'pageTitle' => 'Pilih Tiket - The Nice Park',
            'pesananUmum' => $user->pesanan()->where('kategori', TiketPaket::KATEGORI_UMUM)->dibuatBulanIni()->count(),
            'paketUmum' => TiketPaket::aktif()
                ->kategori(TiketPaket::KATEGORI_UMUM)
                ->orderBy('nama')
                ->get(),
            'paketRombongan' => TiketPaket::aktif()
                ->kategori(TiketPaket::KATEGORI_ROMBONGAN)
                ->orderBy('nama')
                ->get(),
            'rombonganDraft' => $rombonganDraft,
            'rombonganDraftPaket' => $rombonganDraft
                ? TiketPaket::findOrFail($rombonganDraft['tiket_paket_id'])
                : null,
        ]);
    }
}
