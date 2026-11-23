<a href="{{ route('pesan.rombongan.create', ['tiket_paket_id' => $paket->id]) }}" class="mt-5 inline-flex text-sm font-semibold text-juniper-700 underline underline-offset-4">Ubah data kunjungan</a>
<p class="mt-5 text-sm font-semibold text-toffee-300">Tahap biodata peserta</p>
<h3 class="mt-2 text-2xl font-bold text-alabaster-50">Lengkapi daftar peserta</h3>
<p class="mt-3 text-alabaster-200">Jumlah nama harus sama dengan jumlah tiket agar admin dapat meninjau pengajuan.</p>

<dl class="mt-6 grid gap-4 rounded-2xl border border-juniper-600 bg-juniper-800 p-5 text-sm sm:grid-cols-2">
    <div><dt class="text-alabaster-200">Sekolah</dt><dd class="mt-1 font-semibold text-alabaster-50">{{ $draft['nama_sekolah'] }}</dd></div>
    <div><dt class="text-alabaster-200">Paket</dt><dd class="mt-1 font-semibold text-alabaster-50">{{ $paket->nama }}</dd></div>
    <div><dt class="text-alabaster-200">Tanggal kunjungan</dt><dd class="mt-1 font-semibold text-alabaster-50">{{ \Illuminate\Support\Carbon::parse($draft['tanggal_kunjungan'])->format('d M Y') }}</dd></div>
    <div><dt class="text-alabaster-200">Jumlah peserta</dt><dd class="mt-1 font-semibold text-alabaster-50">{{ $draft['jumlah_tiket'] }} orang</dd></div>
    <div><dt class="text-alabaster-200">Diskon rombongan</dt><dd class="mt-1 font-semibold text-alabaster-50">{{ $draft['diskon_nama'] ?? 'Tidak ada diskon aktif' }}</dd></div>
    <div><dt class="text-alabaster-200">Perkiraan total</dt><dd class="mt-1 font-semibold text-toffee-200">Rp {{ number_format($draft['total_bayar'], 0, ',', '.') }}</dd></div>
</dl>

<form method="POST" action="{{ route('pesan.rombongan.simpan-peserta') }}" enctype="multipart/form-data" class="mt-6 rounded-2xl border border-juniper-600 bg-juniper-800 p-5 sm:p-7" x-data="{ sumber: '{{ old('sumber_data', 'manual') }}' }">
            @csrf
            <fieldset>
                <legend class="font-semibold text-alabaster-50">Cara mengisi biodata</legend>
                <div class="mt-4 flex flex-wrap gap-5 text-sm text-alabaster-100">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="sumber_data" value="manual" x-model="sumber" class="border-juniper-400 bg-juniper-900 text-toffee-400 focus:ring-toffee-300">
                        Isi manual
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="sumber_data" value="file" x-model="sumber" class="border-juniper-400 bg-juniper-900 text-toffee-400 focus:ring-toffee-300">
                        Impor Excel atau CSV
                    </label>
                </div>
            </fieldset>

            <div x-cloak x-show="sumber === 'manual'" class="mt-6 space-y-4">
                @for ($index = 0; $index < (int) $draft['jumlah_tiket']; $index++)
                    <fieldset class="grid gap-3 rounded-lg border border-juniper-600 p-4 sm:grid-cols-[minmax(0,1fr)_12rem]">
                        <legend class="px-1 text-sm font-medium text-toffee-200">Peserta {{ $index + 1 }}</legend>
                        <div>
                            <label for="peserta_{{ $index }}_nama" class="block text-sm text-alabaster-100">Nama lengkap</label>
                            <input id="peserta_{{ $index }}_nama" name="peserta[{{ $index }}][nama]" type="text" value="{{ old("peserta.$index.nama") }}" maxlength="255" class="mt-1 block w-full rounded-lg border-juniper-500 bg-juniper-900 text-alabaster-50 focus:border-toffee-300 focus:ring-toffee-300">
                        </div>
                        <div>
                            <label for="peserta_{{ $index }}_kelas" class="block text-sm text-alabaster-100">Kelas (opsional)</label>
                            <input id="peserta_{{ $index }}_kelas" name="peserta[{{ $index }}][kelas]" type="text" value="{{ old("peserta.$index.kelas") }}" maxlength="100" class="mt-1 block w-full rounded-lg border-juniper-500 bg-juniper-900 text-alabaster-50 focus:border-toffee-300 focus:ring-toffee-300">
                        </div>
                    </fieldset>
                @endfor
            </div>

            <div x-cloak x-show="sumber === 'file'" class="mt-6 rounded-lg border border-juniper-600 p-4">
                <p class="text-sm leading-6 text-alabaster-200">Gunakan kolom <span class="font-mono text-alabaster-50">nama</span> dan <span class="font-mono text-alabaster-50">kelas</span>. Baris contoh tidak diperlukan.</p>
                <a href="{{ route('pesan.rombongan.template') }}" class="mt-3 inline-flex text-sm font-semibold text-toffee-200 underline underline-offset-4">Unduh template CSV</a>
                <div class="mt-4">
                    <label for="file_peserta" class="block text-sm font-medium text-alabaster-100">Pilih file .xlsx, .xls, atau .csv (maks. 2 MB)</label>
                    <input id="file_peserta" name="file_peserta" type="file" accept=".xlsx,.xls,.csv,.txt" class="mt-2 block w-full text-sm text-alabaster-100 file:mr-4 file:rounded-md file:border-0 file:bg-toffee-300 file:px-4 file:py-2 file:font-semibold file:text-juniper-900">
                </div>
            </div>
            @error('peserta')<p class="mt-3 text-sm text-red-200">{{ $message }}</p>@enderror
            @error('file_peserta')<p class="mt-3 text-sm text-red-200">{{ $message }}</p>@enderror

            <button type="submit" class="mt-7 w-full rounded-lg bg-toffee-300 px-5 py-3 font-semibold text-juniper-900 transition hover:bg-toffee-200 focus:outline-none focus:ring-2 focus:ring-toffee-200 focus:ring-offset-2 focus:ring-offset-juniper-800">Kirim pengajuan ke admin</button>
</form>
