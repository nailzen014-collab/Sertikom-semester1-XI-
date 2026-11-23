<form method="POST" action="{{ route('pesan.rombongan.periksa') }}" class="mt-7 space-y-5 rounded-2xl border border-juniper-600 bg-juniper-800 p-6 sm:p-8">
    @csrf
    <div>
        <label for="tiket_paket_id" class="block text-sm font-medium text-alabaster-100">Paket rombongan</label>
        <select id="tiket_paket_id" name="tiket_paket_id" required class="mt-2 block w-full rounded-lg border-juniper-500 bg-juniper-900 text-alabaster-50 focus:border-toffee-300 focus:ring-toffee-300">
            <option value="">Pilih paket</option>
            @foreach ($paket as $item)
                <option value="{{ $item->id }}" @selected((string) old('tiket_paket_id', request()->integer('tiket_paket_id')) === (string) $item->id)>{{ $item->nama }} — Rp {{ number_format($item->harga, 0, ',', '.') }} / peserta</option>
            @endforeach
        </select>
        @error('tiket_paket_id')<p class="mt-2 text-sm text-red-200">{{ $message }}</p>@enderror
    </div>

    <div class="grid gap-5 sm:grid-cols-2">
        <div>
            <label for="tanggal_kunjungan" class="block text-sm font-medium text-alabaster-100">Tanggal kunjungan</label>
            <input id="tanggal_kunjungan" name="tanggal_kunjungan" type="date" min="{{ today()->toDateString() }}" value="{{ old('tanggal_kunjungan') }}" required class="mt-2 block w-full rounded-lg border-juniper-500 bg-juniper-900 text-alabaster-50 focus:border-toffee-300 focus:ring-toffee-300">
            @error('tanggal_kunjungan')<p class="mt-2 text-sm text-red-200">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="jumlah_tiket" class="block text-sm font-medium text-alabaster-100">Jumlah peserta</label>
            <input id="jumlah_tiket" name="jumlah_tiket" type="number" min="11" step="1" value="{{ old('jumlah_tiket', 11) }}" required class="mt-2 block w-full rounded-lg border-juniper-500 bg-juniper-900 text-alabaster-50 focus:border-toffee-300 focus:ring-toffee-300">
            @error('jumlah_tiket')<p class="mt-2 text-sm text-red-200">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="border-t border-juniper-600 pt-5">
        <h2 class="font-semibold text-alabaster-50">Data sekolah dan penanggung jawab</h2>
        <div class="mt-4 grid gap-5 sm:grid-cols-2">
            <div>
                <label for="nama_sekolah" class="block text-sm font-medium text-alabaster-100">Nama sekolah</label>
                <input id="nama_sekolah" name="nama_sekolah" type="text" value="{{ old('nama_sekolah') }}" required maxlength="255" autocomplete="organization" class="mt-2 block w-full rounded-lg border-juniper-500 bg-juniper-900 text-alabaster-50 focus:border-toffee-300 focus:ring-toffee-300">
                @error('nama_sekolah')<p class="mt-2 text-sm text-red-200">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="penanggung_jawab" class="block text-sm font-medium text-alabaster-100">Nama penanggung jawab</label>
                <input id="penanggung_jawab" name="penanggung_jawab" type="text" value="{{ old('penanggung_jawab') }}" required maxlength="255" autocomplete="name" class="mt-2 block w-full rounded-lg border-juniper-500 bg-juniper-900 text-alabaster-50 focus:border-toffee-300 focus:ring-toffee-300">
                @error('penanggung_jawab')<p class="mt-2 text-sm text-red-200">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="no_hp" class="block text-sm font-medium text-alabaster-100">Nomor telepon</label>
                <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp', auth()->user()->no_hp) }}" required maxlength="20" autocomplete="tel" class="mt-2 block w-full rounded-lg border-juniper-500 bg-juniper-900 text-alabaster-50 focus:border-toffee-300 focus:ring-toffee-300">
                @error('no_hp')<p class="mt-2 text-sm text-red-200">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="mode_tiket" class="block text-sm font-medium text-alabaster-100">Tampilan e-ticket</label>
                <select id="mode_tiket" name="mode_tiket" required class="mt-2 block w-full rounded-lg border-juniper-500 bg-juniper-900 text-alabaster-50 focus:border-toffee-300 focus:ring-toffee-300">
                    <option value="per_peserta" @selected(old('mode_tiket', 'per_peserta') === 'per_peserta')>Satu tiket untuk setiap peserta</option>
                    <option value="per_rombongan" @selected(old('mode_tiket') === 'per_rombongan')>Satu tiket untuk seluruh rombongan</option>
                </select>
                @error('mode_tiket')<p class="mt-2 text-sm text-red-200">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <p class="text-sm leading-6 text-alabaster-200">Pengajuan rombongan harus ditinjau admin. Pembayaran baru tersedia setelah disetujui.</p>
    <button type="submit" class="w-full rounded-lg bg-toffee-300 px-5 py-3 font-semibold text-juniper-900 transition hover:bg-toffee-200 focus:outline-none focus:ring-2 focus:ring-toffee-200 focus:ring-offset-2 focus:ring-offset-juniper-800">Cek kuota dan lanjutkan</button>
</form>
