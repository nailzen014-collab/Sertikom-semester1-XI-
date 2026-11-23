<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diskon;
use App\Models\Pembayaran;
use App\Models\PengajuanRombongan;
use App\Models\Pesanan;
use App\Models\Tiket;
use App\Models\TiketPaket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $now = now();
        $awalPeriode = $now->copy()->startOfMonth()->subMonths(5);
        $bulan = collect(range(0, 5))->map(function (int $offset) use ($awalPeriode): array {
            $tanggal = $awalPeriode->copy()->addMonths($offset);

            return [
                'key' => $tanggal->format('Y-m'),
                'label' => $tanggal->translatedFormat('M'),
                'pendapatan' => 0,
            ];
        })->keyBy('key');

        Pembayaran::query()
            ->where('status', Pembayaran::STATUS_BERHASIL)
            ->whereNotNull('dibayar_pada')
            ->whereBetween('dibayar_pada', [$awalPeriode, $now])
            ->get(['jumlah', 'dibayar_pada'])
            ->each(function (Pembayaran $pembayaran) use ($bulan): void {
                $key = Carbon::parse($pembayaran->dibayar_pada)->format('Y-m');

                if ($bulan->has($key)) {
                    $data = $bulan->get($key);
                    $data['pendapatan'] += $pembayaran->jumlah;
                    $bulan->put($key, $data);
                }
            });

        $pengajuan = PengajuanRombongan::with([
            'pesanan.user',
            'pesanan.tiketPaket',
            'pesanan.pesertaRombongan',
        ])
            ->where('status', PengajuanRombongan::STATUS_DIAJUKAN)
            ->latest()
            ->paginate(10, ['*'], 'pengajuan_page')
            ->fragment('pengajuan');

        $pesananAdmin = Pesanan::with([
            'user',
            'tiketPaket',
            'diskon',
            'pembayaran',
            'tiket',
            'pengajuanRombongan',
            'pesertaRombongan',
        ])
            ->when($request->filled('tanggal'), fn ($query) => $query->whereDate('tanggal_kunjungan', $request->input('tanggal')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('kategori'), fn ($query) => $query->where('kategori', $request->input('kategori')))
            ->latest()
            ->paginate(15, ['*'], 'pesanan_page')
            ->withQueryString()
            ->fragment('pesanan');

        $tiketValidasi = session('tiket_validasi_id')
            ? Tiket::with(['pesanan.user', 'pesanan.tiketPaket', 'peserta'])
                ->find(session('tiket_validasi_id'))
            : null;

        return view('admin.dashboard', [
            'totalTiket' => TiketPaket::count(),
            'tiketAktif' => TiketPaket::aktif()->count(),
            'diskonAktif' => Diskon::aktif()->count(),
            'totalPesanan' => Pesanan::count(),
            'pendapatanBulanIni' => Pembayaran::where('status', Pembayaran::STATUS_BERHASIL)
                ->whereBetween('dibayar_pada', [$now->copy()->startOfMonth(), $now])
                ->sum('jumlah'),
            'tiketTerbit' => Tiket::count(),
            'pengajuanMenunggu' => PengajuanRombongan::where('status', PengajuanRombongan::STATUS_DIAJUKAN)->count(),
            'pesananTerbaru' => Pesanan::with(['user', 'tiketPaket'])
                ->latest('id')
                ->limit(6)
                ->get(),
            'statistikBulanan' => $bulan->values(),
            'pendapatanMaksimum' => max(1, (int) $bulan->max('pendapatan')),
            'pengajuan' => $pengajuan,
            'pesananAdmin' => $pesananAdmin,
            'statusPesanan' => Pesanan::STATUS,
            'kategoriTiket' => TiketPaket::KATEGORI,
            'paketAdmin' => TiketPaket::latest()->paginate(10, ['*'], 'paket_page')->fragment('tiket-paket'),
            'diskonAdmin' => Diskon::with('admin')->latest()->paginate(10, ['*'], 'diskon_page')->fragment('diskon'),
            'tiketValidasi' => $tiketValidasi,
        ]);
    }
}
