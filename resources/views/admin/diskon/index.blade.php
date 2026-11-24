@extends('layouts.admin-dashboard')

@section('title', 'Diskon - Admin')
@section('page-title', 'Diskon')
@section('page-description', 'Kelola promo per paket tiket dan masa berlaku.')
@section('page-action')
    <a href="{{ route('admin.diskon.create') }}" class="admin-heading-action">Tambah diskon</a>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Kalender Jadwal Diskon -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Kalender Masa Berlaku Diskon</h2>
                        <p class="text-xs text-gray-500">Visualisasi tanggal aktif diskon umum dan rombongan (otomatis berlaku sesuai tanggal kunjungan).</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs">
                        <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full bg-emerald-500"></span> Diskon Umum</span>
                        <span class="inline-flex items-center gap-1.5"><span class="h-3 w-3 rounded-full bg-amber-500"></span> Diskon Rombongan</span>
                    </div>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($diskon as $item)
                        <div class="rounded-lg border p-4 {{ $item->status_aktif ? 'border-gray-200 bg-gray-50/50' : 'border-gray-100 bg-gray-50 opacity-60' }}">
                            <div class="flex items-start justify-between gap-2">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ ($item->tiketPaket->kategori ?? 'umum') === 'umum' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $item->tiketPaket->nama ?? 'Paket dihapus' }}
                                </span>
                                <span class="text-xs font-semibold {{ $item->status_aktif ? 'text-emerald-600' : 'text-gray-400' }}">
                                    {{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>
                            <h3 class="mt-2 text-sm font-bold text-gray-900">{{ $item->nama }}</h3>
                            <p class="text-xs font-semibold text-juniper-700 mt-1">
                                Potongan: {{ $item->tipe === 'persen' ? $item->nilai.'%' : 'Rp '.number_format($item->nilai, 0, ',', '.') }}
                            </p>
                            <div class="mt-3 pt-2 border-t border-gray-200/60 flex items-center justify-between text-xs text-gray-500">
                                <span>Mulai: <strong class="text-gray-700">{{ $item->tanggal_mulai?->format('d M Y') }}</strong></span>
                                <span>Sampai: <strong class="text-gray-700">{{ $item->tanggal_selesai?->format('d M Y') }}</strong></span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 col-span-3">Belum ada promo diskon yang dijadwalkan.</p>
                    @endforelse
                </div>
            </div>

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
                                    <td class="px-4 py-3">{{ $item->tiketPaket->nama ?? '-' }}</td>
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
                                        <form method="POST" action="{{ route('admin.diskon.destroy', $item) }}" class="inline swal-confirm" data-confirm-message="Hapus promo diskon {{ $item->nama }}? Tindakan ini tidak dapat dibatalkan." data-confirm-btn="Ya, Hapus" data-confirm-danger="true">
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
