<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Akun Pengunjung - The Nice Park')</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="user-portal font-sans antialiased">
        <header class="user-topbar">
            <div class="user-topbar-inner">
                <a href="{{ route('user.dashboard') }}" class="user-brand">
                    <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="" class="user-brand-logo">
                    <span>The Nice Park <small>Area Pengunjung</small></span>
                </a>

                <nav aria-label="Navigasi akun" class="user-nav">
                    <a href="{{ route('user.dashboard') }}#pilih-tiket">Pilih tiket</a>
                    <a href="{{ route('user.dashboard') }}#pesanan">Pesanan saya</a>
                    <a href="{{ route('landing') }}#ketentuan-kunjungan">Info &amp; ketentuan</a>
                </nav>

                <div class="user-account">
                    <details class="user-account-menu">
                        <summary aria-label="Menu akun {{ auth()->user()->nama }}">
                            <span>{{ auth()->user()->nama }}</span>
                            <svg viewBox="0 0 16 16" aria-hidden="true"><path d="m4 6 4 4 4-4"/></svg>
                        </summary>
                        <div class="user-account-dropdown">
                            <a href="{{ route('profile.edit') }}">Profil saya</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">Keluar</button>
                            </form>
                        </div>
                    </details>
                </div>
            </div>
        </header>

        <main class="user-content">
            @yield('content')
        </main>
    </body>
</html>
