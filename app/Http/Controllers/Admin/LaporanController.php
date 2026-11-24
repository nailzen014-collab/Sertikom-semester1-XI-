<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PesananExport;
use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LaporanController extends Controller
{
    public function exportExcel(Request $request): BinaryFileResponse
    {
        $filters = $request->only(['tanggal_mulai', 'tanggal_selesai', 'status', 'kategori']);
        $filename = 'laporan-pesanan-'.now()->format('Ymd-His').'.xlsx';

        return Excel::download(new PesananExport($filters), $filename);
    }

    public function exportPdf(Request $request): Response
    {
        $filters = $request->only(['tanggal_mulai', 'tanggal_selesai', 'status', 'kategori']);

        $query = Pesanan::query()
            ->with(['user', 'tiketPaket', 'pembayaran'])
            ->when($filters['tanggal_mulai'] ?? null, fn (Builder $q, string $tgl) => $q->whereDate('tanggal_kunjungan', '>=', $tgl))
            ->when($filters['tanggal_selesai'] ?? null, fn (Builder $q, string $tgl) => $q->whereDate('tanggal_kunjungan', '<=', $tgl))
            ->when($filters['status'] ?? null, fn (Builder $q, string $status) => $q->where('status', $status))
            ->when($filters['kategori'] ?? null, fn (Builder $q, string $kat) => $q->where('kategori', $kat))
            ->latest('tanggal_kunjungan');

        $pesanan = $query->get();
        $totalPendapatan = $pesanan->where('status', Pesanan::STATUS_LUNAS)->sum('total_bayar');

        $periode = 'Semua Tanggal';
        if (! empty($filters['tanggal_mulai']) && ! empty($filters['tanggal_selesai'])) {
            $periode = $filters['tanggal_mulai'].' s/d '.$filters['tanggal_selesai'];
        } elseif (! empty($filters['tanggal_mulai'])) {
            $periode = 'Mulai '.$filters['tanggal_mulai'];
        } elseif (! empty($filters['tanggal_selesai'])) {
            $periode = 'Sampai '.$filters['tanggal_selesai'];
        }

        $pdf = Pdf::loadView('admin.laporan.pdf', [
            'pesanan' => $pesanan,
            'totalPendapatan' => $totalPendapatan,
            'periode' => $periode,
        ])->setPaper('a4', 'landscape');

        return $pdf->download('laporan-pesanan-'.now()->format('Ymd-His').'.pdf');
    }
}
