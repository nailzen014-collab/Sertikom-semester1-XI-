<?php

namespace App\Exports;

use App\Models\Pesanan;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PesananExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /**
     * @param  array<string, mixed>  $filters
     */
    public function __construct(protected array $filters = []) {}

    public function query(): Builder
    {
        return Pesanan::query()
            ->with(['user', 'tiketPaket', 'pembayaran'])
            ->when($this->filters['tanggal_mulai'] ?? null, fn (Builder $q, string $tgl) => $q->whereDate('tanggal_kunjungan', '>=', $tgl))
            ->when($this->filters['tanggal_selesai'] ?? null, fn (Builder $q, string $tgl) => $q->whereDate('tanggal_kunjungan', '<=', $tgl))
            ->when($this->filters['status'] ?? null, fn (Builder $q, string $status) => $q->where('status', $status))
            ->when($this->filters['kategori'] ?? null, fn (Builder $q, string $kat) => $q->where('kategori', $kat))
            ->latest('tanggal_kunjungan');
    }

    public function headings(): array
    {
        return [
            'Kode Pesanan',
            'Nama Pemesan',
            'Email',
            'Kategori',
            'Paket / Tiket',
            'Tanggal Kunjungan',
            'Jumlah Tiket',
            'Harga Satuan (Rp)',
            'Potongan Diskon (Rp)',
            'Total Bayar (Rp)',
            'Status',
            'Tanggal Dipesan',
        ];
    }

    /**
     * @param  Pesanan  $row
     * @return array<int, mixed>
     */
    public function map($row): array
    {
        return [
            $row->kode_pesanan,
            $row->user?->nama ?? '-',
            $row->user?->email ?? '-',
            ucfirst($row->kategori),
            $row->tiketPaket?->nama ?? '-',
            $row->tanggal_kunjungan?->format('d/m/Y') ?? '-',
            $row->jumlah_tiket,
            $row->harga_satuan,
            $row->potongan_diskon,
            $row->total_bayar,
            Pesanan::STATUS[$row->status] ?? $row->status,
            $row->created_at?->format('d/m/Y H:i') ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1C4335']]],
        ];
    }
}
