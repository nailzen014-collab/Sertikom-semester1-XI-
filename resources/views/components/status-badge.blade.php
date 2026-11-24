@props(['status'])

@php
    $map = [
        'menunggu_persetujuan' => ['Menunggu Persetujuan', 'bg-toffee-50 text-toffee-700 ring-toffee-200'],
        'disetujui' => ['Disetujui', 'bg-juniper-50 text-juniper-700 ring-juniper-200'],
        'menunggu_pembayaran' => ['Menunggu Pembayaran', 'bg-toffee-50 text-toffee-700 ring-toffee-200'],
        'lunas' => ['Lunas', 'bg-juniper-50 text-juniper-700 ring-juniper-200'],
        'ditolak' => ['Ditolak', 'bg-red-50 text-red-700 ring-red-200'],
        'kedaluwarsa' => ['Kedaluwarsa', 'bg-alabaster-100 text-juniper-500 ring-alabaster-300'],
        'pending' => ['Pending', 'bg-toffee-50 text-toffee-700 ring-toffee-200'],
        'berhasil' => ['Berhasil', 'bg-juniper-50 text-juniper-700 ring-juniper-200'],
        'gagal' => ['Gagal', 'bg-red-50 text-red-700 ring-red-200'],
        'aktif' => ['Aktif', 'bg-juniper-50 text-juniper-700 ring-juniper-200'],
        'dipakai' => ['Dipakai', 'bg-alabaster-100 text-juniper-500 ring-alabaster-300'],
    ];

    [$label, $class] = $map[$status] ?? [ucfirst(str_replace('_', ' ', $status)), 'bg-alabaster-100 text-juniper-500 ring-alabaster-300'];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 ring-inset {{ $class }}">{{ $label }}</span>
