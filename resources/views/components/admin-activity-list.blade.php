@props(['activities'])

@php
    $gaya = [
        \App\Models\AdminActivity::AKSI_DITAMBAHKAN => 'bg-emerald-100 text-emerald-800',
        \App\Models\AdminActivity::AKSI_DIPERBARUI => 'bg-amber-100 text-amber-800',
        \App\Models\AdminActivity::AKSI_DIHAPUS => 'bg-red-100 text-red-800',
    ];
@endphp

@if ($activities->isEmpty())
    <p class="text-sm text-gray-500">Belum ada aktivitas pengelolaan akun admin.</p>
@else
    <ul class="space-y-2">
        @foreach ($activities as $activity)
            <li class="flex flex-wrap items-center justify-between gap-2">
                <span class="admin-badge {{ $gaya[$activity->aksi] ?? 'bg-gray-100 text-gray-700' }}">
                    {{ $activity->actor_nama }} {{ $activity->aksi }} {{ $activity->subjek_nama }}
                </span>
                <time class="text-xs text-gray-500" datetime="{{ $activity->created_at?->toIso8601String() }}">
                    {{ $activity->created_at?->translatedFormat('d M Y H:i') }}
                </time>
            </li>
        @endforeach
    </ul>
@endif
