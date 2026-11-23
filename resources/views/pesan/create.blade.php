@extends('layouts.public')

@section('body-class', 'inner-page')

@section('title', 'Pesan '.$tiket->nama.' - The Nice Park')

@section('content')
    <section class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('landing') }}" class="text-sm text-juniper-700 hover:text-gold-600">&larr; Kembali ke katalog</a>

        <div class="mt-4 rounded-lg border border-alabaster-200 bg-white p-6 shadow-sm">
            <span class="text-xs font-medium uppercase tracking-wide text-toffee-600">Tiket Umum</span>
            <h1 class="mt-1 text-2xl font-bold text-juniper-800">{{ $tiket->nama }}</h1>
            <p class="mt-2 text-2xl font-bold text-juniper-700">
                Rp {{ number_format($tiket->harga, 0, ',', '.') }}
                <span class="text-sm font-normal text-gray-500">/ orang</span>
            </p>
            @if ($tiket->deskripsi)
                <p class="mt-3 text-sm text-gray-600">{{ $tiket->deskripsi }}</p>
            @endif
            @if ($tiket->fasilitas)
                <p class="mt-2 text-sm text-gray-600"><span class="font-medium text-gray-700">Fasilitas:</span> {{ $tiket->fasilitas }}</p>
            @endif
            <p class="mt-2 text-sm text-gray-500">Kuota harian: {{ $tiket->kuota_harian }} tiket</p>
        </div>

        @if ($errors->any())
            <div class="mt-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('pesan.store', $tiket) }}" class="mt-6 space-y-5 rounded-lg border border-alabaster-200 bg-white p-6 shadow-sm"
            x-data="{
                jumlah: {{ (int) old('jumlah_tiket', 1) }},
                harga: {{ (int) $tiket->harga }},
                tipe: '{{ $diskon?->tipe ?? '' }}',
                nilai: {{ (int) ($diskon?->nilai ?? 0) }},
                get potonganPerTiket() {
                    if (!this.tipe) return 0;
                    return this.tipe === 'persen' ? Math.round(this.harga * this.nilai / 100) : this.nilai;
                },
                get total() {
                    return Math.max(0, (this.harga - this.potonganPerTiket) * this.jumlah);
                }
            }">
            @csrf

            <div>
                <label for="tanggal_kunjungan" class="block text-sm font-medium text-gray-700">Tanggal Kunjungan</label>
                <input type="date" name="tanggal_kunjungan" id="tanggal_kunjungan"
                    value="{{ old('tanggal_kunjungan') }}" min="{{ now()->toDateString() }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500" required>
            </div>

            <div>
                <label for="jumlah_tiket" class="block text-sm font-medium text-gray-700">Jumlah Tiket</label>
                <input type="number" name="jumlah_tiket" id="jumlah_tiket" min="1" max="10"
                    x-model.number="jumlah"
                    value="{{ old('jumlah_tiket', 1) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500" required>
                <p class="mt-1 text-xs text-gray-500">Maksimal 10 tiket per pesanan. Untuk lebih dari 10, gunakan paket rombongan.</p>
            </div>

            @if ($diskon)
                <div class="rounded-md bg-gold-500/15 px-4 py-3 text-sm text-toffee-700">
                    Diskon otomatis: <strong>{{ $diskon->nama }}</strong>
                    ({{ $diskon->tipe === \App\Models\Diskon::TIPE_PERSEN ? $diskon->nilai.'%' : 'Rp '.number_format($diskon->nilai, 0, ',', '.') }} per tiket)
                </div>
            @endif

            <div class="rounded-md bg-alabaster-100 px-4 py-3 text-sm text-gray-700 space-y-1">
                <div class="flex justify-between">
                    <span>Harga per tiket</span>
                    <span>Rp {{ number_format($tiket->harga, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between" x-show="potonganPerTiket > 0">
                    <span>Potongan per tiket</span>
                    <span x-text="'- Rp ' + new Intl.NumberFormat('id-ID').format(potonganPerTiket)"></span>
                </div>
                <div class="flex justify-between border-t border-alabaster-300 pt-2 text-base font-semibold text-juniper-800">
                    <span>Total bayar</span>
                    <span x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(total)"></span>
                </div>
            </div>

            <button type="submit" class="w-full rounded-md bg-juniper-600 px-4 py-3 font-semibold text-white hover:bg-juniper-700">
                Lanjut ke Pembayaran
            </button>
        </form>
    </section>
@endsection
