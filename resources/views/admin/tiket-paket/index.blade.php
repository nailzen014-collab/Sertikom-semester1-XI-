<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Tiket & Paket') }}
            </h2>
            <a href="{{ route('admin.tiket-paket.create') }}" class="px-4 py-2 rounded-md bg-juniper-600 text-white text-sm hover:bg-juniper-700">
                Tambah
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @if (session('message'))
                <div class="rounded-md bg-juniper-50 border border-juniper-200 text-juniper-800 px-4 py-3 text-sm">
                    {{ session('message') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-gray-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Nama</th>
                                <th class="px-4 py-3 font-medium">Kategori</th>
                                <th class="px-4 py-3 font-medium">Harga</th>
                                <th class="px-4 py-3 font-medium">Kuota Harian</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tiketPaket as $tiket)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $tiket->nama }}</td>
                                    <td class="px-4 py-3">{{ \App\Models\TiketPaket::KATEGORI[$tiket->kategori] ?? $tiket->kategori }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($tiket->harga, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ $tiket->kuota_harian }}</td>
                                    <td class="px-4 py-3">
                                        @if ($tiket->status_aktif)
                                            <span class="rounded-full bg-juniper-50 text-juniper-700 px-2 py-1 text-xs">Aktif</span>
                                        @else
                                            <span class="rounded-full bg-gray-100 text-gray-600 px-2 py-1 text-xs">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.tiket-paket.edit', $tiket) }}" class="text-juniper-700 hover:underline">Ubah</a>
                                        <form method="POST" action="{{ route('admin.tiket-paket.destroy', $tiket) }}" class="inline" onsubmit="return confirm('Hapus tiket/paket ini?')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="ms-3 text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $tiketPaket->links() }}
        </div>
    </div>
</x-app-layout>
