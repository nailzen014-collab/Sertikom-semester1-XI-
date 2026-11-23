@php
    $isAktif = old('status_aktif', $diskon->exists ? $diskon->status_aktif : true);
@endphp

<div class="space-y-6">
    <div>
        <x-input-label for="nama" :value="__('Nama Diskon')" />
        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $diskon->nama)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('nama')" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="kategori" :value="__('Kategori')" />
            <select id="kategori" name="kategori" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                @foreach (\App\Models\TiketPaket::KATEGORI as $value => $label)
                    <option value="{{ $value }}" @selected(old('kategori', $diskon->kategori ?? \App\Models\TiketPaket::KATEGORI_UMUM) === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('kategori')" />
        </div>

        <div>
            <x-input-label for="tipe" :value="__('Tipe Diskon')" />
            <select id="tipe" name="tipe" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                @foreach (\App\Models\Diskon::TIPE as $value => $label)
                    <option value="{{ $value }}" @selected(old('tipe', $diskon->tipe ?? \App\Models\Diskon::TIPE_PERSEN) === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('tipe')" />
        </div>
    </div>

    <div>
        <x-input-label for="nilai" :value="__('Nilai (persen 1-100 atau nominal Rp)')" />
        <x-text-input id="nilai" name="nilai" type="number" min="1" class="mt-1 block w-full" :value="old('nilai', $diskon->nilai)" required />
        <x-input-error class="mt-2" :messages="$errors->get('nilai')" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="tanggal_mulai" :value="__('Tanggal Mulai')" />
            <x-text-input id="tanggal_mulai" name="tanggal_mulai" type="date" class="mt-1 block w-full" :value="old('tanggal_mulai', $diskon->tanggal_mulai?->format('Y-m-d'))" required />
            <x-input-error class="mt-2" :messages="$errors->get('tanggal_mulai')" />
        </div>

        <div>
            <x-input-label for="tanggal_selesai" :value="__('Tanggal Selesai')" />
            <x-text-input id="tanggal_selesai" name="tanggal_selesai" type="date" class="mt-1 block w-full" :value="old('tanggal_selesai', $diskon->tanggal_selesai?->format('Y-m-d'))" required />
            <x-input-error class="mt-2" :messages="$errors->get('tanggal_selesai')" />
        </div>
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
