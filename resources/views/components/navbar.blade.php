@props(['user'])

<header x-data="{ open: false }" class="user-topbar shadow-sm">
    <div class="user-topbar-inner">
        <!-- Brand -->
        <a href="{{ route('landing') }}" class="user-brand">
            <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="" class="user-brand-logo">
            <span>The Nice Park <small>Area Pengunjung</small></span>
        </a>

        <!-- Desktop navigation links (always visible) -->
        <nav aria-label="Navigasi utama" class="hidden sm:flex space-x-8">
            <a href="{{ route('user.dashboard') }}" class="block px-1 py-2 text-green-500 hover:text-green-700 border-b-2 border-transparent hover:border-green-200 transition-colors" @class(['border-green-700' => request()->routeIs('user.dashboard')])>
                Ringkasan
            </a>
            <a href="{{ route('user.booking') }}" class="block px-1 py-2 text-green-500 hover:text-green-700 border-b-2 border-transparent hover:border-green-200 transition-colors" @class(['border-green-700' => request()->routeIs('user.booking')])>
                Pilih tiket
            </a>
            <a href="{{ route('user.orders') }}" class="block px-1 py-2 text-green-500 hover:text-green-700 border-b-2 border-transparent hover:border-green-200 transition-colors" @class(['border-green-700' => request()->routeIs('user.orders') || request()->routeIs('riwayat.*')])>
                Pesanan saya
            </a>
</header>

        <!-- User account -->
        <div class="user-account">
            <a href="{{ route('landing') }}" class="user-park-link">Lihat halaman taman</a>
            <details class="user-account-menu">
                <summary aria-label="Menu akun {{ $user->nama }}">
                    <span>{{ $user->nama }}</span>
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

        <!-- Hamburger (mobile only) -->
        <div class="sm:hidden">
            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-green-500 hover:text-green-700 hover:bg-green-50 focus:outline-none focus:bg-green-50 focus:text-green-700 transition duration-150 ease-in-out" aria-label="Buka navigasi" aria-expanded="false" :aria-expanded="open">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile navigation menu -->
<nav x-show="open" x-transition x-cloak @click.away="open = false" aria-label="Navigasi seluler" class="hidden sm:hidden">
    <div class="pt-2 pb-3 space-y-1">
        <a href="{{ route('user.dashboard') }}" class="block px-4 py-2 text-green-500 hover:text-green-700 hover:bg-green-50" @class(['bg-green-50' => request()->routeIs('user.dashboard')])>Ringkasan</a>
        <a href="{{ route('user.booking') }}" class="block px-4 py-2 text-green-500 hover:text-green-700 hover:bg-green-50" @class(['bg-green-50' => request()->routeIs('user.booking')])>Pilih tiket</a>
        <a href="{{ route('user.orders') }}" class="block px-4 py-2 text-green-500 hover:text-green-700 hover:bg-green-50" @class(['bg-green-50' => request()->routeIs('user.orders') || request()->routeIs('riwayat.*')])>Pesanan saya</a>
    </div>
</nav>
