@php
    $isAktif = old('status_aktif', $diskon->exists ? $diskon->status_aktif : true);
    $paketTersedia = \App\Models\TiketPaket::aktif()->orderBy('nama')->get();
    $diskonAktifPerPaket = $diskonAktifPerPaket ?? [];
    $tiketPaketTerpilih = old('tiket_paket_id', $diskon->tiket_paket_id);
@endphp

<div
    class="space-y-6"
    x-data="{
        tiketPaketId: @js((string) $tiketPaketTerpilih),
        tipe: @js(old('tipe', $diskon->tipe ?? \App\Models\Diskon::TIPE_PERSEN)),
        namaDiskon: @js(old('nama', $diskon->nama)),
        gantiDiskon: @js((string) old('ganti_diskon', '0')),
        diskonAktif: {{ Js::from($diskonAktifPerPaket) }},
        get diskonBentrok() {
            return this.diskonAktif[this.tiketPaketId] ?? [];
        },
        syncPaket(event) {
            this.tiketPaketId = event.target.value;
        },
        formatNilai(item) {
            return item.tipe === 'persen'
                ? item.nilai + '%'
                : 'Rp ' + new Intl.NumberFormat('id-ID').format(item.nilai);
        },
    }"
>
    {{-- Nama diskon --}}
    <div>
        <x-input-label for="nama" :value="__('Nama Diskon / Promo')" />
        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" x-model="namaDiskon" required autofocus placeholder="Contoh: Promo Liburan Sekolah" />
        <x-input-error class="mt-2" :messages="$errors->get('nama')" />
    </div>

    {{-- Paket tiket sasaran --}}
    <div>
        <x-input-label for="tiket_paket_id" :value="__('Paket Tiket yang Didiskon')" />
        <select
            id="tiket_paket_id"
            name="tiket_paket_id"
            required
            @change="syncPaket"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500"
        >
            <option value="" disabled @selected(! $tiketPaketTerpilih)>Pilih paket tiket</option>
            @foreach ($paketTersedia as $paket)
                <option value="{{ $paket->id }}" @selected((string) $tiketPaketTerpilih === (string) $paket->id)>
                    {{ $paket->nama }} ({{ \App\Models\TiketPaket::KATEGORI[$paket->kategori] ?? $paket->kategori }})
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">Diskon hanya berlaku untuk paket tiket yang dipilih di sini.</p>
        <x-input-error class="mt-2" :messages="$errors->get('tiket_paket_id')" />
    </div>

    {{-- Pilihan ganti/pertahankan saat paket sudah punya diskon aktif (khusus tambah baru) --}}
    @unless ($diskon->exists)
        <div x-show="diskonBentrok.length > 0" x-cloak class="rounded-xl border border-amber-300 bg-amber-50 p-4">
            <div class="flex items-start gap-2">
                <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.9 2.4 18a2 2 0 0 0 1.7 3h15.8a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>
                </svg>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-amber-900">Paket ini sudah punya diskon aktif</p>
                    <ul class="mt-2 space-y-1 text-xs text-amber-800">
                        <template x-for="item in diskonBentrok" :key="item.id">
                            <li class="flex items-center justify-between gap-2 rounded-md bg-white/60 px-2 py-1">
                                <span class="truncate" x-text="item.nama"></span>
                                <span class="font-semibold" x-text="formatNilai(item)"></span>
                            </li>
                        </template>
                    </ul>

                    <p class="mt-3 text-sm font-medium text-amber-900">Apa yang ingin kamu lakukan?</p>
                    <div class="mt-2 space-y-2">
                        <label class="flex cursor-pointer items-start gap-2 rounded-lg border p-3 text-sm transition" :class="gantiDiskon === '0' ? 'border-juniper-500 bg-white' : 'border-amber-200 bg-white/50'">
                            <input type="radio" name="ganti_diskon" value="0" x-model="gantiDiskon" class="mt-0.5 border-gray-300 text-juniper-600 focus:ring-juniper-500">
                            <span>
                                <span class="block font-medium text-gray-800">Pertahankan yang lama</span>
                                <span class="block text-xs text-gray-500">Tambah diskon baru; boleh lebih dari satu diskon aktif untuk paket ini.</span>
                            </span>
                        </label>
                        <label class="flex cursor-pointer items-start gap-2 rounded-lg border p-3 text-sm transition" :class="gantiDiskon === '1' ? 'border-juniper-500 bg-white' : 'border-amber-200 bg-white/50'">
                            <input type="radio" name="ganti_diskon" value="1" x-model="gantiDiskon" class="mt-0.5 border-gray-300 text-juniper-600 focus:ring-juniper-500">
                            <span>
                                <span class="block font-medium text-gray-800">Ganti dengan yang baru</span>
                                <span class="block text-xs text-gray-500">Nonaktifkan diskon lama untuk paket ini, lalu pakai diskon baru saja.</span>
                            </span>
                        </label>
                    </div>
                </div>
            </div>
            <x-input-error class="mt-2" :messages="$errors->get('ganti_diskon')" />
        </div>
    @endunless

    {{-- Tipe & nilai --}}
    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="tipe" :value="__('Tipe Diskon')" />
            <select id="tipe" name="tipe" x-model="tipe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                @foreach (\App\Models\Diskon::TIPE as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
        </div>

        <div>
            <x-input-label for="nilai" :value="__('Nilai Potongan')" />
            <div class="relative mt-1">
                <x-text-input id="nilai" name="nilai" type="number" min="1" class="block w-full pr-12" :value="old('nilai', $diskon->nilai)" required placeholder="10 atau 5000" />
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                    <span class="text-xs font-semibold text-gray-500" x-text="tipe === 'persen' ? '%' : 'Rp'"></span>
                </div>
            </div>
            <p class="mt-1 text-xs text-gray-500" x-text="tipe === 'persen' ? 'Persentase potongan (1 - 100).' : 'Nominal potongan per tiket dalam rupiah.'"></p>
            <x-input-error class="mt-2" :messages="$errors->get('nilai')" />
        </div>
    </div>

    {{-- Masa berlaku --}}
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

    {{-- Status aktif --}}
    <div>
        <input type="hidden" name="status_aktif" value="0">
        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-gray-200 bg-gray-50/60 p-4">
            <input type="checkbox" name="status_aktif" value="1" @checked($isAktif) class="mt-0.5 rounded border-gray-300 text-juniper-600 shadow-sm focus:ring-juniper-500">
            <span>
                <span class="block text-sm font-medium text-gray-800">Aktifkan diskon ini segera</span>
                <span class="block text-xs text-gray-500">Jika dimatikan, diskon tersimpan sebagai draf dan tidak langsung berlaku.</span>
            </span>
        </label>
        <x-input-error class="mt-2" :messages="$errors->get('status_aktif')" />
    </div>
</div>