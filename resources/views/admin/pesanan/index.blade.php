@extends('layouts.admin-dashboard')

@section('title', 'Pesanan & Pembayaran - Admin')
@section('page-title', 'Pesanan & Pembayaran')
@section('page-description', 'Telusuri pesanan pengunjung dan status transaksinya.')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <form method="GET" action="{{ route('admin.pesanan.index') }}" class="bg-white shadow-sm sm:rounded-lg p-4 flex flex-wrap items-end gap-3">
                <div>
                    <label for="tanggal" class="block text-xs font-medium text-gray-600">Tanggal kunjungan</label>
                    <input type="date" name="tanggal" id="tanggal" value="{{ request('tanggal') }}"
                        class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                </div>
                <div>
                    <label for="status" class="block text-xs font-medium text-gray-600">Status</label>
                    <select name="status" id="status" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                        <option value="">Semua</option>
                        @foreach ($status as $key => $label)
                            <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="kategori" class="block text-xs font-medium text-gray-600">Kategori</label>
                    <select name="kategori" id="kategori" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                        <option value="">Semua</option>
                        @foreach ($kategori as $key => $label)
                            <option value="{{ $key }}" @selected(request('kategori') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="rounded-md bg-juniper-600 px-4 py-2 text-sm font-medium text-white hover:bg-juniper-700">
                    Filter
                </button>
                <a href="{{ route('admin.pesanan.index') }}" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                    Reset
                </a>
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('admin.laporan.excel', request()->query()) }}" class="inline-flex items-center gap-1.5 rounded-md border border-green-600 bg-green-50 px-3 py-2 text-xs font-semibold text-green-700 hover:bg-green-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Ekspor Excel
                    </a>
                    <a href="{{ route('admin.laporan.pdf', request()->query()) }}" class="inline-flex items-center gap-1.5 rounded-md border border-rose-600 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11v6m0 0l-2.5-2.5M12 17l2.5-2.5"/></svg>
                        Ekspor PDF
                    </a>
                </div>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-gray-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Kode</th>
                                <th class="px-4 py-3 font-medium">Pemesan</th>
                                <th class="px-4 py-3 font-medium">Paket</th>
                                <th class="px-4 py-3 font-medium">Kategori</th>
                                <th class="px-4 py-3 font-medium">Tanggal</th>
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
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-gray-900">{{ $item->user->nama }}</div>
                                        <div class="text-xs text-gray-500">{{ $item->user->email }}</div>
                                    </td>
                                    <td class="px-4 py-3">{{ $item->tiketPaket->nama }}</td>
                                    <td class="px-4 py-3">{{ \App\Models\TiketPaket::KATEGORI[$item->kategori] ?? $item->kategori }}</td>
                                    <td class="px-4 py-3">{{ $item->tanggal_kunjungan->format('d M Y') }}</td>
                                    <td class="px-4 py-3">{{ $item->jumlah_tiket }}</td>
                                    <td class="px-4 py-3">Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3"><x-status-badge :status="$item->status" /></td>
                                    <td class="px-4 py-3 text-right">
                                        <a href="{{ route('admin.pesanan.show', $item) }}" class="text-juniper-700 hover:underline">Detail</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-6 text-center text-gray-500">Belum ada pesanan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $pesanan->links() }}
        </div>
    </div>
@endsection
