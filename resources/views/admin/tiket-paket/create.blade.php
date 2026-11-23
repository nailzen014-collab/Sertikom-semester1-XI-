@extends('layouts.admin-dashboard')

@section('title', 'Tambah Tiket/Paket - Admin')
@section('page-title', 'Tambah Tiket/Paket')
@section('page-description', 'Tambahkan tiket atau paket kunjungan baru.')

@section('content')
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.tiket-paket.store') }}">
                    @csrf

                    @include('admin.tiket-paket._form')

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                        <a href="{{ route('admin.tiket-paket.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
