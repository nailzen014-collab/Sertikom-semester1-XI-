<section id="pesanan" class="admin-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Pesanan dan pembayaran</h2>
            <p>Telusuri pesanan pengunjung dan status transaksinya.</p>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.dashboard') }}#pesanan" class="mb-5 flex flex-wrap items-end gap-3">
        <div>
            <label for="filter_tanggal" class="block text-xs font-medium text-gray-600">Tanggal kunjungan</label>
            <input id="filter_tanggal" type="date" name="tanggal" value="{{ request('tanggal') }}" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
        </div>
        <div>
            <label for="filter_status" class="block text-xs font-medium text-gray-600">Status</label>
            <select id="filter_status" name="status" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                <option value="">Semua</option>
                @foreach ($statusPesanan as $key => $label)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="filter_kategori" class="block text-xs font-medium text-gray-600">Kategori</label>
            <select id="filter_kategori" name="kategori" class="mt-1 rounded-md border-gray-300 text-sm shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                <option value="">Semua</option>
                @foreach ($kategoriTiket as $key => $label)
                    <option value="{{ $key }}" @selected(request('kategori') === $key)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="rounded-md bg-juniper-600 px-4 py-2 text-sm font-medium text-white hover:bg-juniper-700">Filter</button>
        <a href="{{ route('admin.dashboard') }}#pesanan" class="rounded-md border border-gray-300 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Reset</a>
    </form>

    <div class="admin-table-scroll" role="region" aria-label="Daftar pesanan" tabindex="0">
        <table class="admin-orders-table">
            <thead>
                <tr>
                    <th scope="col">Kode</th>
                    <th scope="col">Pemesan</th>
                    <th scope="col">Paket</th>
                    <th scope="col">Tanggal</th>
                    <th scope="col">Jumlah</th>
                    <th scope="col">Total</th>
                    <th scope="col">Status</th>
                    <th scope="col">Rincian</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pesananAdmin as $item)
                    <tr>
                        <td class="font-mono text-xs">{{ $item->kode_pesanan }}</td>
                        <td>{{ $item->user->nama }}<br><small>{{ $item->user->email }}</small></td>
                        <td>{{ $item->tiketPaket->nama }}<br><small>{{ \App\Models\TiketPaket::KATEGORI[$item->kategori] ?? $item->kategori }}</small></td>
                        <td>{{ $item->tanggal_kunjungan->format('d M Y') }}</td>
                        <td>{{ $item->jumlah_tiket }}</td>
                        <td>Rp {{ number_format($item->total_bayar, 0, ',', '.') }}</td>
                        <td><x-status-badge :status="$item->status" /></td>
                        <td>
                            <details>
                                <summary class="cursor-pointer font-semibold text-juniper-700">Buka</summary>
                                <div class="mt-3 min-w-64 space-y-2 text-xs">
                                    <p>Telepon: {{ $item->user->no_hp ?: '-' }}</p>
                                    <p>Harga satuan: Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</p>
                                    <p>Diskon: Rp {{ number_format($item->potongan_diskon, 0, ',', '.') }}{{ $item->diskon ? ' · '.$item->diskon->nama : '' }}</p>
                                    @if ($item->pengajuanRombongan)
                                        <p>Sekolah: {{ $item->pengajuanRombongan->nama_sekolah }}</p>
                                        <p>Penanggung jawab: {{ $item->pengajuanRombongan->penanggung_jawab }}</p>
                                    @endif
                                    @if ($item->pembayaran->isNotEmpty())
                                        <p class="font-semibold">Pembayaran</p>
                                        @foreach ($item->pembayaran as $bayar)
                                            <p>{{ $bayar->order_id_gateway }} · Rp {{ number_format($bayar->jumlah, 0, ',', '.') }} · {{ \App\Models\Pembayaran::STATUS[$bayar->status] ?? $bayar->status }}</p>
                                        @endforeach
                                    @endif
                                    @if ($item->tiket->isNotEmpty())
                                        <p class="font-semibold">Tiket terbit: {{ $item->tiket->count() }}</p>
                                    @endif
                                </div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="admin-empty-row">Tidak ada pesanan sesuai filter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $pesananAdmin->links() }}
</section>
