@extends('layouts.public')

@section('title', 'Pilih Kategori Tiket - The Nice Park')

@section('content')
    <section class="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <a href="{{ route('landing') }}" class="text-sm font-medium text-toffee-700 underline-offset-4 hover:text-toffee-900 hover:underline">&larr; Kembali ke beranda</a>
        <div class="mt-5 max-w-2xl">
            <p class="text-sm font-semibold text-toffee-700">Mulai pemesanan</p>
            <h1 class="mt-2 text-3xl font-bold text-juniper-900 sm:text-4xl">Pilih kategori kunjungan</h1>
            <p class="mt-3 text-juniper-700">Pilih sesuai jumlah peserta. Harga dan diskon dihitung otomatis saat Anda mengisi pesanan.</p>
        </div>

        @if (session('message'))
            <p class="mt-6 rounded-lg border border-toffee-400/60 bg-toffee-100 px-4 py-3 text-sm text-juniper-900">{{ session('message') }}</p>
        @endif

        <div class="mt-8 grid gap-5 lg:grid-cols-2">
            <section class="rounded-2xl border border-juniper-500 bg-juniper-800 p-6 sm:p-8">
                <p class="text-sm font-semibold text-toffee-200">1–10 orang</p>
                <h2 class="mt-2 text-2xl font-bold text-alabaster-50">Tiket umum</h2>
                <p class="mt-3 text-sm leading-6 text-alabaster-200">Pilih tiket, tanggal, dan jumlah. Tidak perlu persetujuan admin; setelah pesanan dibuat Anda dapat langsung membayar.</p>
                <p class="mt-5 rounded-lg bg-juniper-700 px-4 py-3 text-sm text-alabaster-100">Diskon umum aktif diterapkan otomatis.</p>
                <div class="mt-6 space-y-3">
                    @forelse ($paketUmum as $tiket)
                        <a href="{{ route('pesan.create', $tiket) }}" class="flex items-center justify-between gap-4 rounded-lg border border-juniper-500 px-4 py-3 transition hover:border-toffee-300">
                            <span class="font-medium text-alabaster-50">{{ $tiket->nama }}</span>
                            <span class="shrink-0 text-sm font-semibold text-toffee-200">Rp {{ number_format($tiket->harga, 0, ',', '.') }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-alabaster-200">Belum ada tiket umum yang tersedia.</p>
                    @endforelse
                </div>
            </section>

            <section id="rombongan" class="rounded-2xl border border-toffee-400/70 bg-toffee-100 p-6 sm:p-8">
                <p class="text-sm font-semibold text-toffee-800">11 orang atau lebih</p>
                <h2 class="mt-2 text-2xl font-bold text-juniper-900">Paket rombongan</h2>
                <p class="mt-3 text-sm leading-6 text-juniper-700">Ajukan data sekolah dan peserta. Admin meninjau pengajuan sebelum pembayaran dibuka.</p>
                <p class="mt-5 rounded-lg bg-juniper-800 px-4 py-3 text-sm text-alabaster-50">Diskon rombongan aktif diterapkan otomatis.</p>
                <div class="mt-6 space-y-3">
                    @forelse ($paketRombongan as $tiket)
                        <a href="{{ route('pesan.rombongan.create', ['tiket_paket_id' => $tiket->id]) }}" class="flex items-center justify-between gap-4 rounded-lg border border-toffee-300/40 px-4 py-3 transition hover:border-toffee-200">
                            <span class="font-medium text-juniper-900">{{ $tiket->nama }}</span>
                            <span class="shrink-0 text-sm font-semibold text-toffee-800">Rp {{ number_format($tiket->harga, 0, ',', '.') }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-juniper-700">Belum ada paket rombongan yang tersedia.</p>
                    @endforelse
                </div>
                @if ($paketRombongan->isNotEmpty())
                    <a href="{{ route('pesan.rombongan.create') }}" class="mt-6 inline-flex rounded-lg bg-toffee-300 px-5 py-3 font-semibold text-juniper-900 transition hover:bg-toffee-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-juniper-900 focus-visible:ring-offset-2 focus-visible:ring-offset-toffee-100">Ajukan paket rombongan</a>
                @endif
            </section>
        </div>
    </section>
@endsection
