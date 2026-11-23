@extends('layouts.guest')

@section('title', 'Masuk - The Nice Park')

@section('content')
    <x-auth-session-status class="mb-5" :status="session('status')" />

    <div class="auth-heading">
        <p class="auth-eyebrow">Selamat datang kembali</p>
        <h2>Masuk ke akun Anda</h2>
        <p>Lanjutkan pemesanan atau lihat status kunjungan.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="auth-form">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input id="email" class="mt-2 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="mt-2 block w-full" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3">
            <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-alabaster-200">
                <input id="remember_me" type="checkbox" class="rounded border-juniper-400 text-toffee-500 shadow-sm focus:ring-toffee-300" name="remember">
                Ingat saya
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-toffee-200 underline-offset-4 hover:underline focus:outline-none focus-visible:ring-2 focus-visible:ring-toffee-300" href="{{ route('password.request') }}">Lupa kata sandi?</a>
            @endif
        </div>

        <x-primary-button class="w-full justify-center rounded-lg bg-toffee-500 px-4 py-3 text-sm font-semibold text-juniper-900 hover:bg-toffee-400 focus:ring-toffee-300">
            Masuk
        </x-primary-button>
    </form>

    <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Daftar sekarang</a></p>
@endsection
