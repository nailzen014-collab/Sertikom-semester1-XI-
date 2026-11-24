<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Akun Pengunjung - The Nice Park')</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400..700&family=Plus+Jakarta+Sans:wght@400..800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="user-portal font-sans antialiased">
        <a href="#main-content" class="park-skip-link">Lewati ke konten</a>
        <header class="user-topbar">
            <div class="user-topbar-inner">
                <a href="{{ route('user.dashboard') }}" class="user-brand">
                    <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="" class="user-brand-logo">
                    <span>The Nice Park <small>Area Pengunjung</small></span>
                </a>

                <nav aria-label="Navigasi akun" class="user-nav">
                    <a href="{{ route('user.dashboard') }}" @class(['is-active' => request()->routeIs('user.dashboard')])>Ringkasan</a>
                    <a href="{{ route('user.booking') }}" @class(['is-active' => request()->routeIs('user.booking')])>Pilih tiket</a>
                    <a href="{{ route('user.orders') }}" @class(['is-active' => request()->routeIs('user.orders') || request()->routeIs('riwayat.*')])>Pesanan saya</a>
                </nav>

                <div class="user-account">
                    <a href="{{ route('landing') }}" class="user-park-link">Lihat halaman taman</a>
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

        <main id="main-content" class="user-content">
            @if (session('message'))
                <div data-flash-message="{{ session('message') }}"></div>
            @endif
            @if (session('error'))
                <div data-flash-error="{{ session('error') }}"></div>
            @endif
            @yield('content')
        </main>
    </body>
</html>
