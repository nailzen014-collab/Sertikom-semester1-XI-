@extends('layouts.admin-dashboard')

@section('title', 'Profil Admin - The Nice Park')
@section('page-title', 'Profil Admin')
@section('page-description', 'Perbarui informasi akun dan keamanan Anda.')

@section('content')
    @include('profile.partials.settings')
@endsection
