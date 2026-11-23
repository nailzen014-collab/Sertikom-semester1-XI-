@extends('layouts.admin-dashboard')

@section('title', 'Diskon - Admin')
@section('page-title', 'Diskon')
@section('page-description', 'Kelola promo berdasarkan kategori dan masa berlaku.')
@section('page-action')
    <a href="{{ route('admin.diskon.create') }}" class="admin-heading-action">Tambah diskon</a>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-gray-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Nama</th>
                                <th class="px-4 py-3 font-medium">Kategori</th>
                                <th class="px-4 py-3 font-medium">Tipe</th>
                                <th class="px-4 py-3 font-medium">Nilai</th>
                                <th class="px-4 py-3 font-medium">Periode</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($diskon as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama }}</td>
                                    <td class="px-4 py-3">{{ \App\Models\TiketPaket::KATEGORI[$item->kategori] ?? $item->kategori }}</td>
                                    <td class="px-4 py-3">{{ \App\Models\Diskon::TIPE[$item->tipe] ?? $item->tipe }}</td>
                                    <td class="px-4 py-3">
                                        @if ($item->tipe === \App\Models\Diskon::TIPE_PERSEN)
                                            {{ $item->nilai }}%
                                        @else
                                            Rp {{ number_format($item->nilai, 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        {{ $item->tanggal_mulai?->format('d/m/Y') }} &ndash; {{ $item->tanggal_selesai?->format('d/m/Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($item->status_aktif)
                                            <span class="rounded-full bg-juniper-50 text-juniper-700 px-2 py-1 text-xs">Aktif</span>
                                        @else
                                            <span class="rounded-full bg-gray-100 text-gray-600 px-2 py-1 text-xs">Nonaktif</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.diskon.edit', $item) }}" class="text-juniper-700 hover:underline">Ubah</a>
                                        <form method="POST" action="{{ route('admin.diskon.destroy', $item) }}" class="inline" onsubmit="return confirm('Hapus diskon ini?')">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="ms-3 text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $diskon->links() }}
        </div>
    </div>
@endsection
