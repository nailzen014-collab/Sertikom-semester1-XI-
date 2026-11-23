@extends('layouts.admin-dashboard')

@section('title', 'Dashboard Admin - The Nice Park')

@section('content')
    @if (session('message'))
        <div role="status" class="rounded-md border border-juniper-200 bg-juniper-50 px-4 py-3 text-sm text-juniper-800">{{ session('message') }}</div>
    @endif
    @if ($errors->any())
        <div role="alert" class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
        </div>
    @endif

    <section id="ringkasan" aria-label="Ringkasan">
        <div class="admin-metrics">
        <article class="admin-metric">
            <div class="admin-metric-icon admin-metric-icon-green" aria-hidden="true">▣</div>
            <p>Total pesanan</p>
            <strong>{{ number_format($totalPesanan, 0, ',', '.') }}</strong>
            <span>Semua status</span>
        </article>
        <article class="admin-metric admin-metric-toffee">
            <div class="admin-metric-icon admin-metric-icon-toffee" aria-hidden="true">Rp</div>
            <p>Pendapatan bulan ini</p>
            <strong>Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</strong>
            <span>Dari pembayaran terverifikasi</span>
        </article>
        <article class="admin-metric">
            <div class="admin-metric-icon admin-metric-icon-green" aria-hidden="true">◇</div>
            <p>E-ticket terbit</p>
            <strong>{{ number_format($tiketTerbit, 0, ',', '.') }}</strong>
            <span>{{ $tiketAktif }} paket aktif tersedia</span>
        </article>
        <article class="admin-metric">
            <div class="admin-metric-icon admin-metric-icon-toffee" aria-hidden="true">!</div>
            <p>Perlu ditinjau</p>
            <strong>{{ number_format($pengajuanMenunggu, 0, ',', '.') }}</strong>
            <span><a href="#pengajuan">Pengajuan rombongan</a></span>
        </article>
        </div>

    <div class="admin-dashboard-grid">
        <section class="admin-panel admin-orders-panel">
            <div class="admin-panel-heading">
                <div>
                    <h2>Pesanan terbaru</h2>
                    <p>Aktivitas pesanan pengunjung</p>
                </div>
                <a href="#pesanan">Semua pesanan</a>
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
                    <h2>Pendapatan</h2>
                    <p>Enam bulan terakhir</p>
                </div>
                <span class="admin-chart-key"><span aria-hidden="true"></span> Pembayaran berhasil</span>
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
        <a href="#tiket-paket"><span aria-hidden="true">◇</span><span><strong>Kelola tiket &amp; paket</strong><small>{{ $totalTiket }} produk, {{ $tiketAktif }} aktif</small></span><b aria-hidden="true">→</b></a>
        <a href="#diskon"><span aria-hidden="true">%</span><span><strong>Kelola diskon</strong><small>{{ $diskonAktif }} diskon aktif</small></span><b aria-hidden="true">→</b></a>
        <a href="#validasi-tiket"><span aria-hidden="true">✓</span><span><strong>Validasi tiket masuk</strong><small>Periksa kode dan tanggal kunjungan</small></span><b aria-hidden="true">→</b></a>
    </section>
    </section>

    @include('admin.dashboard-sections.pengajuan')
    @include('admin.dashboard-sections.pesanan')
    @include('admin.dashboard-sections.tiket-paket')
    @include('admin.dashboard-sections.diskon')
    @include('admin.dashboard-sections.validasi-tiket')
@endsection
