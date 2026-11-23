<section id="pengajuan" class="admin-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Pengajuan rombongan</h2>
            <p>Tinjau data sekolah dan peserta sebelum memutuskan.</p>
        </div>
        <span>{{ $pengajuan->total() }} menunggu</span>
    </div>

    <div class="admin-table-scroll" role="region" aria-label="Pengajuan rombongan" tabindex="0">
        <table class="admin-orders-table">
            <thead>
                <tr>
                    <th scope="col">Sekolah</th>
                    <th scope="col">Penanggung jawab</th>
                    <th scope="col">Paket dan tanggal</th>
                    <th scope="col">Peserta</th>
                    <th scope="col">Diajukan</th>
                    <th scope="col">Tinjauan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengajuan as $item)
                    <tr>
                        <td>{{ $item->nama_sekolah }}</td>
                        <td>{{ $item->penanggung_jawab }}<br><small>{{ $item->no_hp }}</small></td>
                        <td>{{ $item->pesanan->tiketPaket->nama }}<br><small>{{ $item->pesanan->tanggal_kunjungan->format('d M Y') }}</small></td>
                        <td>{{ $item->pesanan->jumlah_tiket }}</td>
                        <td>{{ $item->created_at->format('d M Y') }}</td>
                        <td>
                            <details>
                                <summary class="cursor-pointer font-semibold text-juniper-700">Lihat dan putuskan</summary>
                                <div class="mt-3 min-w-72 space-y-3">
                                    <ol class="grid gap-1 text-sm">
                                        @foreach ($item->pesanan->pesertaRombongan as $peserta)
                                            <li>{{ $peserta->nama }}{{ $peserta->kelas ? ' · '.$peserta->kelas : '' }}</li>
                                        @endforeach
                                    </ol>
                                    <div class="grid gap-3">
                                        <form method="POST" action="{{ route('admin.pengajuan.approve', $item->pesanan) }}">
                                            @csrf
                                            <button type="submit" class="w-full rounded-md bg-juniper-600 px-3 py-2 text-sm font-semibold text-white hover:bg-juniper-700">Setujui</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.pengajuan.reject', $item->pesanan) }}" class="space-y-2">
                                            @csrf
                                            <label for="catatan_admin_{{ $item->id }}" class="block text-xs font-medium">Alasan penolakan</label>
                                            <textarea id="catatan_admin_{{ $item->id }}" name="catatan_admin" rows="2" required maxlength="2000" class="block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-juniper-500 focus:ring-juniper-500">{{ old('catatan_admin') }}</textarea>
                                            <button type="submit" class="w-full rounded-md border border-red-300 px-3 py-2 text-sm font-semibold text-red-700 hover:bg-red-50">Tolak</button>
                                        </form>
                                    </div>
                                </div>
                            </details>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="admin-empty-row">Tidak ada pengajuan yang menunggu tinjauan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $pengajuan->links() }}
</section>
