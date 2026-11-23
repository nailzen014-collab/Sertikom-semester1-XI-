<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Validasi E-Ticket</h2>
            <a href="{{ route('admin.dashboard') }}" class="text-sm text-juniper-700 hover:underline">&larr; Dashboard</a>
        </div>
    </x-slot>

    <div class="py-10">
        <section class="mx-auto max-w-xl space-y-5 px-4 sm:px-6 lg:px-8">
            <div class="rounded-lg bg-white p-6 shadow-sm sm:p-8">
                <p class="text-sm leading-6 text-gray-600">Pindai QR dengan perangkat pemindai atau ketik kode tiket. Sistem hanya menerima tiket lunas yang berlaku hari ini dan belum dipakai.</p>
                @if (session('message'))
                    <p role="status" class="mt-5 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('message') }}</p>
                @endif
                @if ($errors->any())
                    <div role="alert" class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif
                <form method="POST" action="{{ route('admin.tiket.validasi.store') }}" class="mt-6 space-y-4">
                    @csrf
                    <div>
                        <label for="kode_tiket" class="block text-sm font-medium text-gray-700">Kode tiket</label>
                        <input id="kode_tiket" name="kode_tiket" type="text" value="{{ old('kode_tiket') }}" required autofocus autocomplete="off" spellcheck="false" class="mt-1 block w-full rounded-md border-gray-300 font-mono uppercase shadow-sm focus:border-juniper-500 focus:ring-juniper-500">
                        @error('kode_tiket')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                    <button type="submit" class="w-full rounded-md bg-juniper-600 px-4 py-3 font-semibold text-white hover:bg-juniper-700 focus:outline-none focus:ring-2 focus:ring-toffee-300 focus:ring-offset-2">Validasi dan tandai dipakai</button>
                </form>
            </div>

            @if ($tiket)
                <div class="rounded-lg border border-juniper-200 bg-juniper-50 p-6">
                    <p class="text-sm font-semibold text-juniper-800">Tiket berhasil digunakan</p>
                    <p class="mt-2 font-mono text-lg font-bold text-juniper-800">{{ $tiket->kode_tiket }}</p>
                    <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                        <div><dt class="text-gray-600">Pemesan</dt><dd class="font-medium">{{ $tiket->pesanan->user->nama }}</dd></div>
                        <div><dt class="text-gray-600">Paket</dt><dd class="font-medium">{{ $tiket->pesanan->tiketPaket->nama }}</dd></div>
                        @if ($tiket->peserta)
                            <div><dt class="text-gray-600">Peserta</dt><dd class="font-medium">{{ $tiket->peserta->nama }}</dd></div>
                        @endif
                        <div><dt class="text-gray-600">Tanggal kunjungan</dt><dd class="font-medium">{{ $tiket->pesanan->tanggal_kunjungan->format('d M Y') }}</dd></div>
                    </dl>
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
