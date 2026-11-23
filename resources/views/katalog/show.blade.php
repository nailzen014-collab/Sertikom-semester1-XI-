@extends('layouts.public')

@section('title', $tiket->nama.' - The Nice Park')

@section('content')
    <section class="mx-auto max-w-5xl px-4 py-12 sm:px-6 lg:px-8">
        <a href="{{ route('landing') }}#katalog" class="text-sm font-medium text-toffee-200 hover:text-toffee-100">&larr; Kembali ke katalog</a>
        <div class="mt-6 grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <article class="rounded-2xl border border-juniper-600 bg-juniper-800 p-6 sm:p-8">
                <p class="text-sm font-semibold text-toffee-300">{{ \App\Models\TiketPaket::KATEGORI[$tiket->kategori] }} · {{ $tiket->kategori === \App\Models\TiketPaket::KATEGORI_UMUM ? 'maksimal 10 tiket' : 'lebih dari 10 tiket' }}</p>
                <h1 class="mt-3 text-3xl font-bold text-alabaster-50">{{ $tiket->nama }}</h1>
                <p class="mt-3 text-2xl font-bold text-toffee-200">Rp {{ number_format($tiket->harga, 0, ',', '.') }} <span class="text-base font-normal text-alabaster-200">/ orang</span></p>
                @if ($diskon)
                    <p class="mt-5 rounded-lg bg-toffee-500/15 px-4 py-3 text-sm text-alabaster-100">Diskon aktif: <strong>{{ $diskon->nama }}</strong> — {{ $diskon->tipe === \App\Models\Diskon::TIPE_PERSEN ? $diskon->nilai.'%' : 'Rp '.number_format($diskon->nilai, 0, ',', '.') }} per orang.</p>
                @endif
                <div class="mt-8 border-t border-juniper-600 pt-6">
                    <h2 class="text-lg font-semibold text-alabaster-50">Tentang paket</h2>
                    <p class="mt-3 whitespace-pre-line text-sm leading-7 text-alabaster-200">{{ $tiket->deskripsi ?: 'Informasi paket akan diperbarui oleh admin.' }}</p>
                </div>
                @if ($tiket->fasilitas)
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold text-alabaster-50">Fasilitas</h2>
                        <p class="mt-3 whitespace-pre-line text-sm leading-7 text-alabaster-200">{{ $tiket->fasilitas }}</p>
                    </div>
                @endif
                <dl class="mt-6 grid gap-4 border-t border-juniper-600 pt-6 sm:grid-cols-2">
                    <div><dt class="text-sm text-alabaster-200">Kuota harian</dt><dd class="mt-1 font-semibold text-alabaster-50">{{ number_format($tiket->kuota_harian, 0, ',', '.') }} peserta</dd></div>
                    <div><dt class="text-sm text-alabaster-200">Ketentuan kategori</dt><dd class="mt-1 font-semibold text-alabaster-50">{{ $tiket->kategori === \App\Models\TiketPaket::KATEGORI_UMUM ? '1–10 peserta, tanpa persetujuan admin' : 'Minimal 11 peserta, perlu persetujuan admin' }}</dd></div>
                </dl>
            </article>
            <aside class="h-fit rounded-2xl border border-toffee-400/40 bg-toffee-500/15 p-6">
                <h2 class="text-lg font-semibold text-alabaster-50">Lanjutkan pemesanan</h2>
                <p class="mt-2 text-sm leading-6 text-alabaster-200">Pilih tanggal dan jumlah peserta. Kuota dan potongan harga akan dihitung oleh sistem.</p>
                <a href="{{ route('pesan.kategori', ['kategori' => $tiket->kategori]) }}" class="mt-6 inline-flex w-full justify-center rounded-lg bg-toffee-300 px-4 py-3 text-center font-semibold text-juniper-900 transition hover:bg-toffee-200">
                    Pilih kategori
                </a>
            </aside>
        </div>
    </section>
@endsection
