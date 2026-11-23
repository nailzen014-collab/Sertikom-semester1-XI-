@extends('layouts.admin-dashboard')

@section('title', 'Tambah Diskon - Admin')
@section('page-title', 'Tambah Diskon')
@section('page-description', 'Buat promo untuk kategori tiket yang dipilih.')

@section('content')
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.diskon.store') }}">
                    @csrf

                    @include('admin.diskon._form')

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                        <a href="{{ route('admin.diskon.index') }}" class="text-sm text-gray-600 hover:underline">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
