<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'The Nice Park'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=nunito:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="site-public font-sans antialiased bg-alabaster-50 text-gray-800 @yield('body-class')">
        <header x-data="{ menuOpen: false }" class="public-header sticky top-0 z-30">
            <div class="public-header__inner">
                <a href="{{ route('landing') }}" class="public-brand">
                    <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="Logo The Nice Park" class="public-brand__logo">
                    <span>The Nice Park</span>
                </a>

                <button type="button" class="public-menu-toggle" @click="menuOpen = ! menuOpen" :aria-expanded="menuOpen" :aria-label="menuOpen ? 'Tutup navigasi' : 'Buka navigasi'" aria-controls="site-navigation">
                    <span class="sr-only" x-text="menuOpen ? 'Tutup navigasi' : 'Buka navigasi'">Buka navigasi</span>
                    <svg x-show="!menuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-cloak x-show="menuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="m6 6 12 12M18 6 6 18"/></svg>
                </button>

                <nav id="site-navigation" aria-label="Navigasi utama" class="public-nav" :class="menuOpen ? 'is-open' : ''">
                    <a href="{{ route('landing') }}" @class(['public-nav-link', 'is-current' => request()->routeIs('landing')]) @if (request()->routeIs('landing')) aria-current="page" @endif>Beranda</a>
                    <a href="{{ route('landing') }}#katalog" class="public-nav-link">Tiket</a>
                    <a href="{{ route('landing') }}#rombongan" class="public-nav-link">Paket Rombongan</a>
                    <a href="{{ route('landing') }}#ketentuan" class="public-nav-link">Ketentuan</a>
                    <a href="{{ route('landing') }}#kontak" class="public-nav-link">Kontak</a>
                    @auth
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="public-nav-link">Admin</a>
                        @else
                            <a href="{{ route('riwayat.index') }}" class="public-nav-link">Riwayat</a>
                            <details class="public-account-menu relative">
                                <summary>
                                    {{ Auth::user()->nama }}
                                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m4 6 4 4 4-4"/></svg>
                                </summary>
                                <div class="public-account-dropdown">
                                    <a href="{{ route('profile.edit') }}">Profil saya</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit">Keluar</button>
                                    </form>
                                </div>
                            </details>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="public-nav-link">Masuk</a>
                        <a href="{{ route('register') }}" class="public-nav-cta">Daftar</a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            @yield('content')
        </main>

        <footer id="kontak" class="public-footer scroll-mt-20 bg-juniper-900 text-alabaster-100">
            <div class="mx-auto grid max-w-7xl gap-8 px-4 py-10 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                    <div class="flex items-center gap-2">
                        <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="" class="h-9 w-9 rounded-full bg-white object-contain">
                        <span class="font-semibold">The Nice Park &middot; Rumpin</span>
                    </div>
                    <p class="mt-4 max-w-sm text-sm leading-6 text-alabaster-200">Playground indoor dan outdoor, mini zoo, serta area kuliner untuk kunjungan keluarga dan rombongan sekolah.</p>
                </div>
                <div>
                    <h2 class="font-semibold text-alabaster-50">Kunjungi kami</h2>
                    <address class="mt-3 not-italic text-sm leading-6 text-alabaster-200">Kp Cisirung, RT.003/RW.001, Rabak, Kec. Rumpin, Kabupaten Bogor, Jawa Barat 16350</address>
                    <p class="mt-2 text-sm text-alabaster-200">Jam operasional dapat berubah. Periksa kanal resmi sebelum berkunjung.</p>
                    <a class="mt-3 inline-flex text-sm font-semibold text-toffee-200 hover:text-toffee-100" href="https://www.google.com/maps/dir/?api=1&amp;destination=-6.4708883,106.6457887" target="_blank" rel="noopener noreferrer">Petunjuk arah</a>
                </div>
                <div>
                    <h2 class="font-semibold text-alabaster-50">Tautan cepat</h2>
                    <ul class="mt-3 space-y-2 text-sm text-alabaster-200">
                        <li><a href="{{ route('landing') }}#katalog" class="hover:text-toffee-200">Katalog tiket</a></li>
                        <li><a href="{{ route('landing') }}#ketentuan" class="hover:text-toffee-200">Ketentuan kunjungan</a></li>
                        <li><a href="https://www.instagram.com/theniceparkrumpin/" target="_blank" rel="noopener noreferrer" class="hover:text-toffee-200">Instagram The Nice Park Rumpin</a></li>
                        <li><a href="https://www.tiktok.com/@theniceparkrumpin6" target="_blank" rel="noopener noreferrer" class="hover:text-toffee-200">TikTok The Nice Park Rumpin</a></li>
                    </ul>
                </div>
            </div>
            <p class="border-t border-juniper-700 px-4 py-4 text-center text-xs text-alabaster-200">&copy; {{ date('Y') }} The Nice Park. Playground &amp; Mini Zoo.</p>
        </footer>
    </body>
</html>
