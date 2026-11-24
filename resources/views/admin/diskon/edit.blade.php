@extends('layouts.admin-dashboard')

@section('title', 'Ubah Diskon - Admin')
@section('page-title', 'Ubah Diskon')
@section('page-description', 'Perbarui promo dan masa berlaku diskon.')
@section('page-action')
    <a href="{{ route('admin.diskon.index') }}" class="admin-heading-action">&larr; Kembali ke daftar diskon</a>
@endsection

@section('content')
    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('admin.diskon.update', $diskon) }}">
                @csrf
                @method('put')

                <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-6 py-5 sm:px-8">
                        <h2 class="text-lg font-semibold text-gray-900">Detail diskon</h2>
                        <p class="mt-1 text-sm text-gray-500">Perbarui paket sasaran, besaran potongan, atau masa berlaku promo.</p>
                    </div>

                    <div class="px-6 py-6 sm:px-8">
                        @include('admin.diskon._form')
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-100 bg-gray-50/70 px-6 py-4 sm:px-8">
                        <a href="{{ route('admin.diskon.index') }}" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 transition hover:bg-gray-50">
                            Batal
                        </a>
                        <x-primary-button>{{ __('Simpan perubahan') }}</x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection