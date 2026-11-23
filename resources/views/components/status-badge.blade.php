@props(['status'])

@php
    $map = [
        'menunggu_persetujuan' => ['Menunggu Persetujuan', 'bg-toffee-500/20 text-toffee-200'],
        'disetujui' => ['Disetujui', 'bg-juniper-700 text-alabaster-100'],
        'menunggu_pembayaran' => ['Menunggu Pembayaran', 'bg-toffee-500/20 text-toffee-200'],
        'lunas' => ['Lunas', 'bg-juniper-700 text-alabaster-100'],
        'ditolak' => ['Ditolak', 'bg-red-950 text-red-200'],
        'kedaluwarsa' => ['Kedaluwarsa', 'bg-juniper-700 text-alabaster-200'],
        'pending' => ['Pending', 'bg-toffee-500/20 text-toffee-200'],
        'berhasil' => ['Berhasil', 'bg-juniper-700 text-alabaster-100'],
        'gagal' => ['Gagal', 'bg-red-950 text-red-200'],
        'aktif' => ['Aktif', 'bg-juniper-700 text-alabaster-100'],
        'dipakai' => ['Dipakai', 'bg-juniper-700 text-alabaster-200'],
    ];

    [$label, $class] = $map[$status] ?? [ucfirst(str_replace('_', ' ', $status)), 'bg-juniper-700 text-alabaster-100'];
@endphp

<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $class }}">{{ $label }}</span>
