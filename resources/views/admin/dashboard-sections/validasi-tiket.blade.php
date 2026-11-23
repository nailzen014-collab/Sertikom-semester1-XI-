<section id="validasi-tiket" class="admin-panel">
    <div class="admin-panel-heading">
        <div>
            <h2>Validasi tiket masuk</h2>
            <p>Masukkan kode tiket untuk memastikan tiket lunas dan berlaku hari ini.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.tiket.validasi.store') }}#validasi-tiket" class="max-w-xl space-y-4">
        @csrf
        <div>
            <label for="kode_tiket_dashboard" class="block text-sm font-medium text-gray-700">Kode tiket</label>
            <input id="kode_tiket_dashboard" name="kode_tiket" type="text" value="{{ old('kode_tiket') }}" required autocomplete="off" spellcheck="false" class="mt-1 block w-full rounded-md border-gray-300 font-mono uppercase shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
            @error('kode_tiket')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="rounded-md bg-juniper-600 px-4 py-3 font-semibold text-white hover:bg-juniper-700">Validasi dan tandai dipakai</button>
    </form>

    @if ($tiketValidasi)
        <div class="mt-6 rounded-lg border border-juniper-200 bg-juniper-50 p-5">
            <p role="status" class="font-semibold text-juniper-800">Tiket berhasil digunakan</p>
            <p class="mt-2 font-mono text-lg font-bold text-juniper-800">{{ $tiketValidasi->kode_tiket }}</p>
            <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-gray-600">Pemesan</dt><dd class="font-medium">{{ $tiketValidasi->pesanan->user->nama }}</dd></div>
                <div><dt class="text-gray-600">Paket</dt><dd class="font-medium">{{ $tiketValidasi->pesanan->tiketPaket->nama }}</dd></div>
                @if ($tiketValidasi->peserta)
                    <div><dt class="text-gray-600">Peserta</dt><dd class="font-medium">{{ $tiketValidasi->peserta->nama }}</dd></div>
                @endif
                <div><dt class="text-gray-600">Tanggal kunjungan</dt><dd class="font-medium">{{ $tiketValidasi->pesanan->tanggal_kunjungan->format('d M Y') }}</dd></div>
            </dl>
        </div>
    @endif
</section>
