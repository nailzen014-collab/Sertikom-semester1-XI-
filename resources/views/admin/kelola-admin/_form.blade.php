@php
    $sedangDiubah = $admin->exists;
@endphp

<div class="space-y-6">
    <div>
        <x-input-label for="nama" :value="__('Nama')" />
        <x-text-input id="nama" name="nama" type="text" class="mt-1 block w-full" :value="old('nama', $admin->nama)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('nama')" />
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $admin->email)" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div>
            <x-input-label for="no_hp" :value="__('No. HP')" />
            <x-text-input id="no_hp" name="no_hp" type="text" class="mt-1 block w-full" :value="old('no_hp', $admin->no_hp)" />
            <x-input-error class="mt-2" :messages="$errors->get('no_hp')" />
        </div>
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="password" :value="__('Kata sandi')" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" :required="! $sedangDiubah" autocomplete="new-password" />
            <x-input-error class="mt-2" :messages="$errors->get('password')" />
        </div>

        <div>
            <x-input-label for="password_confirmation" :value="__('Ulangi kata sandi')" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" :required="! $sedangDiubah" autocomplete="new-password" />
        </div>
    </div>

    @if ($sedangDiubah)
        <p class="text-xs text-gray-500">Kosongkan kata sandi jika tidak ingin mengubahnya.</p>
    @endif
</div>
