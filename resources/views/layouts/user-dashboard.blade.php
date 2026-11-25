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

        <x-navbar :user="auth()->user()" />

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
