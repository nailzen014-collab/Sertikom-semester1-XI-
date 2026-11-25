@props(['user'])

<header class="user-topbar shadow-sm">
    <div class="user-topbar-inner">
        <a href="{{ route('landing') }}" class="user-brand">
            <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="" class="user-brand-logo">
            <span>The Nice Park <small>Area Pengunjung</small></span>
        </a>

        <nav aria-label="Navigasi utama" class="user-nav">
            <a href="{{ route('user.dashboard') }}" @class(['is-active' => request()->routeIs('user.dashboard')]) @if (request()->routeIs('user.dashboard')) aria-current="page" @endif>
                Ringkasan
            </a>
            <a href="{{ route('user.booking') }}" @class(['is-active' => request()->routeIs('user.booking')]) @if (request()->routeIs('user.booking')) aria-current="page" @endif>
                Pilih tiket
            </a>
            <a href="{{ route('user.orders') }}" @class(['is-active' => request()->routeIs('user.orders') || request()->routeIs('riwayat.*')]) @if (request()->routeIs('user.orders') || request()->routeIs('riwayat.*')) aria-current="page" @endif>
                Pesanan saya
            </a>
        </nav>

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
    </div>
</header>
