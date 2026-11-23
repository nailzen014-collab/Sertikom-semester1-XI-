<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Riwayat Pesanan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-gray-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Kode</th>
                                <th class="px-4 py-3 font-medium">Paket</th>
                                <th class="px-4 py-3 font-medium">Tanggal Kunjungan</th>
                                <th class="px-4 py-3 font-medium">Jumlah</th>
                                <th class="px-4 py-3 font-medium">Total</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($pesanan as $item)
                                <tr>
                                    <td class="px-4 py-3 font-mono text-xs text-gray-700">{{ $item->kode_pesanan }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->tiketPaket->nama }}</td>
                                    <td class="px-4 py-3">{{ $item->tanggal_kunjungan->format('d M Y') }}</td>
                                    <td class="px-4 py-3">{{ $item->jumlah_tiket }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3"><x-status-badge :status="$item->status" /></td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('riwayat.show', $item) }}" class="text-juniper-700 hover:underline">Detail</a>
                                        @if ($item->status === \App\Models\Pesanan::STATUS_MENUNGGU_PEMBAYARAN)
                                            <a href="{{ route('pembayaran.show', $item) }}" class="ms-3 text-toffee-700 font-medium hover:underline">Bayar</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada pesanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $pesanan->links() }}
        </div>
    </div>
</x-app-layout>
