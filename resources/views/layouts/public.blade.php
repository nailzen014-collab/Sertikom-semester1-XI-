<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'The Nice Park'))</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-alabaster-50 text-gray-800 @yield('body-class')">
        <header x-data="{ menuOpen: false }" class="sticky top-0 z-30 border-b border-juniper-700 bg-juniper-900/95 backdrop-blur">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ route('landing') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="Logo The Nice Park" class="h-10 w-10 rounded-full object-contain">
                    <span class="font-bold text-lg text-alabaster-100">The Nice Park</span>
                </a>

                <nav aria-label="Navigasi utama" class="hidden items-center gap-5 text-sm lg:flex">
                    <a href="{{ route('landing') }}" class="text-alabaster-100 hover:text-toffee-200">Beranda</a>
                    <a href="{{ route('landing') }}#katalog" class="text-alabaster-100 hover:text-toffee-200">Tiket</a>
                    <a href="{{ route('landing') }}#rombongan" class="text-alabaster-100 hover:text-toffee-200">Paket Rombongan</a>
                    <a href="{{ route('landing') }}#ketentuan" class="text-alabaster-100 hover:text-toffee-200">Ketentuan</a>
                    <a href="{{ route('landing') }}#kontak" class="text-alabaster-100 hover:text-toffee-200">Kontak</a>
                    @auth
                        @if (Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="text-alabaster-100 hover:text-toffee-200">Admin</a>
                        @else
                            <a href="{{ route('riwayat.index') }}" class="text-alabaster-100 hover:text-toffee-200">Riwayat</a>
                            <details class="public-account-menu relative">
                                <summary class="flex cursor-pointer list-none items-center gap-2 text-alabaster-100 hover:text-toffee-200">
                                    {{ Auth::user()->nama }}
                                    <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m4 6 4 4 4-4"/></svg>
                                </summary>
                                <div class="absolute right-0 top-full z-50 mt-2 w-48 rounded-lg border border-juniper-700 bg-juniper-900 p-2 shadow-xl">
                                    <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 text-alabaster-100 hover:bg-juniper-700">Profil saya</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full rounded-md px-3 py-2 text-left text-alabaster-100 hover:bg-juniper-700">Keluar</button>
                                    </form>
                                </div>
                            </details>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-alabaster-100 hover:text-toffee-200">Masuk</a>
                        <a href="{{ route('register') }}" class="rounded-md bg-toffee-300 px-4 py-2 font-semibold text-juniper-900 hover:bg-toffee-200">Daftar</a>
                    @endauth
                </nav>

                <button type="button" class="inline-flex items-center justify-center rounded-md border border-juniper-500 p-2 text-alabaster-100 hover:bg-juniper-700 focus:outline-none focus:ring-2 focus:ring-toffee-300 lg:hidden" @click="menuOpen = ! menuOpen" :aria-expanded="menuOpen" aria-controls="mobile-navigation">
                    <span class="sr-only">Buka navigasi</span>
                    <svg x-show="!menuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-cloak x-show="menuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-width="2" d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>
            <nav id="mobile-navigation" aria-label="Navigasi seluler" x-cloak x-show="menuOpen" class="space-y-1 border-t border-juniper-700 px-4 py-3 text-sm lg:hidden">
                <a href="{{ route('landing') }}" class="block rounded-md px-3 py-2 text-alabaster-100 hover:bg-juniper-700">Beranda</a>
                <a href="{{ route('landing') }}#katalog" class="block rounded-md px-3 py-2 text-alabaster-100 hover:bg-juniper-700">Tiket</a>
                <a href="{{ route('landing') }}#rombongan" class="block rounded-md px-3 py-2 text-alabaster-100 hover:bg-juniper-700">Paket Rombongan</a>
                <a href="{{ route('landing') }}#ketentuan" class="block rounded-md px-3 py-2 text-alabaster-100 hover:bg-juniper-700">Ketentuan</a>
                <a href="{{ route('landing') }}#kontak" class="block rounded-md px-3 py-2 text-alabaster-100 hover:bg-juniper-700">Kontak</a>
                @auth
                    @if (Auth::user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="block rounded-md px-3 py-2 text-alabaster-100 hover:bg-juniper-700">Admin</a>
                    @else
                        <a href="{{ route('riwayat.index') }}" class="block rounded-md px-3 py-2 text-alabaster-100 hover:bg-juniper-700">Riwayat</a>
                        <details class="public-account-menu rounded-md border border-juniper-700">
                            <summary class="flex cursor-pointer list-none items-center justify-between rounded-md px-3 py-2 text-alabaster-100 hover:bg-juniper-700">
                                {{ Auth::user()->nama }}
                                <svg class="h-4 w-4" viewBox="0 0 16 16" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m4 6 4 4 4-4"/></svg>
                            </summary>
                            <div class="space-y-1 px-2 pb-2">
                                <a href="{{ route('profile.edit') }}" class="block rounded-md px-3 py-2 text-alabaster-100 hover:bg-juniper-700">Profil saya</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full rounded-md px-3 py-2 text-left text-alabaster-100 hover:bg-juniper-700">Keluar</button>
                                </form>
                            </div>
                        </details>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block rounded-md px-3 py-2 text-alabaster-100 hover:bg-juniper-700">Masuk</a>
                    <a href="{{ route('register') }}" class="block rounded-md px-3 py-2 text-toffee-200 hover:bg-juniper-700">Daftar</a>
                @endauth
            </nav>
        </header>

        <main>
            @yield('content')
        </main>

        <footer id="kontak" class="scroll-mt-20 bg-juniper-900 text-alabaster-100">
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
