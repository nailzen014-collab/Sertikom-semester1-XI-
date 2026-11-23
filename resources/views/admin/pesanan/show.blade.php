@extends('layouts.admin-dashboard')

@section('title', 'Detail Pesanan - Admin')
@section('page-title', 'Detail Pesanan')
@section('page-description', 'Rincian pesanan, pengajuan, pembayaran, dan tiket.')
@section('page-action')
    <a href="{{ route('admin.pesanan.index') }}" class="admin-heading-action">&larr; Daftar pesanan</a>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <p class="font-mono text-xs text-gray-500">{{ $pesanan->kode_pesanan }}</p>
                        <h3 class="mt-1 text-xl font-bold text-juniper-800">{{ $pesanan->tiketPaket->nama }}</h3>
                    </div>
                    <x-status-badge :status="$pesanan->status" />
                </div>

                <dl class="mt-6 grid gap-4 sm:grid-cols-2 text-sm">
                    <div>
                        <dt class="text-gray-500">Pemesan</dt>
                        <dd class="font-medium text-gray-900">{{ $pesanan->user->nama }} ({{ $pesanan->user->email }})</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">No. HP</dt>
                        <dd class="font-medium text-gray-900">{{ $pesanan->user->no_hp ?: '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Kategori</dt>
                        <dd class="font-medium text-gray-900">{{ \App\Models\TiketPaket::KATEGORI[$pesanan->kategori] ?? $pesanan->kategori }}</dd>
                    </div>
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
                    <div>
                        <dt class="text-gray-500">Total bayar</dt>
                        <dd class="font-bold text-juniper-800">Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</dd>
                    </div>
                </dl>
            </div>

            @if ($pesanan->pengajuanRombongan)
                <section class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-semibold text-gray-800">Pengajuan sekolah</h3>
                            <p class="mt-1 text-sm text-gray-500">Status: {{ ucfirst($pesanan->pengajuanRombongan->status) }}</p>
                        </div>
                        <p class="text-sm text-gray-600">
                            Tampilan e-ticket:
                            {{ $pesanan->pengajuanRombongan->mode_tiket === \App\Models\PengajuanRombongan::MODE_PER_ROMBONGAN ? 'Satu untuk seluruh rombongan' : 'Satu per peserta' }}
                        </p>
                    </div>
                    <dl class="mt-4 grid gap-4 text-sm sm:grid-cols-3">
                        <div><dt class="text-gray-500">Nama sekolah</dt><dd class="font-medium text-gray-900">{{ $pesanan->pengajuanRombongan->nama_sekolah }}</dd></div>
                        <div><dt class="text-gray-500">Penanggung jawab</dt><dd class="font-medium text-gray-900">{{ $pesanan->pengajuanRombongan->penanggung_jawab }}</dd></div>
                        <div><dt class="text-gray-500">Kontak</dt><dd class="font-medium text-gray-900">{{ $pesanan->pengajuanRombongan->no_hp }}</dd></div>
                    </dl>

                    @if ($pesanan->pengajuanRombongan->catatan_admin)
                        <div class="mt-5 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                            <p class="font-semibold">Alasan penolakan</p>
                            <p class="mt-1">{{ $pesanan->pengajuanRombongan->catatan_admin }}</p>
                        </div>
                    @endif

                    <div class="mt-5">
                        <h4 class="text-sm font-semibold text-gray-800">Daftar peserta ({{ $pesanan->pesertaRombongan->count() }})</h4>
                        <ol class="mt-2 grid gap-2 text-sm sm:grid-cols-2">
                            @foreach ($pesanan->pesertaRombongan as $peserta)
                                <li class="rounded-md bg-gray-50 px-3 py-2">{{ $peserta->nama }}{{ $peserta->kelas ? ' · '.$peserta->kelas : '' }}</li>
                            @endforeach
                        </ol>
                    </div>

                    @if ($pesanan->status === \App\Models\Pesanan::STATUS_MENUNGGU_PERSETUJUAN)
                        <div class="mt-6 grid gap-6 border-t border-gray-100 pt-5 md:grid-cols-2">
                            <form method="POST" action="{{ route('admin.pengajuan.approve', $pesanan) }}">
                                @csrf
                                <button type="submit" class="w-full rounded-md bg-juniper-600 px-4 py-2.5 font-semibold text-white hover:bg-juniper-700">Setujui pengajuan</button>
                            </form>
                            <form method="POST" action="{{ route('admin.pengajuan.reject', $pesanan) }}" class="space-y-3">
                                @csrf
                                <div>
                                    <label for="catatan_admin" class="block text-sm font-medium text-gray-700">Alasan penolakan</label>
                                    <textarea id="catatan_admin" name="catatan_admin" rows="3" required maxlength="2000" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500">{{ old('catatan_admin') }}</textarea>
                                </div>
                                <button type="submit" class="w-full rounded-md border border-red-300 px-4 py-2.5 font-semibold text-red-700 hover:bg-red-50">Tolak pengajuan</button>
                            </form>
                        </div>
                    @endif
                </section>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800">Pembayaran</h3>
                @if ($pesanan->pembayaran->isEmpty())
                    <p class="mt-2 text-sm text-gray-500">Belum ada data pembayaran.</p>
                @else
                    <ul class="mt-3 space-y-2 text-sm">
                        @foreach ($pesanan->pembayaran as $bayar)
                            <li class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 pb-2">
                                <span class="font-mono text-xs text-gray-600">{{ $bayar->order_id_gateway }}</span>
                                <span>{{ $bayar->metode ?: '-' }}</span>
                                <span>Rp {{ number_format($bayar->jumlah, 0, ',', '.') }}</span>
                                <x-status-badge :status="$bayar->status" />
                                <span class="text-gray-500">{{ $bayar->dibayar_pada?->format('d M Y H:i') ?? '-' }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800">Tiket</h3>
                @if ($pesanan->tiket->isEmpty())
                    <p class="mt-2 text-sm text-gray-500">E-ticket belum terbit.</p>
                @else
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach ($pesanan->tiket as $item)
                            <span class="inline-flex items-center gap-2 rounded-md border border-alabaster-200 px-3 py-1.5 text-sm">
                                <span class="font-mono text-gray-700">{{ $item->kode_tiket }}</span>
                                <x-status-badge :status="$item->status" />
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
