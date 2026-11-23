<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Dashboard Admin - The Nice Park')</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="admin-dashboard font-sans antialiased">
        <div class="admin-shell">
            <aside class="admin-sidebar">
                <a href="{{ route('admin.dashboard') }}" class="admin-brand">
                    <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="" class="admin-brand-logo">
                    <span>The Nice Park</span>
                </a>

                <div class="admin-profile">
                    <span class="admin-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(auth()->user()->nama, 0, 1)) }}</span>
                    <span>
                        <span class="admin-profile-name">{{ auth()->user()->nama }}</span>
                        <span class="admin-profile-role">Administrator</span>
                    </span>
                </div>

                <nav aria-label="Navigasi admin" class="admin-sidebar-nav">
                    <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])>
                        <span aria-hidden="true">⌂</span> Ringkasan
                    </a>
                    <a href="{{ route('admin.pengajuan.index') }}" @class(['is-active' => request()->routeIs('admin.pengajuan.*')])>
                        <span aria-hidden="true">▤</span> Pengajuan
                        @if ($pengajuanMenunggu > 0)<span class="admin-nav-count">{{ $pengajuanMenunggu }}</span>@endif
                    </a>
                    <a href="{{ route('admin.pesanan.index') }}" @class(['is-active' => request()->routeIs('admin.pesanan.*')])>
                        <span aria-hidden="true">▣</span> Pesanan
                    </a>
                    <a href="{{ route('admin.tiket-paket.index') }}" @class(['is-active' => request()->routeIs('admin.tiket-paket.*')])>
                        <span aria-hidden="true">◇</span> Tiket &amp; paket
                    </a>
                    <a href="{{ route('admin.diskon.index') }}" @class(['is-active' => request()->routeIs('admin.diskon.*')])>
                        <span aria-hidden="true">%</span> Diskon
                    </a>
                    <a href="{{ route('admin.tiket.validasi') }}" @class(['is-active' => request()->routeIs('admin.tiket.validasi*')])>
                        <span aria-hidden="true">✓</span> Validasi tiket
                    </a>
                </nav>

                <a href="{{ route('landing') }}" class="admin-sidebar-home">Lihat halaman taman</a>
            </aside>

            <div class="admin-main">
                <header class="admin-topbar">
                    <details class="admin-mobile-menu">
                        <summary aria-label="Buka navigasi admin">Menu</summary>
                        <nav aria-label="Navigasi admin seluler">
                            <a href="{{ route('admin.dashboard') }}">Ringkasan</a>
                            <a href="{{ route('admin.pengajuan.index') }}">Pengajuan ({{ $pengajuanMenunggu }})</a>
                            <a href="{{ route('admin.pesanan.index') }}">Pesanan</a>
                            <a href="{{ route('admin.tiket-paket.index') }}">Tiket &amp; paket</a>
                            <a href="{{ route('admin.diskon.index') }}">Diskon</a>
                            <a href="{{ route('admin.tiket.validasi') }}">Validasi tiket</a>
                        </nav>
                    </details>
                    <p class="admin-topbar-label">Ruang pengelola</p>
                    <div class="admin-topbar-actions">
                        <a href="{{ route('profile.edit') }}" class="admin-topbar-link">Profil</a>
                        <a href="{{ route('landing') }}" class="admin-topbar-link">Lihat situs</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="admin-topbar-link">Keluar</button>
                        </form>
                    </div>
                </header>

                <main class="admin-content">
                    <div class="admin-page-heading">
                        <div>
                            <p class="admin-eyebrow">The Nice Park · Rumpin</p>
                            <h1>@yield('page-title', 'Dashboard Admin')</h1>
                            <p>@yield('page-description', 'Ruang pengelolaan The Nice Park.')</p>
                        </div>
                        @hasSection('page-action')
                            <div>@yield('page-action')</div>
                        @endif
                    </div>
                    @if (session('message'))
                        <div role="status" class="mb-5 rounded-md border border-juniper-200 bg-juniper-50 px-4 py-3 text-sm text-juniper-800">{{ session('message') }}</div>
                    @endif
                    @if ($errors->any())
                        <div role="alert" class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            @foreach ($errors->all() as $error)<p>{{ $error }}</p>@endforeach
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
