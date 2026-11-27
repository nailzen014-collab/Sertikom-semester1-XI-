<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'The Nice Park'))</title>

        <link rel="icon" type="image/x-icon" href="{{ asset('images/tha-nice-park.jpg') }}">
        <link rel="apple-touch-icon" href="{{ asset('images/tha-nice-park.jpg') }}">

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400..700&family=Plus+Jakarta+Sans:wght@400..800&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="site-public font-sans antialiased bg-cream-50 text-green-900 @yield('body-class')">
        <a href="#main-content" class="park-skip-link">Lewati ke konten</a>
<header x-data="{ menuOpen: false }" class="public-header sticky top-0 z-30 shadow-sm">
    <div class="public-header__inner">
        <a href="{{ route('landing') }}" class="public-brand">
            <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="Logo The Nice Park" class="public-brand__logo">
            <span>The Nice Park</span>
        </a>

        <nav aria-label="Navigasi utama" class="public-nav-desktop hidden items-center gap-1 lg:flex">
            <a href="{{ route('landing') }}" @class(['public-nav-link', 'is-current' => request()->routeIs('landing')]) @if (request()->routeIs('landing')) aria-current="page" @endif>Beranda</a>
            <a href="{{ route('landing') }}#katalog" class="public-nav-link">Tiket</a>
            <a href="{{ route('landing') }}#rombongan" class="public-nav-link">Paket Rombongan</a>
            <a href="{{ route('landing') }}#ketentuan" class="public-nav-link">Ketentuan</a>
            <a href="{{ route('landing') }}#kontak" class="public-nav-link">Kontak</a>

            <span class="public-nav-divider" aria-hidden="true"></span>

            @auth
                @if (Auth::user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" @class(['public-nav-link', 'is-current' => request()->routeIs('admin.*')]) @if (request()->routeIs('admin.*')) aria-current="page" @endif>Admin</a>
                @else
                    <a href="{{ route('riwayat.index') }}" @class(['public-nav-link', 'is-current' => request()->routeIs('riwayat.*') || request()->routeIs('user.*')]) @if (request()->routeIs('riwayat.*') || request()->routeIs('user.*')) aria-current="page" @endif>Riwayat</a>
                    <details class="public-account-menu">
                        <summary>
                            {{ Auth::user()->nama }}
                            <span class="inline-flex items-center justify-center min-w-6 h-6 rounded-full bg-bordo-600 px-1.5 text-[10px] font-bold text-white leading-none shadow-sm border border-white/40">Aktif</span>
                            <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m4 6 4 4 4-4"/></svg>
                        </summary>
                        <div class="public-account-dropdown">
                            <div class="public-account-summary">
                                <p class="public-account-summary__name">{{ Auth::user()->nama }}</p>
                                <p class="public-account-summary__role">Akun pengunjung</p>
                            </div>
                            <a href="{{ route('profile.edit') }}">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="8" r="4"/></svg>
                                Profil saya
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">
                                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </details>
                @endif
            @else
                <a href="{{ route('login') }}" @class(['public-nav-link', 'is-current' => request()->routeIs('login')]) @if (request()->routeIs('login')) aria-current="page" @endif>Masuk</a>
                <a href="{{ route('register') }}" @class(['public-nav-cta', 'is-current' => request()->routeIs('register'), 'ring-2 ring-bordo-400' => request()->routeIs('register')]) @if (request()->routeIs('register')) aria-current="page" @endif>Daftar</a>
            @endauth
        </nav>

        <div class="lg:hidden">
            <button type="button" class="public-menu-toggle" @click="menuOpen = ! menuOpen" :aria-expanded="menuOpen" :aria-label="menuOpen ? 'Tutup navigasi' : 'Buka navigasi'" aria-controls="site-navigation">
                <span class="sr-only" x-text="menuOpen ? 'Tutup navigasi' : 'Buka navigasi'">Buka navigasi</span>
                <svg x-show="!menuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                <svg x-cloak x-show="menuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="m6 6 12 12M18 6 6 18"/></svg>
            </button>
        </div>
    </div>

    <nav id="site-navigation" aria-label="Navigasi utama" x-cloak x-show="menuOpen" x-transition @click.away="menuOpen = false" class="public-nav lg:hidden">
        <a href="{{ route('landing') }}" @class(['public-nav-link', 'is-current' => request()->routeIs('landing')]) @if (request()->routeIs('landing')) aria-current="page" @endif>Beranda</a>
        <a href="{{ route('landing') }}#katalog" class="public-nav-link">Tiket</a>
        <a href="{{ route('landing') }}#rombongan" class="public-nav-link">Paket Rombongan</a>
        <a href="{{ route('landing') }}#ketentuan" class="public-nav-link">Ketentuan</a>
        <a href="{{ route('landing') }}#kontak" class="public-nav-link">Kontak</a>
        @auth
            @if (Auth::user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" @class(['public-nav-link', 'is-current' => request()->routeIs('admin.*')]) @if (request()->routeIs('admin.*')) aria-current="page" @endif>Admin</a>
            @else
                <a href="{{ route('riwayat.index') }}" @class(['public-nav-link', 'is-current' => request()->routeIs('riwayat.*') || request()->routeIs('user.*')]) @if (request()->routeIs('riwayat.*') || request()->routeIs('user.*')) aria-current="page" @endif>Riwayat</a>
                <details class="public-account-menu">
                    <summary>
                        {{ Auth::user()->nama }}
                        <span class="inline-flex items-center justify-center min-w-6 h-6 rounded-full bg-bordo-600 px-1.5 text-[10px] font-bold text-white leading-none shadow-sm border border-white/40">Aktif</span>
                        <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m4 6 4 4 4-4"/></svg>
                    </summary>
                    <div class="public-account-dropdown">
                        <div class="public-account-summary">
                            <p class="public-account-summary__name">{{ Auth::user()->nama }}</p>
                            <p class="public-account-summary__role">Akun pengunjung</p>
                        </div>
                        <a href="{{ route('profile.edit') }}">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="8" r="4"/></svg>
                            Profil saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="m16 17 5-5-5-5"/><path d="M21 12H9"/></svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </details>
            @endif
        @else
            <a href="{{ route('login') }}" @class(['public-nav-link', 'is-current' => request()->routeIs('login')]) @if (request()->routeIs('login')) aria-current="page" @endif>Masuk</a>
            <a href="{{ route('register') }}" @class(['public-nav-cta', 'is-current' => request()->routeIs('register'), 'ring-2 ring-bordo-400' => request()->routeIs('register')]) @if (request()->routeIs('register')) aria-current="page" @endif>Daftar</a>
        @endauth
    </nav>
</header>

        <main id="main-content" class="page-enter delay-1">
            @if (session('message'))
                <div data-flash-message="{{ session('message') }}"></div>
            @endif
            @if (session('error'))
                <div data-flash-error="{{ session('error') }}"></div>
            @endif
            @yield('content')
        </main>

        <footer id="kontak" class="public-footer scroll-mt-20 bg-green-900 text-cream-100">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="" class="h-9 w-9 rounded-full bg-white object-contain">
                        <span class="font-semibold">The Nice Park &middot; Rumpin</span>
                    </div>
                    <p class="mt-4 max-w-sm text-sm leading-6 text-cream-200">Playground indoor dan outdoor, mini zoo, serta area kuliner untuk kunjungan keluarga dan rombongan sekolah.</p>
                </div>
                <div>
                    <h2 class="font-semibold text-cream-50">Kunjungi kami</h2>
                    <address class="mt-3 not-italic text-sm leading-6 text-cream-200">Kp Cisirung, RT.003/RW.001, Rabak, Kec. Rumpin, Kabupaten Bogor, Jawa Barat 16350</address>
                    <p class="mt-2 text-sm text-cream-200">Jam operasional dapat berubah. Periksa kanal resmi sebelum berkunjung.</p>
                    <a class="mt-3 inline-flex text-sm font-semibold text-bordo-200 hover:text-bordo-100" href="https://www.google.com/maps/dir/?api=1&destination=-6.4708883,106.6457887" target="_blank" rel="noopener noreferrer">Petunjuk arah</a>
                </div>
                <div>
                    <h2 class="font-semibold text-cream-50">Tautan cepat</h2>
                    <ul class="mt-3 space-y-2 text-sm text-cream-200">
                        <li><a href="{{ route('landing') }}#katalog" class="hover:text-bordo-200">Katalog tiket</a></li>
                        <li><a href="{{ route('landing') }}#ketentuan" class="hover:text-bordo-200">Ketentuan kunjungan</a></li>
                        <li><a href="https://www.instagram.com/theniceparkrumpin/" target="_blank" rel="noopener noreferrer" class="hover:text-bordo-200">Instagram The Nice Park Rumpin</a></li>
                        <li><a href="https://www.tiktok.com/@theniceparkrumpin6" target="_blank" rel="noopener noreferrer" class="hover:text-bordo-200">TikTok The Nice Park Rumpin</a></li>
                    </ul>
                </div>
            </div>
            <p class="border-t border-green-700 px-4 py-4 text-center text-xs text-cream-200">&copy; {{ date('Y') }} The Nice Park. Playground & Mini Zoo.</p>
        </footer>

        <script>
            document.documentElement.classList.add('is-loaded');
        </script>
    </body>
</html>