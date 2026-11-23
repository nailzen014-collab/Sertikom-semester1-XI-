<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RejectPengajuanRombonganRequest;
use App\Models\PengajuanRombongan;
use App\Models\Pesanan;
use App\Models\TiketPaket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PengajuanRombonganController extends Controller
{
    public function index(): View
    {
        return view('admin.pengajuan.index', [
            'pengajuan' => PengajuanRombongan::with([
                'pesanan.user',
                'pesanan.tiketPaket',
            ])
                ->where('status', PengajuanRombongan::STATUS_DIAJUKAN)
                ->latest()
                ->paginate(15),
        ]);
    }

    public function approve(Pesanan $pesanan): RedirectResponse
    {
        DB::transaction(function () use ($pesanan): void {
            $pesanan = Pesanan::whereKey($pesanan->id)->lockForUpdate()->firstOrFail();
            $pengajuan = $pesanan->pengajuanRombongan()->lockForUpdate()->first();

            abort_unless(
                $pesanan->kategori === TiketPaket::KATEGORI_ROMBONGAN
                    && $pesanan->status === Pesanan::STATUS_MENUNGGU_PERSETUJUAN
                    && $pengajuan?->status === PengajuanRombongan::STATUS_DIAJUKAN,
                409
            );

            if ($pesanan->pesertaRombongan()->count() !== $pesanan->jumlah_tiket) {
                throw ValidationException::withMessages([
                    'pengajuan' => 'Jumlah biodata peserta tidak sesuai dengan jumlah tiket.',
                ]);
            }

            $pengajuan->update([
                'admin_id' => auth()->id(),
                'status' => PengajuanRombongan::STATUS_DISETUJUI,
                'catatan_admin' => null,
                'ditinjau_pada' => now(),
            ]);

            $pesanan->update([
                'status' => Pesanan::STATUS_MENUNGGU_PEMBAYARAN,
            ]);
        });

        return redirect()->to(route('admin.dashboard').'#pengajuan')
            ->with('message', 'Pengajuan disetujui. Pengunjung sekarang dapat melakukan pembayaran.');
    }

    public function reject(RejectPengajuanRombonganRequest $request, Pesanan $pesanan): RedirectResponse
    {
        DB::transaction(function () use ($request, $pesanan): void {
            $pesanan = Pesanan::whereKey($pesanan->id)->lockForUpdate()->firstOrFail();
            $pengajuan = $pesanan->pengajuanRombongan()->lockForUpdate()->first();

            abort_unless(
                $pesanan->kategori === TiketPaket::KATEGORI_ROMBONGAN
                    && $pesanan->status === Pesanan::STATUS_MENUNGGU_PERSETUJUAN
                    && $pengajuan?->status === PengajuanRombongan::STATUS_DIAJUKAN,
                409
            );

            $pengajuan->update([
                'admin_id' => $request->user()->id,
                'status' => PengajuanRombongan::STATUS_DITOLAK,
                'catatan_admin' => $request->validated('catatan_admin'),
                'ditinjau_pada' => now(),
            ]);

            $pesanan->update([
                'status' => Pesanan::STATUS_DITOLAK,
            ]);
        });

        return redirect()->to(route('admin.dashboard').'#pengajuan')
            ->with('message', 'Pengajuan ditolak dan alasan sudah disimpan.');
    }
}
