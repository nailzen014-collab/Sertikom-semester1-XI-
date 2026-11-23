@extends('layouts.public')

@section('body-class', 'inner-page')

@section('title', 'Pembayaran '.$pesanan->kode_pesanan.' - The Nice Park')

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <a href="{{ route('riwayat.show', $pesanan) }}" class="text-sm text-toffee-200 hover:text-toffee-100">
            &larr; Kembali ke detail pesanan
        </a>

        <div class="mt-5 rounded-lg border border-juniper-600 bg-juniper-800 p-6 sm:p-8">
            <p class="text-sm text-alabaster-200">Pesanan {{ $pesanan->kode_pesanan }}</p>
            <h1 class="mt-2 text-2xl font-bold text-alabaster-50">Selesaikan Pembayaran</h1>

            <dl class="mt-6 grid gap-4 border-t border-juniper-600 pt-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm text-alabaster-200">Tiket atau paket</dt>
                    <dd class="mt-1 font-medium text-alabaster-50">{{ $pesanan->tiketPaket->nama }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-alabaster-200">Tanggal kunjungan</dt>
                    <dd class="mt-1 font-medium text-alabaster-50">{{ $pesanan->tanggal_kunjungan->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-alabaster-200">Jumlah tiket</dt>
                    <dd class="mt-1 font-medium text-alabaster-50">{{ $pesanan->jumlah_tiket }}</dd>
                </div>
                <div>
                    <dt class="text-sm text-alabaster-200">Total pembayaran</dt>
                    <dd class="mt-1 text-xl font-bold text-toffee-200">Rp {{ number_format($pesanan->total_bayar, 0, ',', '.') }}</dd>
                </div>
            </dl>

            @if (session('error'))
                <p class="mt-6 rounded-md border border-red-800 bg-red-950 px-4 py-3 text-sm text-red-200">
                    {{ session('error') }}
                </p>
            @endif

            <form method="POST" action="{{ route('pembayaran.process', $pesanan) }}" class="mt-8">
                @csrf
                <button type="submit" class="w-full rounded-md bg-toffee-500 px-5 py-3 font-semibold text-juniper-900 transition hover:bg-toffee-400 focus:outline-none focus:ring-2 focus:ring-toffee-200 focus:ring-offset-2 focus:ring-offset-juniper-800">
                    Lanjut ke pembayaran aman
                </button>
            </form>

            <p class="mt-4 text-center text-xs text-alabaster-200">
                Pembayaran diproses melalui Midtrans. Tiket elektronik diterbitkan setelah pembayaran berhasil.
            </p>
        </div>
    </section>
@endsection
