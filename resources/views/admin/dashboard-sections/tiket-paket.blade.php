<section id="tiket-paket" class="admin-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Tiket dan paket</h2>
            <p>Daftar harga, kuota, dan status produk.</p>
        </div>
        <a href="{{ route('admin.tiket-paket.create') }}" class="admin-heading-action">Tambah tiket/paket</a>
    </div>

    <div class="admin-table-scroll" role="region" aria-label="Tiket dan paket" tabindex="0">
        <table class="admin-orders-table">
            <thead>
                <tr>
                    <th scope="col">Nama</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Harga</th>
                    <th scope="col">Kuota harian</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($paketAdmin as $item)
                    <tr>
                        <td>{{ $item->nama }}</td>
                        <td>{{ \App\Models\TiketPaket::KATEGORI[$item->kategori] ?? $item->kategori }}</td>
                        <td>Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                        <td>{{ $item->kuota_harian }}</td>
                        <td>{{ $item->status_aktif ? 'Aktif' : 'Nonaktif' }}</td>
                        <td>
                            <a href="{{ route('admin.tiket-paket.edit', $item) }}" class="font-semibold text-juniper-700 hover:underline">Ubah</a>
                            <form method="POST" action="{{ route('admin.tiket-paket.destroy', $item) }}" class="ml-3 inline" onsubmit="return confirm('Hapus tiket/paket ini?')">
                                @csrf
                                @method('delete')
                                <button type="submit" class="font-semibold text-red-700 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="admin-empty-row">Belum ada tiket atau paket.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $paketAdmin->links() }}
</section>
