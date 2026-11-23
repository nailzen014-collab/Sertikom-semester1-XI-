<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Pesanan') }}
            </h2>
            <a href="{{ route('riwayat.index') }}" class="text-sm text-juniper-700 hover:text-gold-600">&larr; Riwayat</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('message'))
                <div class="rounded-md bg-juniper-50 border border-juniper-200 text-juniper-800 px-4 py-3 text-sm">
                    {{ session('message') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-mono text-xs text-gray-500">{{ $pesanan->kode_pesanan }}</p>
                        <h3 class="mt-1 text-xl font-bold text-juniper-800">{{ $pesanan->tiketPaket->nama }}</h3>
                        <p class="text-sm text-gray-500">Kategori {{ \App\Models\TiketPaket::KATEGORI[$pesanan->kategori] ?? $pesanan->kategori }}</p>
                    </div>
                    <x-status-badge :status="$pesanan->status" />
                </div>

                <dl class="mt-6 grid gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <dt class="text-gray-500">Tanggal kunjungan</dt>
                        <dd class="font-medium text-gray-900">{{ $pesanan->tanggal_kunjungan->format('d M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Jumlah tiket</dt>
                        <dd class="font-medium text-gray-900">{{ $pesanan->jumlah_tiket }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Harga satuan</dt>
                        <dd class="font-medium text-gray-900">Rp {{ number_format($pesanan->harga_satuan, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Potongan diskon</dt>
                        <dd class="font-medium text-gray-900">
                            Rp {{ number_format($pesanan->potongan_diskon, 0, ',', '.') }}
                            @if ($pesanan->diskon)
                                <span class="text-gray-500">({{ $pesanan->diskon->nama }})</span>
                            @endif
                        </dd>
                    </div>
                    <div class="sm:col-span-2 border-t border-gray-100 pt-3">
                        <dt class="text-gray-500">Total bayar</dt>
                        <dd class="text-lg font-bold text-juniper-800">Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</dd>
                    </div>
                </dl>

                @if ($pesanan->status === \App\Models\Pesanan::STATUS_MENUNGGU_PEMBAYARAN)
                    <div class="mt-6">
                        <a href="{{ route('pembayaran.show', $pesanan) }}"
                            class="inline-block rounded-md bg-juniper-600 px-5 py-2.5 font-semibold text-white hover:bg-juniper-700">
                            Bayar Sekarang
                        </a>
                    </div>
                @endif
            </div>

            @if ($pesanan->pengajuanRombongan)
                <section class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <h3 class="font-semibold text-gray-800">Pengajuan rombongan</h3>
                        <x-status-badge :status="$pesanan->pengajuanRombongan->status" />
                    </div>
                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-3">
                        <div><dt class="text-gray-500">Sekolah</dt><dd class="font-medium text-gray-900">{{ $pesanan->pengajuanRombongan->nama_sekolah }}</dd></div>
                        <div><dt class="text-gray-500">Penanggung jawab</dt><dd class="font-medium text-gray-900">{{ $pesanan->pengajuanRombongan->penanggung_jawab }}</dd></div>
                        <div><dt class="text-gray-500">Kontak</dt><dd class="font-medium text-gray-900">{{ $pesanan->pengajuanRombongan->no_hp }}</dd></div>
                    </dl>
                    @if ($pesanan->pengajuanRombongan->status === \App\Models\PengajuanRombongan::STATUS_DITOLAK)
                        <div role="alert" class="mt-5 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                            <p class="font-semibold">Alasan penolakan dari admin</p>
                            <p class="mt-1">{{ $pesanan->pengajuanRombongan->catatan_admin }}</p>
                        </div>
                    @endif
                    <div class="mt-5">
                        <h4 class="text-sm font-semibold text-gray-800">Data peserta</h4>
                        <ul class="mt-2 grid gap-2 text-sm sm:grid-cols-2">
                            @foreach ($pesanan->pesertaRombongan as $peserta)
                                <li class="rounded-md bg-gray-50 px-3 py-2">{{ $peserta->nama }}{{ $peserta->kelas ? ' · '.$peserta->kelas : '' }}</li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            @endif

            @if ($pesanan->pembayaran->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800">Pembayaran</h3>
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach ($pesanan->pembayaran as $bayar)
                            <li class="flex flex-wrap items-center justify-between gap-2">
                                <span class="font-mono text-xs text-gray-600">{{ $bayar->order_id_gateway }}</span>
                                <span>Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</span>
                                <x-status-badge :status="$bayar->status" />
                                @if ($bayar->dibayar_pada)
                                    <span class="text-gray-500">{{ $bayar->dibayar_pada->format('d M Y H:i') }}</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($pesanan->tiket->isNotEmpty())
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="font-semibold text-gray-800">E-Ticket Anda</h3>
                    <p class="mt-1 text-sm text-gray-500">Tunjukkan QR/kode tiket ini di pintu masuk.</p>
                    <ul class="mt-4 space-y-2">
                        @foreach ($pesanan->tiket as $item)
                            <li class="flex flex-wrap items-center justify-between gap-2 rounded-md border border-alabaster-200 px-3 py-2 text-sm">
                                <span class="font-mono text-gray-700">{{ $item->kode_tiket }}</span>
                                <x-status-badge :status="$item->status" />
                                <span class="flex gap-3">
                                    <a href="{{ route('tiket.show', $item) }}" class="text-juniper-700 hover:underline">Lihat</a>
                                    <a href="{{ route('tiket.pdf', $item) }}" class="text-toffee-700 hover:underline">PDF</a>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
