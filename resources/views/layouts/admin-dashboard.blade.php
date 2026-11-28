<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', 'Dashboard Admin - The Nice Park')</title>

        <link rel="icon" type="image/x-icon" href="{{ asset('images/tha-nice-park.jpg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/tha-nice-park.jpg') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400..700&family=Plus+Jakarta+Sans:wght@400..800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="admin-dashboard font-sans antialiased">
        <a href="#main-content" class="park-skip-link">Lewati ke konten</a>
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
                        <span class="admin-profile-role">{{ auth()->user()->labelRole() }}</span>
                    </span>
                </div>

                <nav aria-label="Navigasi admin" class="admin-sidebar-nav">
                    <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])>
                        <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/></svg>
                        Ringkasan
                    </a>
                    <a href="{{ route('admin.pengajuan.index') }}" @class(['is-active' => request()->routeIs('admin.pengajuan.*')])>
                        <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 5h16v14H4z"/><path d="m4 13 4-4 4 4 4-4 4 4"/></svg>
                        Pengajuan
                        @if ($pengajuanMenunggu > 0)<span class="admin-nav-count">{{ $pengajuanMenunggu }}</span>@endif
                    </a>
                    <a href="{{ route('admin.pesanan.index') }}" @class(['is-active' => request()->routeIs('admin.pesanan.*')])>
                        <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3z"/><path d="M9 8h6M9 12h6"/></svg>
                        Pesanan
                    </a>
                    <a href="{{ route('admin.tiket-paket.index') }}" @class(['is-active' => request()->routeIs('admin.tiket-paket.*')])>
                        <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 8a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v2a2 2 0 0 0 0 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2a2 2 0 0 0 0-4z"/><path d="M13 6v12"/></svg>
                        Tiket &amp; paket
                    </a>
                    <a href="{{ route('admin.diskon.index') }}" @class(['is-active' => request()->routeIs('admin.diskon.*')])>
                        <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m19 5-14 14"/><circle cx="7.5" cy="7.5" r="2.5"/><circle cx="16.5" cy="16.5" r="2.5"/></svg>
                        Diskon
                    </a>
                    <a href="{{ route('admin.tiket.validasi') }}" @class(['is-active' => request()->routeIs('admin.tiket.validasi*')])>
                        <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l7 3v6c0 4-3 6.5-7 8-4-1.5-7-4-7-8V6z"/><path d="m9 12 2 2 4-4"/></svg>
                        Validasi tiket
                    </a>
                    @if (auth()->user()->isSuperadmin())
                        <a href="{{ route('admin.kelola-admin.index') }}" @class(['is-active' => request()->routeIs('admin.kelola-admin.*')])>
                            <svg class="admin-nav-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M19 8v6M22 11h-6"/></svg>
                            Kelola admin
                        </a>
                    @endif
                </nav>

                <a href="{{ route('landing') }}" class="admin-sidebar-home">Lihat halaman taman</a>
            </aside>

            <div class="admin-main">
                <header class="admin-topbar">
                    <details class="admin-mobile-menu">
                        <summary aria-label="Buka navigasi admin">Menu</summary>
                        <nav aria-label="Navigasi admin seluler">
                            <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])>Ringkasan</a>
                            <a href="{{ route('admin.pengajuan.index') }}" @class(['is-active' => request()->routeIs('admin.pengajuan.*')])>Pengajuan @if ($pengajuanMenunggu > 0)({{ $pengajuanMenunggu }})@endif</a>
                            <a href="{{ route('admin.pesanan.index') }}" @class(['is-active' => request()->routeIs('admin.pesanan.*')])>Pesanan</a>
                            <a href="{{ route('admin.tiket-paket.index') }}" @class(['is-active' => request()->routeIs('admin.tiket-paket.*')])>Tiket &amp; paket</a>
                            <a href="{{ route('admin.diskon.index') }}" @class(['is-active' => request()->routeIs('admin.diskon.*')])>Diskon</a>
                            <a href="{{ route('admin.tiket.validasi') }}" @class(['is-active' => request()->routeIs('admin.tiket.validasi*')])>Validasi tiket</a>
                            @if (auth()->user()->isSuperadmin())
                                <a href="{{ route('admin.kelola-admin.index') }}" @class(['is-active' => request()->routeIs('admin.kelola-admin.*')])>Kelola admin</a>
                            @endif
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

                <main id="main-content" class="admin-content page-enter delay-1">
                    @if (session('message'))
                        <div data-flash-message="{{ session('message') }}"></div>
                    @endif
                    @if (session('error'))
                        <div data-flash-error="{{ session('error') }}"></div>
                    @endif
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
