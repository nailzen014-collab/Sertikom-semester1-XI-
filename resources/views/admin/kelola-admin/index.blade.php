@extends('layouts.admin-dashboard')

@section('title', 'Kelola Admin - Admin')
@section('page-title', 'Kelola Admin')
@section('page-description', 'Tambahkan, ubah, atau hapus akun admin biasa.')
@section('page-action')
    <a href="{{ route('admin.kelola-admin.create') }}" class="admin-heading-action">Tambah admin</a>
@endsection

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-gray-500">
                            <tr>
                                <th class="px-4 py-3 font-medium">Nama</th>
                                <th class="px-4 py-3 font-medium">Email</th>
                                <th class="px-4 py-3 font-medium">No. HP</th>
                                <th class="px-4 py-3 font-medium">Peran</th>
                                <th class="px-4 py-3 font-medium text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($admin as $item)
                                <tr>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $item->nama }}</td>
                                    <td class="px-4 py-3">{{ $item->email }}</td>
                                    <td class="px-4 py-3">{{ $item->no_hp ?: '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full bg-juniper-50 text-juniper-700 px-2 py-1 text-xs">{{ $item->labelRole() }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('admin.kelola-admin.edit', $item) }}" class="text-juniper-700 hover:underline">Ubah</a>
                                        <form method="POST" action="{{ route('admin.kelola-admin.destroy', $item) }}" class="inline swal-confirm" data-confirm-message="Hapus akun admin {{ $item->nama }}? Tindakan ini tidak dapat dibatalkan." data-confirm-btn="Ya, Hapus" data-confirm-danger="true">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="ms-3 text-red-600 hover:underline">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada akun admin biasa.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $admin->links() }}

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="mb-4">
                    <h2 class="text-base font-semibold text-gray-900">Riwayat pengelolaan admin</h2>
                    <p class="text-xs text-gray-500">Badge menunjukkan siapa yang menambah, mengubah, atau menghapus akun admin.</p>
                </div>
                <x-admin-activity-list :activities="$aktivitas" />
            </div>
        </div>
    </div>
@endsection
