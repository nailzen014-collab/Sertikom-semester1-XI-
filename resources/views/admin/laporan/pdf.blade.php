<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Rekap Pesanan - The Nice Park</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; margin: 0; padding: 20px; font-size: 11px; }
        .header { border-bottom: 2px solid #1c4335; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { margin: 0; font-size: 18px; color: #1c4335; }
        .header p { margin: 4px 0 0; color: #6b7280; font-size: 11px; }
        .summary-box { margin-bottom: 16px; padding: 10px; background: #f2e8d7; border-radius: 6px; }
        .summary-box table { width: 100%; border: none; }
        .summary-box td { font-size: 11px; padding: 3px 6px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.data th, table.data td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: left; }
        table.data th { background: #1c4335; color: #ffffff; font-weight: bold; font-size: 10px; }
        table.data tr:nth-child(even) { background: #fbf7ef; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .footer { margin-top: 20px; font-size: 9px; color: #9ca3af; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>The Nice Park &middot; Rumpin</h1>
        <p>Laporan Rekap Pesanan &amp; Pendapatan Kunjungan Wisata</p>
        <p>Periode: {{ $periode }} &middot; Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

    <div class="summary-box">
        <table>
            <tr>
                <td><strong>Total Pesanan:</strong> {{ number_format($pesanan->count(), 0, ',', '.') }} transaksi</td>
                <td><strong>Total Tiket:</strong> {{ number_format($pesanan->sum('jumlah_tiket'), 0, ',', '.') }} orang</td>
                <td class="text-right"><strong>Total Pendapatan (Lunas):</strong> Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Pemesan</th>
                <th>Kategori</th>
                <th>Paket</th>
                <th>Tgl Kunjungan</th>
                <th class="text-center">Jml</th>
                <th class="text-right">Total Bayar</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pesanan as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><code>{{ $item->kode_pesanan }}</code></td>
                    <td>{{ $item->user->nama ?? '-' }}</td>
                    <td>{{ ucfirst($item->kategori) }}</td>
                    <td>{{ $item->tiketPaket->nama ?? '-' }}</td>
                    <td>{{ $item->tanggal_kunjungan?->format('d/m/Y') }}</td>
                    <td class="text-center">{{ $item->jumlah_tiket }}</td>
                    <td class="text-right">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                    <td>{{ \App\Models\Pesanan::STATUS[$item->status] ?? $item->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data pesanan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dokumen ini dihasilkan secara otomatis oleh Sistem Pemesanan Tiket The Nice Park.
    </div>
</body>
</html>
