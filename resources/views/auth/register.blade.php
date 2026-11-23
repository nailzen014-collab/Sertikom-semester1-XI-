@extends('layouts.guest')

@section('title', 'Daftar - The Nice Park')

@section('content')
    <div class="auth-heading">
        <p class="auth-eyebrow">Buat akun pengunjung</p>
        <h2>Mulai rencanakan kunjungan</h2>
        <p>Gunakan satu akun untuk tiket keluarga maupun pengajuan rombongan.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <div>
            <x-input-label for="nama" value="Nama lengkap" />
            <x-text-input id="nama" class="mt-2 block w-full" type="text" name="nama" :value="old('nama')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('nama')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="no_hp" value="Nomor telepon (opsional)" />
            <x-text-input id="no_hp" class="mt-2 block w-full" type="tel" name="no_hp" :value="old('no_hp')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('no_hp')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Ulangi kata sandi" />
            <x-text-input id="password_confirmation" class="mt-2 block w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full justify-center rounded-lg bg-toffee-500 px-4 py-3 text-sm font-semibold text-juniper-900 hover:bg-toffee-400 focus:ring-toffee-300">
            Buat akun
        </x-primary-button>
    </form>

    <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection
