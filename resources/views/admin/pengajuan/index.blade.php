<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Pengajuan Rombongan</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-juniper-700 hover:underline">&larr; Dashboard</a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-4 px-4 sm:px-6 lg:px-8">
            <p class="text-sm text-gray-600">Tinjau data sekolah dan daftar peserta sebelum memutuskan pengajuan.</p>
            <div class="overflow-hidden rounded-lg bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th scope="col" class="px-4 py-3 font-medium">Sekolah</th>
                                <th scope="col" class="px-4 py-3 font-medium">Penanggung jawab</th>
                                <th scope="col" class="px-4 py-3 font-medium">Paket dan tanggal</th>
                                <th scope="col" class="px-4 py-3 font-medium">Peserta</th>
                                <th scope="col" class="px-4 py-3 font-medium">Diajukan</th>
                                <th scope="col" class="px-4 py-3 font-medium"><span class="sr-only">Aksi</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($pengajuan as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama_sekolah }}</td>
                                    <td class="px-4 py-3">
                                        <div>{{ $item->penanggung_jawab }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->no_hp }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div>{{ $item->pesanan->tiketPaket->nama }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->pesanan->tanggal_kunjungan->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $item->pesanan->jumlah_tiket }} orang</td>
                                    <td class="px-4 py-3">{{ $item->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.pesanan.show', $item->pesanan) }}" class="font-semibold text-juniper-700 hover:underline">Tinjau</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada pengajuan yang menunggu tinjauan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $pengajuan->links() }}
        </div>
    </div>
</x-app-layout>
