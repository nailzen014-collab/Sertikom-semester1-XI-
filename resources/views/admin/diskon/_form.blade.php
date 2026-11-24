@php
    $isAktif = old('status_aktif', $diskon->exists ? $diskon->status_aktif : true);
    $paketTersedia = \App\Models\TiketPaket::aktif()->get();
@endphp

<div class="space-y-6" x-data="{
    kategori: '{{ old('kategori', $diskon->kategori ?? \App\Models\TiketPaket::KATEGORI_UMUM) }}',
    tipe: '{{ old('tipe', $diskon->tipe ?? \App\Models\Diskon::TIPE_PERSEN) }}',
    paketList: {{ Js::from($paketTersedia) }},
    namaDiskon: '{{ old('nama', $diskon->nama) }}',
    pilihPaket(namaPaket) {
        if (!this.namaDiskon || this.namaDiskon.startsWith('Diskon ')) {
            this.namaDiskon = 'Diskon ' + namaPaket;
        }
    }
}">
    <div>
        <x-input-label for="nama" :value="__('Nama Diskon / Promo')" />
        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" x-model="namaDiskon" required autofocus placeholder="Masukkan nama diskon (mis. Promo Liburan Sekolah / Diskon Paket Rombongan)" />
        <x-input-error class="mt-2" :messages="$errors->get('nama')" />
    </div>

    <div>
        <x-input-label :value="__('Paket Wisata yang Terdaftar di Sistem')" />
        <p class="text-xs text-gray-500 mb-2">Pilih paket tiket yang terdaftar untuk menyesuaikan nama promo dan kategorinya:</p>
        <div class="flex flex-wrap gap-2">
            <template x-for="paket in paketList" :key="paket.id">
                <button type="button"
                    @click="kategori = paket.kategori; pilihPaket(paket.nama)"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border text-xs font-semibold transition"
                    :class="kategori === paket.kategori ? 'border-juniper-600 bg-juniper-50 text-juniper-800' : 'border-gray-200 bg-white text-gray-700 hover:bg-gray-50'">
                    <span class="w-2 h-2 rounded-full" :class="paket.kategori === 'umum' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                    <span x-text="paket.nama + ' (' + (paket.kategori === 'umum' ? 'Umum' : 'Rombongan') + ')'"></span>
                </button>
            </template>
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="kategori" :value="__('Kategori Sasaran Diskon')" />
            <select id="kategori" name="kategori" x-model="kategori" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                @foreach (\App\Models\TiketPaket::KATEGORI as $value => $label)
                    <option value="{{ $value }}">
                        {{ $label }} ({{ $value === \App\Models\TiketPaket::KATEGORI_UMUM ? 'Pemesanan 1-10 tiket' : 'Pemesanan > 10 tiket' }})
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('kategori')" />
        </div>

        <div>
            <x-input-label for="tipe" :value="__('Tipe Diskon')" />
            <select id="tipe" name="tipe" x-model="tipe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                @foreach (\App\Models\Diskon::TIPE as $value => $label)
                    <option value="{{ $value }}">
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
        </div>
    </div>

    <div>
        <x-input-label for="nilai" :value="__('Nilai Potongan')" />
        <div class="relative mt-1">
            <x-text-input id="nilai" name="nilai" type="number" min="1" class="block w-full pr-12" :value="old('nilai', $diskon->nilai)" required placeholder="Contoh: 10 untuk 10%, atau 5000 untuk Rp 5.000" />
            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                <span class="text-xs font-semibold text-gray-500" x-text="tipe === 'persen' ? '%' : 'Rp'"></span>
            </div>
        </div>
        <p class="mt-1 text-xs text-gray-500" x-text="tipe === 'persen' ? 'Masukkan nilai persentase diskon (1 - 100%).' : 'Masukkan nominal rupiah potongan per tiket.'"></p>
        <x-input-error class="mt-2" :messages="$errors->get('nilai')" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="tanggal_mulai" :value="__('Tanggal Mulai Berlaku')" />
            <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="mt-1 block w-full" :value="old('tanggal_mulai', $diskon->tanggal_mulai?->format('Y-m-d') ?? today()->toDateString())" required />
            <x-input-error class="mt-2" :messages="$errors->get('tanggal_mulai')" />
        </div>

        <div>
            <x-input-label for="tanggal_selesai" :value="__('Tanggal Selesai Berlaku')" />
            <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full" :value="old('tanggal_selesai', $diskon->tanggal_selesai?->format('Y-m-d') ?? today()->addMonth()->toDateString())" required />
            <x-input-error class="mt-2" :messages="$errors->get('tanggal_selesai')" />
        </div>
    </div>

    <div>
        <input type="hidden" name="status_aktif" value="0">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="status_aktif" value="1" @checked($isAktif) class="rounded border-gray-300 text-juniper-600 shadow-sm focus:ring-juniper-500">
            <span class="text-sm font-medium text-gray-700">{{ __('Aktifkan diskon ini segera') }}</span>
        </label>
        <x-input-error class="mt-2" :messages="$errors->get('status_aktif')" />
    </div>
</div>
