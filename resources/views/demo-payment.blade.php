@extends('layouts.public')

@section('body-class', 'inner-page')

@section('title', $title)

@section('content')
    <section class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="rounded-lg border border-juniper-600 bg-juniper-800 p-6 sm:p-8">
            <p class="text-sm text-toffee-200">Demo transaksi</p>
            <h1 class="mt-2 text-2xl font-bold text-alabaster-50">Pembayaran Tiket</h1>
            <p class="mt-2 text-alabaster-200">Harga tiket: Rp10.000</p>

            @if (session('message'))
                <p class="mt-4 rounded-md border border-juniper-500 bg-juniper-700 px-4 py-3 text-sm text-alabaster-100">
                    {{ session('message') }}
                </p>
            @endif

            @if (session('error'))
                <p class="mt-4 rounded-md border border-red-800 bg-red-950 px-4 py-3 text-sm text-red-200">
                    {{ session('error') }}
                </p>
            @endif

            @if ($order)
                <dl class="mt-6 grid gap-3 border-t border-juniper-600 pt-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm text-alabaster-200">Kode pesanan</dt>
                        <dd class="mt-1 font-mono text-alabaster-50">{{ $order['order_code'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-alabaster-200">Status</dt>
                        <dd class="mt-1 font-semibold text-toffee-200">{{ $order['status'] }}</dd>
                    </div>
                </dl>
            @endif

            <div class="mt-8 flex flex-wrap gap-3">
                @if (! $order || $order['status'] !== 'paid')
                    <form method="POST" action="{{ route('demo-payment.pay') }}">
                        @csrf
                        <button type="submit" class="rounded-md bg-toffee-500 px-5 py-3 font-semibold text-juniper-900 transition hover:bg-toffee-400">
                            {{ $order ? 'Buka kembali halaman bayar' : 'Bayar Rp10.000' }}
                        </button>
                    </form>
                @endif

                @if ($order)
                    <form method="POST" action="{{ route('demo-payment.check') }}">
                        @csrf
                        <button type="submit" class="rounded-md border border-toffee-300 px-5 py-3 font-semibold text-toffee-200 transition hover:bg-juniper-700">
                            Cek Status
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
