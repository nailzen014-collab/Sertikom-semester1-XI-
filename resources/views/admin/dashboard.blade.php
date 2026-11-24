@extends('layouts.admin-dashboard')

@section('title', 'Dashboard Admin - The Nice Park')
@section('page-title', 'Dashboard Admin')
@section('page-description', 'Ringkasan pesanan, pendapatan, dan aktivitas kunjungan.')
@section('page-action')
    <a href="{{ route('admin.pengajuan.index') }}" class="admin-heading-action">Tinjau pengajuan</a>
@endsection

@section('content')
    <section id="ringkasan" aria-label="Ringkasan">
        <div class="admin-metrics">
        <article class="admin-metric">
            <div class="admin-metric-icon admin-metric-icon-green" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"/><path d="M9 8h6M9 12h6"/></svg>
            </div>
            <p>Total pesanan</p>
            <strong>{{ number_format($totalPesanan, 0, ',', '.') }}</strong>
            <span>Semua status</span>
        </article>
        <article class="admin-metric admin-metric-toffee">
            <div class="admin-metric-icon admin-metric-icon-toffee" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18"/><circle cx="12" cy="14" r="1.4"/></svg>
            </div>
            <p>Pendapatan bulan ini</p>
            <strong>Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</strong>
            <span>Dari pembayaran terverifikasi</span>
        </article>
        <article class="admin-metric">
            <div class="admin-metric-icon admin-metric-icon-green" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4z"/><path d="M13 6v12"/></svg>
            </div>
            <p>E-ticket terbit</p>
            <strong>{{ number_format($tiketTerbit, 0, ',', '.') }}</strong>
            <span>{{ $tiketAktif }} paket aktif tersedia</span>
        </article>
        <article class="admin-metric">
            <div class="admin-metric-icon admin-metric-icon-toffee" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v6"/><path d="M12 16.5v.01"/></svg>
            </div>
            <p>Perlu ditinjau</p>
            <strong>{{ number_format($pengajuanMenunggu, 0, ',', '.') }}</strong>
            <span><a href="{{ route('admin.pengajuan.index') }}">Pengajuan rombongan</a></span>
        </article>
        </div>

    <div class="admin-dashboard-grid">
        <section class="admin-panel admin-orders-panel">
            <div class="admin-panel-heading">
                <div>
                    <h2>Pesanan terbaru</h2>
                    <p>Aktivitas pesanan pengunjung</p>
                </div>
                <a href="{{ route('admin.pesanan.index') }}">Semua pesanan</a>
            </div>
            <div class="admin-table-scroll" role="region" aria-label="Pesanan terbaru" tabindex="0">
                <table class="admin-orders-table">
                    <thead>
                        <tr>
                            <th scope="col">Pemesan</th>
                            <th scope="col">Paket</th>
                            <th scope="col">Tanggal kunjungan</th>
                            <th scope="col">Jumlah</th>
                            <th scope="col">Total</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pesananTerbaru as $pesanan)
                            <tr>
                                <td>
                                    <span class="admin-order-name">{{ $pesanan->user->nama }}</span>
                                    <span class="admin-order-code">{{ $pesanan->kode_pesanan }}</span>
                                </td>
                                <td>{{ $pesanan->tiketPaket->nama }}</td>
                                <td>{{ $pesanan->tanggal_kunjungan->format('d M Y') }}</td>
                                <td>{{ $pesanan->jumlah_tiket }}</td>
                                <td>Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</td>
                                <td><x-status-badge :status="$pesanan->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="admin-empty-row">Belum ada pesanan. Pesanan baru akan tampil di sini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-panel admin-chart-panel">
            <div class="admin-panel-heading">
                <div>
                    <h2>Tren Pendapatan</h2>
                    <p>Enam bulan terakhir</p>
                </div>
                <span class="admin-chart-key"><span aria-hidden="true"></span> Pembayaran berhasil</span>
            </div>

            @php
                $points = [];
                $count = count($statistikBulanan);
                $chartWidth = 500;
                $chartHeight = 140;
                $paddingX = 30;
                $paddingY = 20;
                $usableWidth = $chartWidth - ($paddingX * 2);
                $usableHeight = $chartHeight - ($paddingY * 2);

                foreach ($statistikBulanan as $i => $item) {
                    $x = $paddingX + ($count > 1 ? ($i / ($count - 1)) * $usableWidth : 0);
                    $ratio = $pendapatanMaksimum > 0 ? ($item['pendapatan'] / $pendapatanMaksimum) : 0;
                    $y = ($chartHeight - $paddingY) - ($ratio * $usableHeight);
                    $points[] = ['x' => round($x, 1), 'y' => round($y, 1), 'item' => $item];
                }
                $polylinePoints = implode(' ', array_map(fn($p) => "{$p['x']},{$p['y']}", $points));
                $areaPoints = "{$points[0]['x']},".($chartHeight - $paddingY)." {$polylinePoints} ".(end($points)['x']).",".($chartHeight - $paddingY);
            @endphp

            <div class="w-full mb-3 px-2">
                <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight }}" class="w-full h-28 overflow-visible" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="revenueGradient" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#e8a33c" stop-opacity="0.35"/>
                            <stop offset="100%" stop-color="#e8a33c" stop-opacity="0.0"/>
                        </linearGradient>
                    </defs>
                    <polygon points="{{ $areaPoints }}" fill="url(#revenueGradient)" />
                    <polyline fill="none" stroke="#e8a33c" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" points="{{ $polylinePoints }}" />
                    @foreach ($points as $p)
                        <circle cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4" fill="#ffffff" stroke="#1c4335" stroke-width="2" class="hover:r-6 transition-all cursor-pointer">
                            <title>{{ $p['item']['label'] }}: Rp {{ number_format($p['item']['pendapatan'], 0, ',', '.') }}</title>
                        </circle>
                    @endforeach
                </svg>
            </div>

            <figure class="admin-chart" role="img" aria-label="Grafik pendapatan pembayaran berhasil selama enam bulan terakhir">
                <div class="admin-chart-grid">
                    @foreach ($statistikBulanan as $bulan)
                        @php($tinggi = max(4, (int) round($bulan['pendapatan'] / $pendapatanMaksimum * 100)))
                        <div class="admin-chart-column">
                            <span class="admin-chart-value">Rp {{ number_format($bulan['pendapatan'], 0, ',', '.') }}</span>
                            <div class="admin-chart-track">
                                <span class="admin-chart-bar" style="height: {{ $tinggi }}%"></span>
                            </div>
                            <span class="admin-chart-month">{{ $bulan['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                <figcaption class="admin-chart-caption">Pendapatan dihitung dari transaksi yang statusnya berhasil di Midtrans.</figcaption>
            </figure>
        </section>
    </div>

    <section class="admin-quick-links" aria-label="Aksi pengelolaan">
        <a href="{{ route('admin.tiket-paket.index') }}"><span class="admin-quick-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4z"/><path d="M13 6v12"/></svg></span><span><strong>Kelola tiket &amp; paket</strong><small>{{ $totalTiket }} produk, {{ $tiketAktif }} aktif</small></span><b aria-hidden="true">→</b></a>
        <a href="{{ route('admin.diskon.index') }}"><span class="admin-quick-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="m19 5-14 14"/><circle cx="7.5" cy="7.5" r="2.5"/><circle cx="16.5" cy="16.5" r="2.5"/></svg></span><span><strong>Kelola diskon</strong><small>{{ $diskonAktif }} diskon aktif</small></span><b aria-hidden="true">→</b></a>
        <a href="{{ route('admin.tiket.validasi') }}"><span class="admin-quick-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4-3 6.5-7 8-4-1.5-7-4-7-8V6z"/><path d="m9 12 2 2 4-4"/></svg></span><span><strong>Validasi tiket masuk</strong><small>Periksa kode dan tanggal kunjungan</small></span><b aria-hidden="true">→</b></a>
    </section>
    </section>
@endsection
