@extends('layouts.user-dashboard')

@section('title', 'Profil Saya - The Nice Park')

@section('content')
    <div class="user-welcome">
        <div>
            <p class="user-eyebrow">Area pengunjung</p>
            <h1>Profil saya</h1>
            <p>Perbarui informasi akun dan keamanan Anda.</p>
        </div>
    </div>

    <div class="mt-6">
        @include('profile.partials.settings')
    </div>
@endsection
