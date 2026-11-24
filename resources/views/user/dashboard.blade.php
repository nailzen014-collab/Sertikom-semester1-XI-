@extends('layouts.user-dashboard')

@section('title', $pageTitle)

@section('content')
    @if (session('message'))
        <p class="user-notice" role="status">{{ session('message') }}</p>
    @endif
    @if (session('error'))
        <p class="user-notice user-notice-error" role="alert">{{ session('error') }}</p>
    @endif
    @if ($errors->any())
        <div class="user-notice user-notice-error" role="alert">
            <p class="font-semibold">Data belum dapat diproses.</p>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($page === 'dashboard')
        <div class="user-welcome" id="ringkasan">
            <div>
                <p class="user-eyebrow">Area pengunjung</p>
                <h1>Halo, {{ auth()->user()->nama }}</h1>
                <p>Kelola pesanan dan kunjungan Anda dari akun ini.</p>
            </div>
            <a href="{{ route('user.booking') }}" class="user-primary-action">Pesan tiket</a>
        </div>

        <section class="user-summary" aria-label="Ringkasan akun">
            <article class="user-summary-card">
                <span class="user-summary-label">Total pesanan</span>
                <strong>{{ number_format($totalPesanan, 0, ',', '.') }}</strong>
                <span class="user-summary-note">Riwayat di akun ini</span>
            </article>
            <article class="user-summary-card user-summary-card-toffee">
                <span class="user-summary-label">Perlu ditindaklanjuti</span>
                <strong>{{ number_format($pesananMenunggu, 0, ',', '.') }}</strong>
                <span class="user-summary-note">Menunggu persetujuan atau pembayaran</span>
            </article>
            <article class="user-summary-card">
                <span class="user-summary-label">E-tiket terbit</span>
                <strong>{{ number_format($tiketTerbit, 0, ',', '.') }}</strong>
                <span class="user-summary-note">Tiket dari pembayaran berhasil</span>
            </article>
        </section>
    @endif

    @if ($page === 'orders')
        <section class="user-orders" id="pesanan">
        <div class="user-section-heading">
            <div>
                <h2>Pesanan saya</h2>
                <p>Buka pesanan untuk melihat status, pembayaran, dan e-tiket.</p>
            </div>
            <span class="user-section-count">{{ $pesanan->total() }} pesanan</span>
        </div>

        @forelse ($pesanan as $item)
            <details class="user-order-disclosure" id="pesanan-{{ $item->id }}">
                <summary class="user-order-summary">
                    <span class="user-order-main">
                        <strong>{{ $item->tiketPaket->nama }}</strong>
                        <small>{{ $item->kode_pesanan }} · {{ $item->tanggal_kunjungan->format('d M Y') }}</small>
                    </span>
                    <span class="user-order-meta">
                        <span>{{ $item->jumlah_tiket }} tiket</span>
                        <span class="user-state user-state-{{ str_replace('_', '-', $item->status) }}">
                            {{ \App\Models\Pesanan::STATUS[$item->status] ?? ucfirst($item->status) }}
                        </span>
                        <a href="{{ route('riwayat.show', $item) }}" class="user-order-detail-btn">Detail</a>
                    </span>
                </summary>

                <div class="user-order-detail">
                    <dl class="user-order-facts">
                        <div><dt>Tanggal kunjungan</dt><dd>{{ $item->tanggal_kunjungan->format('d M Y') }}</dd></div>
                        <div><dt>Jumlah tiket</dt><dd>{{ $item->jumlah_tiket }} orang</dd></div>
                        <div><dt>Harga per tiket</dt><dd>Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</dd></div>
                        <div><dt>Potongan</dt><dd>Rp {{ number_format($item->potongan_diskon, 0, ',', '.') }}</dd></div>
                    </dl>

                    @if ($item->pengajuanRombongan)
                        <section class="user-inline-section">
                            <h3>Informasi rombongan</h3>
                            <dl class="user-order-facts">
                                <div><dt>Sekolah</dt><dd>{{ $item->pengajuanRombongan->nama_sekolah }}</dd></div>
                                <div><dt>Penanggung jawab</dt><dd>{{ $item->pengajuanRombongan->penanggung_jawab }}</dd></div>
                                <div><dt>Nomor telepon</dt><dd>{{ $item->pengajuanRombongan->no_hp }}</dd></div>
                            </dl>
                            @if ($item->pengajuanRombongan->catatan_admin)
                                <p class="user-admin-note"><strong>Catatan admin:</strong> {{ $item->pengajuanRombongan->catatan_admin }}</p>
                            @endif
                            @if ($item->pesertaRombongan->isNotEmpty())
                                <details class="user-participant-list">
                                    <summary>Lihat &amp; Kelola {{ $item->pesertaRombongan->count() }} peserta</summary>
                                    @php($bisaUbah = ! $item->tanggal_kunjungan->isPast() || $item->tanggal_kunjungan->isToday())
                                    @if ($bisaUbah)
                                        <p class="mt-2 text-xs text-gray-500">Anda dapat mengubah nama/kelas peserta sebelum hari kunjungan berakhir.</p>
                                    @endif
                                    <ul class="mt-2 divide-y divide-gray-100">
                                        @foreach ($item->pesertaRombongan as $peserta)
                                            <li class="py-2 flex items-center justify-between gap-3 text-sm" x-data="{ editing: false, nama: '{{ addslashes($peserta->nama) }}', kelas: '{{ addslashes($peserta->kelas ?? '') }}' }">
                                                <div x-show="!editing" class="flex-1">
                                                    <strong>{{ $peserta->nama }}</strong>
                                                    @if ($peserta->kelas)
                                                        <span class="text-xs text-gray-500">&middot; Kelas {{ $peserta->kelas }}</span>
                                                    @endif
                                                </div>
                                                @if ($bisaUbah)
                                                    <div x-show="editing" class="flex-1">
                                                        <form method="POST" action="{{ route('pesan.rombongan.update-peserta', [$item, $peserta]) }}" class="flex flex-wrap items-center gap-2">
                                                            @csrf
                                                            <input type="text" name="nama" x-model="nama" required class="text-xs rounded border-gray-300 px-2 py-1" placeholder="Nama siswa">
                                                            <input type="text" name="kelas" x-model="kelas" class="text-xs rounded border-gray-300 px-2 py-1 w-24" placeholder="Kelas">
                                                            <button type="submit" class="text-xs rounded bg-juniper-600 px-2.5 py-1 text-white font-medium hover:bg-juniper-700">Simpan</button>
                                                            <button type="button" @click="editing = false" class="text-xs text-gray-500 hover:underline">Batal</button>
                                                        </form>
                                                    </div>
                                                    <button type="button" x-show="!editing" @click="editing = true" class="text-xs text-juniper-700 font-medium hover:underline">Ubah nama</button>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </details>
                            @endif
                        </section>
                    @endif

                    <div class="user-order-total">
                        <span>Total pembayaran</span>
                        <strong>Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</strong>
                    </div>

                    @if ($item->status === \App\Models\Pesanan::STATUS_MENUNGGU_PEMBAYARAN)
                        <form method="POST" action="{{ route('pembayaran.process', $item) }}" class="user-inline-payment swal-confirm" data-confirm-message="Lanjutkan pembayaran pesanan {{ $item->kode_pesanan }} sebesar Rp {{ number_format($item->total_bayar, 0, ',', '.') }} melalui Midtrans?" data-confirm-btn="Ya, Bayar">
                            @csrf
                            <p>Pembayaran aman diproses melalui Midtrans. E-tiket terbit setelah pembayaran terverifikasi.</p>
                            <button type="submit" class="user-primary-action">Bayar sekarang</button>
                        </form>
                    @endif

                    @if ($item->pembayaran->isNotEmpty())
                        <section class="user-inline-section">
                            <h3>Riwayat pembayaran</h3>
                            <ul class="user-payment-list">
                                @foreach ($item->pembayaran as $payment)
                                    <li>
                                        <span>{{ \App\Models\Pembayaran::STATUS[$payment->status] ?? ucfirst($payment->status) }}</span>
                                        <strong>Rp {{ number_format($payment->jumlah, 0, ',', '.') }}</strong>
                                        @if ($payment->dibayar_pada)
                                            <time datetime="{{ $payment->dibayar_pada->toIso8601String() }}">{{ $payment->dibayar_pada->format('d M Y, H:i') }}</time>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif

                    @if ($item->tiket->isNotEmpty())
                        <section class="user-inline-section">
                            <div class="flex items-center justify-between flex-wrap gap-2">
                                <div>
                                    <h3>E-tiket</h3>
                                    <p class="user-section-help">Tunjukkan kode ini saat masuk atau unduh PDF untuk menyimpan QR.</p>
                                </div>
                                @if ($item->kategori === \App\Models\TiketPaket::KATEGORI_ROMBONGAN)
                                    <div>
                                        <a href="{{ route('pesanan.pdf-rombongan', $item) }}" class="inline-flex items-center gap-1.5 rounded bg-juniper-700 px-3 py-1.5 text-xs font-semibold text-white hover:bg-juniper-800">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            Unduh E-Tiket Rombongan (Induk)
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <ul class="user-ticket-list mt-3">
                                @foreach ($item->tiket as $ticket)
                                    <li>
                                        <span class="user-ticket-code">{{ $ticket->kode_tiket }}</span>
                                        <span class="user-state user-state-{{ $ticket->status === 'aktif' ? 'aktif' : 'dipakai' }}">
                                            {{ $ticket->status === 'aktif' ? 'Belum dipakai' : 'Sudah dipakai' }}
                                        </span>
                                        <a href="{{ route('tiket.pdf', $ticket) }}" class="user-download-link">Unduh PDF</a>
                                    </li>
                                @endforeach
                            </ul>
                        </section>
                    @endif
                </div>
            </details>
        @empty
            <div class="user-empty-state">
                <h3>Belum ada pesanan</h3>
                <p>Pilih tiket untuk merencanakan kunjungan pertama Anda.</p>
                <a href="{{ route('user.booking') }}">Pilih tiket</a>
            </div>
        @endforelse

        @if ($pesanan->hasPages())
            <div class="user-pagination">{{ $pesanan->links() }}</div>
        @endif
        </section>
    @endif

    @if ($page === 'booking')
        <section class="user-booking" id="pilih-tiket">
        <div class="user-section-heading">
            <div>
                <h2>Pilih tiket untuk kunjungan berikutnya</h2>
                <p>Isi tanggal dan jumlah, lalu pesanan akan muncul di halaman ini. Maksimal {{ \App\Models\Pesanan::BATAS_PESANAN_UMUM_PER_AKUN }} pesanan umum per akun.</p>
            </div>
        </div>

        @if ($pesananUmum >= \App\Models\Pesanan::BATAS_PESANAN_UMUM_PER_AKUN)
            <p class="user-notice" role="status">Batas pesanan tiket umum di akun ini sudah tercapai. Pengajuan rombongan tetap tersedia.</p>
        @else
            <p class="user-section-count">Sisa {{ \App\Models\Pesanan::BATAS_PESANAN_UMUM_PER_AKUN - $pesananUmum }} pesanan umum</p>
        @endif

        @if ($paketUmum->isNotEmpty())
            <h3 class="user-booking-group-title">Tiket umum · 1–10 orang</h3>
            <div class="user-package-grid">
                @foreach ($paketUmum as $paket)
                    <article class="user-package-card" id="paket-{{ $paket->id }}">
                        <div>
                            <h4>{{ $paket->nama }}</h4>
                            <p class="user-package-price">Rp {{ number_format($paket->harga, 0, ',', '.') }} <span>/ orang</span></p>
                            @if ($paket->deskripsi)
                                <p class="user-package-description">{{ $paket->deskripsi }}</p>
                            @endif
                        </div>
                        @if ($pesananUmum < \App\Models\Pesanan::BATAS_PESANAN_UMUM_PER_AKUN)
                            <form method="POST" action="{{ route('pesan.store', $paket) }}" class="user-booking-form">
                                @csrf
                                <label>
                                    <span>Tanggal kunjungan</span>
                                    <input name="tanggal_kunjungan" type="date" min="{{ today()->toDateString() }}" value="{{ old('tanggal_kunjungan') }}" required>
                                </label>
                                <label>
                                    <span>Jumlah tiket</span>
                                    <input name="jumlah_tiket" type="number" min="1" max="10" value="{{ old('jumlah_tiket', 1) }}" required>
                                </label>
                                <p>Diskon yang tersedia diterapkan otomatis.</p>
                                <button type="submit" class="user-primary-action">Buat pesanan</button>
                            </form>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif

        @if ($paketRombongan->isNotEmpty())
            <details class="user-group-invite" id="paket-rombongan" @if ($rombonganDraft || request()->boolean('rombongan') || $errors->any()) open @endif>
                <summary>
                    <div>
                        <h3>Kunjungan sekolah atau rombongan</h3>
                        <p>Untuk 11 orang atau lebih. Isi data kunjungan dan peserta di sini; pembayaran dibuka setelah disetujui admin.</p>
                    </div>
                    <span class="user-secondary-action">Ajukan rombongan</span>
                </summary>
                <div class="user-group-application">
                    @if ($rombonganDraft)
                        @include('pesan.rombongan.peserta', ['draft' => $rombonganDraft, 'paket' => $rombonganDraftPaket])
                    @else
                        @include('pesan.rombongan.create', ['paket' => $paketRombongan])
                    @endif
                </div>
            </details>
        @endif
        </section>
    @endif
@endsection
