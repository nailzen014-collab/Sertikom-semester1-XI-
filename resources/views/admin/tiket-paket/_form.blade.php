@php
    $isAktif = old('status_aktif', $tiket->exists ? $tiket->status_aktif : true);
@endphp

<div class="space-y-6">
    <div>
        <x-input-label for="nama" :value="__('Nama Tiket/Paket')" />
        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $tiket->nama)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('nama')" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="kategori" :value="__('Kategori')" />
            <select id="kategori" name="kategori" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                @foreach (\App\Models\TiketPaket::KATEGORI as $value => $label)
                    <option value="{{ $value }}" @selected(old('kategori', $tiket->kategori ?? \App\Models\TiketPaket::KATEGORI_UMUM) === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('kategori')" />
        </div>

        <div>
            <x-input-label for="harga" :value="__('Harga per orang (Rp)')" />
            <x-text-input id="harga" name="harga" type="number" min="0" class="mt-1 block w-full" :value="old('harga', $tiket->harga)" required />
            <x-input-error class="mt-2" :messages="$errors->get('harga')" />
        </div>
    </div>

    <div>
        <x-input-label for="kuota_harian" :value="__('Kuota Harian')" />
        <x-text-input id="kuota_harian" name="kuota_harian" type="number" min="0" class="mt-1 block w-full" :value="old('kuota_harian', $tiket->kuota_harian)" required />
        <x-input-error class="mt-2" :messages="$errors->get('kuota_harian')" />
    </div>

    <div>
        <x-input-label for="deskripsi" :value="__('Deskripsi')" />
        <textarea id="deskripsi" name="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500">{{ old('deskripsi', $tiket->deskripsi) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('deskripsi')" />
    </div>

    <div>
        <x-input-label for="fasilitas" :value="__('Fasilitas')" />
        <textarea id="fasilitas" name="fasilitas" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500">{{ old('fasilitas', $tiket->fasilitas) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('fasilitas')" />
    </div>

    <div>
        <input type="hidden" name="status_aktif" value="0">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="status_aktif" value="1" @checked($isAktif) class="rounded border-gray-300 text-juniper-600 shadow-sm focus:ring-juniper-500">
            <span class="text-sm text-gray-700">{{ __('Aktif') }}</span>
        </label>
        <x-input-error class="mt-2" :messages="$errors->get('status_aktif')" />
    </div>
</div>
