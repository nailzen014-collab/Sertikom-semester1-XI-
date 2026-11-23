<section id="diskon" class="admin-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Diskon</h2>
            <p>Kelola promo berdasarkan kategori dan masa berlaku.</p>
        </div>
        <a href="{{ route('admin.diskon.create') }}" class="admin-heading-action">Tambah diskon</a>
    </div>

    <div class="admin-table-scroll" role="region" aria-label="Daftar diskon" tabindex="0">
        <table class="admin-orders-table">
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Tipe</th>
                    <th scope="col">Nilai</th>
                    <th scope="col">Periode</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($diskonAdmin as $item)
                    <tr>
                        <td>{{ $item->nama }}</td>
                        <td>{{ \App\Models\TiketPaket::KATEGORI[$item->kategori] ?? $item->kategori }}</td>
                        <td>{{ \App\Models\Diskon::TIPE[$item->tipe] ?? $item->tipe }}</td>
                        <td>{{ $item->tipe === \App\Models\Diskon::TIPE_PERSEN ? $item->nilai.'%' : 'Rp '.number_format($item->nilai, 0, ',', '.') }}</td>
                        <td>{{ $item->tanggal_mulai?->format('d/m/Y') }} – {{ $item->tanggal_selesai?->format('d/m/Y') }}</td>
                        <td>{{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}</td>
                        <td>
                            <a href="{{ route('admin.diskon.edit', $item) }}" class="font-semibold text-juniper-700 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('admin.diskon.destroy', $item) }}" class="ml-3 inline" onsubmit="return confirm('Hapus diskon ini?')">
                                @csrf
                                @method('delete')
                                <button type="submit" class="font-semibold text-red-700 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="admin-empty-row">Belum ada diskon.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $diskonAdmin->links() }}
</section>
