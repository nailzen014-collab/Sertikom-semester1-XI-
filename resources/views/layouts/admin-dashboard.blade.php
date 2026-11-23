<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Dashboard Admin - The Nice Park')</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">
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
                    <a href="#ringkasan">
                        <span aria-hidden="true">⌂</span> Ringkasan
                    </a>
                    <a href="#pengajuan">
                        <span aria-hidden="true">▤</span> Pengajuan
                        @if ($pengajuanMenunggu > 0)<span class="admin-nav-count">{{ $pengajuanMenunggu }}</span>@endif
                    </a>
                    <a href="#pesanan">
                        <span aria-hidden="true">▣</span> Pesanan
                    </a>
                    <a href="#tiket-paket">
                        <span aria-hidden="true">◇</span> Tiket &amp; paket
                    </a>
                    <a href="#diskon">
                        <span aria-hidden="true">%</span> Diskon
                    </a>
                    <a href="#validasi-tiket">
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
                            <a href="#ringkasan">Ringkasan</a>
                            <a href="#pengajuan">Pengajuan ({{ $pengajuanMenunggu }})</a>
                            <a href="#pesanan">Pesanan</a>
                            <a href="#tiket-paket">Tiket &amp; paket</a>
                            <a href="#diskon">Diskon</a>
                            <a href="#validasi-tiket">Validasi tiket</a>
                        </nav>
                    </details>
                    <p class="admin-topbar-label">Ruang pengelola</p>
                    <div class="admin-topbar-actions">
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
                            <h1>Dashboard Admin</h1>
                            <p>Ringkasan pesanan, pendapatan, dan aktivitas kunjungan.</p>
                        </div>
                        <a href="#pengajuan" class="admin-heading-action">Tinjau pengajuan</a>
                    </div>
                    @yield('content')
                </main>
            </div>
        </div>
    </body>
</html>
