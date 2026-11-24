<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'The Nice Park'))</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400..700&family=Plus+Jakarta+Sans:wght@400..800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="auth-page font-sans antialiased">
        <main class="auth-shell">
            <section class="auth-story" style="--auth-photo: url('{{ asset('images/download.jpg') }}')">
                <a href="{{ route('landing') }}" class="auth-brand">
                    <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="" class="auth-logo">
                    <span>The Nice Park <small>Rumpin · Bogor</small></span>
                </a>
                <div class="auth-story-copy">
                    <p class="auth-eyebrow">Playground · Mini zoo · Family time</p>
                    <h1>Hari bermain yang dimulai dari sini.</h1>
                    <p>Pilih tiket keluarga atau ajukan kunjungan sekolah. Semua status pesanan tersimpan di satu tempat.</p>
                </div>
                <a href="{{ route('landing') }}" class="auth-back-link">&larr; Kembali ke beranda</a>
            </section>

            <section class="auth-content" aria-label="Formulir akun">
                <a href="{{ route('landing') }}" class="auth-mobile-brand">
                    <img src="{{ asset('images/tha-nice-park.jpg') }}" alt="" class="auth-logo">
                    <span>The Nice Park</span>
                </a>
                <div class="auth-card">
                    @yield('content', $slot ?? '')
                </div>
            </section>
        </main>
    </body>
</html>
