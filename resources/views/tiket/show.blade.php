<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('E-Ticket') }}
            </h2>
            <a href="{{ route('riwayat.show', $tiket->pesanan) }}" class="text-sm text-juniper-700 hover:text-gold-600">&larr; Detail Pesanan</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden border border-alabaster-200">
                <div class="bg-juniper-700 px-6 py-4 text-white">
                    <p class="text-sm text-alabaster-200">The Nice Park &middot; Rumpin</p>
                    <p class="text-lg font-semibold">E-Ticket</p>
                </div>

                <div class="p-6 text-center">
                    <img src="{{ $qr }}" alt="QR {{ $tiket->kode_tiket }}" class="mx-auto h-48 w-48">

                    <p class="mt-4 font-mono text-lg font-bold text-juniper-800">{{ $tiket->kode_tiket }}</p>

                    <div class="mt-4 text-sm text-gray-600 space-y-1">
                        @if ($tiket->peserta)
                            <p><span class="text-gray-500">Peserta:</span> {{ $tiket->peserta->nama }}</p>
                            @if ($tiket->peserta->kelas)
                                <p><span class="text-gray-500">Kelas:</span> {{ $tiket->peserta->kelas }}</p>
                            @endif
                        @elseif ($tiket->jenis === \App\Models\Tiket::JENIS_ROMBONGAN)
                            <p><span class="text-gray-500">Sekolah:</span> {{ $tiket->pesanan->pengajuanRombongan->nama_sekolah }}</p>
                            <p><span class="text-gray-500">Penanggung jawab:</span> {{ $tiket->pesanan->pengajuanRombongan->penanggung_jawab }}</p>
                            <p><span class="text-gray-500">Jumlah peserta:</span> {{ $tiket->pesanan->jumlah_tiket }}</p>
                        @else
                            <p><span class="text-gray-500">Nama:</span> {{ $tiket->pesanan->user->nama }}</p>
                        @endif
                        <p><span class="text-gray-500">Paket:</span> {{ $tiket->pesanan->tiketPaket->nama }}</p>
                        <p><span class="text-gray-500">Tanggal kunjungan:</span> {{ $tiket->pesanan->tanggal_kunjungan->format('d M Y') }}</p>
                    </div>

                    @if ($tiket->jenis === \App\Models\Tiket::JENIS_ROMBONGAN)
                        <div class="mt-5 rounded-md bg-alabaster-100 p-4 text-left">
                            <h2 class="text-sm font-semibold text-juniper-800">Daftar peserta</h2>
                            <ul class="mt-2 max-h-48 space-y-1 overflow-y-auto text-sm text-gray-700">
                                @foreach ($tiket->pesanan->pesertaRombongan as $peserta)
                                    <li>{{ $peserta->nama }}{{ $peserta->kelas ? ' · '.$peserta->kelas : '' }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mt-4">
                        <x-status-badge :status="$tiket->status" />
                    </div>

                    <a href="{{ route('tiket.pdf', $tiket) }}"
                        class="mt-6 inline-block w-full rounded-md bg-juniper-600 px-4 py-2.5 font-semibold text-white hover:bg-juniper-700">
                        Unduh PDF
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
