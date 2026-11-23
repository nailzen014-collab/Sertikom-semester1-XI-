<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>E-Ticket {{ $tiket->kode_tiket }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; color: #1f2937; margin: 0; padding: 24px; }
        .ticket { border: 2px solid #1f3d33; border-radius: 10px; overflow: hidden; }
        .head { background: #1f3d33; color: #f5edde; padding: 18px 24px; }
        .head h1 { margin: 0; font-size: 22px; }
        .head p { margin: 4px 0 0; font-size: 12px; }
        .body { padding: 24px; text-align: center; }
        .kode { font-family: DejaVu Sans Mono, monospace; font-size: 20px; font-weight: bold; color: #173028; margin: 12px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; text-align: left; font-size: 13px; }
        td { padding: 6px 0; vertical-align: top; }
        td.label { color: #6b7280; width: 42%; }
        .note { margin-top: 18px; padding-top: 12px; border-top: 1px dashed #d1d5db; font-size: 11px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="head">
            <h1>The Nice Park &middot; Rumpin</h1>
            <p>Playground &amp; Mini Zoo &middot; E-Ticket</p>
        </div>

        <div class="body">
            <img src="{{ $qr }}" alt="QR {{ $tiket->kode_tiket }}" width="200" height="200">
            <div class="kode">{{ $tiket->kode_tiket }}</div>

            <table>
                <tr>
                    <td class="label">{{ $tiket->jenis === \App\Models\Tiket::JENIS_ROMBONGAN ? 'Sekolah' : ($tiket->peserta ? 'Peserta' : 'Nama pemesan') }}</td>
                    <td>
                        @if ($tiket->jenis === \App\Models\Tiket::JENIS_ROMBONGAN)
                            {{ $tiket->pesanan->pengajuanRombongan->nama_sekolah }}
                        @elseif ($tiket->peserta)
                            {{ $tiket->peserta->nama }}{{ $tiket->peserta->kelas ? ' · '.$tiket->peserta->kelas : '' }}
                        @else
                            {{ $tiket->pesanan->user->nama }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">Paket</td>
                    <td>{{ $tiket->pesanan->tiketPaket->nama }}</td>
                </tr>
                <tr>
                    <td class="label">Tanggal kunjungan</td>
                    <td>{{ $tiket->pesanan->tanggal_kunjungan->format('d M Y') }}</td>
                </tr>
                <tr>
                    <td class="label">Kode pesanan</td>
                    <td>{{ $tiket->pesanan->kode_pesanan }}</td>
                </tr>
                <tr>
                    <td class="label">Jumlah peserta</td>
                    <td>{{ $tiket->pesanan->jumlah_tiket }}</td>
                </tr>
            </table>

            @if ($tiket->jenis === \App\Models\Tiket::JENIS_ROMBONGAN)
                <table>
                    <tr><td colspan="2"><strong>Daftar peserta</strong></td></tr>
                    @foreach ($tiket->pesanan->pesertaRombongan as $peserta)
                        <tr><td>{{ $peserta->nama }}</td><td>{{ $peserta->kelas ?: '-' }}</td></tr>
                    @endforeach
                </table>
            @endif

            <p class="note">
                Tunjukkan QR atau kode tiket ini di pintu masuk. Tiket hanya berlaku pada tanggal kunjungan
                yang tertera dan tidak dapat dipakai lebih dari satu kali.
            </p>
        </div>
    </div>
</body>
</html>
