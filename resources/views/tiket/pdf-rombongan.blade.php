<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>E-Ticket Rombongan {{ $pesanan->kode_pesanan }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; margin: 0; padding: 24px; }
        .ticket { border: 2px solid #1c4335; border-radius: 10px; overflow: hidden; }
        .head { background: #1c4335; color: #fbf7ef; padding: 18px 24px; }
        .head h1 { margin: 0; font-size: 20px; }
        .head p { margin: 4px 0 0; font-size: 11px; opacity: 0.9; }
        .body { padding: 24px; }
        .center { text-align: center; }
        .kode { font-family: DejaVu Sans Mono, monospace; font-size: 20px; font-weight: bold; color: #1c4335; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; font-size: 12px; }
        td, th { padding: 6px 8px; vertical-align: top; }
        td.label { color: #6b7280; width: 35%; }
        .peserta-table { margin-top: 16px; border: 1px solid #d1d5db; }
        .peserta-table th { background: #f2e8d7; text-align: left; font-size: 11px; }
        .peserta-table td { border-top: 1px solid #e5e7eb; font-size: 11px; }
        .note { margin-top: 18px; padding-top: 12px; border-top: 1px dashed #d1d5db; font-size: 10px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="head">
            <h1>The Nice Park &middot; Rumpin</h1>
            <p>E-Ticket Induk Kunjungan Rombongan Sekolah</p>
        </div>

        <div class="body">
            <div class="center">
                <img src="{{ $qr }}" alt="QR {{ $tiket->kode_tiket }}" width="180" height="180">
                <div class="kode">{{ $tiket->kode_tiket }}</div>
            </div>

            <table>
                <tr>
                    <td class="label">Sekolah / Instansi</td>
                    <td><strong>{{ $pesanan->pengajuanRombongan?->nama_sekolah ?? $pesanan->user->nama }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Penanggung Jawab</td>
                    <td>{{ $pesanan->pengajuanRombongan?->penanggung_jawab ?? $pesanan->user->nama }} ({{ $pesanan->pengajuanRombongan?->no_hp ?? '-' }})</td>
                </tr>
                <tr>
                    <td class="label">Paket Wisata</td>
                    <td>{{ $pesanan->tiketPaket->nama }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal Kunjungan</td>
                    <td>{{ $pesanan->tanggal_kunjungan->translatedFormat('l, d F Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Kode Pesanan</td>
                    <td><code>{{ $pesanan->kode_pesanan }}</code></td>
                </tr>
                <tr>
                    <td class="label">Total Peserta</td>
                    <td><strong>{{ $pesanan->jumlah_tiket }} orang</strong></td>
                </tr>
            </table>

            <table class="peserta-table">
                <thead>
                    <tr>
                        <th style="width: 10%;">No</th>
                        <th>Nama Siswa / Peserta</th>
                        <th style="width: 25%;">Kelas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pesanan->pesertaRombongan as $idx => $p)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ $p->nama }}</td>
                            <td>{{ $p->kelas ?: '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p class="note">
                Tunjukkan e-ticket rombongan ini kepada petugas tiket di pintu masuk The Nice Park. Tiket mencakup seluruh rombongan dan hanya berlaku pada tanggal kunjungan yang ditentukan.
            </p>
        </div>
    </div>
</body>
</html>
