<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Diskon;
use App\Models\Pembayaran;
use App\Models\PengajuanRombongan;
use App\Models\Pesanan;
use App\Models\Tiket;
use App\Models\TiketPaket;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $now = now();
        $awalPeriode = $now->copy()->startOfMonth()->subMonths(5);
        $monthExpression = match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', dibayar_pada)",
            'pgsql' => "TO_CHAR(dibayar_pada, 'YYYY-MM')",
            'sqlsrv' => "FORMAT(dibayar_pada, 'yyyy-MM')",
            default => "DATE_FORMAT(dibayar_pada, '%Y-%m')",
        };
        $pendapatanPerBulan = Pembayaran::query()
            ->where('status', Pembayaran::STATUS_BERHASIL)
            ->whereBetween('dibayar_pada', [$awalPeriode, $now])
            ->selectRaw("$monthExpression as bulan, SUM(jumlah) as pendapatan")
            ->groupByRaw($monthExpression)
            ->pluck('pendapatan', 'bulan');
        $bulan = collect(range(0, 5))->map(function (int $offset) use ($awalPeriode, $pendapatanPerBulan): array {
            $tanggal = $awalPeriode->copy()->addMonths($offset);
            $key = $tanggal->format('Y-m');

            return [
                'label' => $tanggal->translatedFormat('M'),
                'pendapatan' => (int) $pendapatanPerBulan->get($key, 0),
            ];
        });

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
            'statistikBulanan' => $bulan,
            'pendapatanMaksimum' => max(1, (int) $bulan->max('pendapatan')),
        ]);
    }
}
