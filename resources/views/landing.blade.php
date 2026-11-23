@extends('layouts.public')

@section('title', 'The Nice Park - Tiket & Paket Wisata')

@section('content')
    <section
        class="relative isolate overflow-hidden bg-juniper-900 min-h-[88vh] flex items-center"
        x-data="{
            aktif: 0,
            items: JSON.parse(document.getElementById('destinasi-data').textContent),
            pilih(i) {
                this.aktif = i;
                const track = this.$refs.track;
                const el = track.children[i];
                if (el) {
                    track.scrollTo({
                        left: track.scrollLeft + el.getBoundingClientRect().left - track.getBoundingClientRect().left,
                        behavior: 'smooth',
                    });
                }
            },
            next() { this.pilih((this.aktif + 1) % this.items.length); },
            prev() { this.pilih((this.aktif - 1 + this.items.length) % this.items.length); },
            get item() { return this.items[this.aktif]; }
        }"
        @keydown.window.arrow-left.prevent="prev"
        @keydown.window.arrow-right.prevent="next"
    >
        <script type="application/json" id="destinasi-data">@json($destinasi)</script>

        @foreach ($destinasi as $i => $d)
            <div x-cloak
                class="absolute -inset-1 z-0 bg-cover bg-center transition-opacity duration-700 ease-in-out motion-reduce:transition-none"
                :class="aktif === {{ $i }} ? 'opacity-100 animate-kenburns' : 'opacity-0'"
                style="background-image: url('{{ asset($d['gambar']) }}')"></div>
        @endforeach
        <div class="absolute inset-0 z-10 bg-gradient-to-r from-juniper-900/90 via-juniper-900/60 to-juniper-900/20"></div>

        <div class="relative z-20 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 grid items-center gap-10 lg:grid-cols-2">
            <div>
                <p class="text-toffee-200 text-sm sm:text-base">Playground &amp; Mini Zoo &middot; Rumpin</p>
                <h1 class="mt-4 text-3xl sm:text-5xl lg:text-6xl font-extrabold uppercase tracking-wide text-white leading-none max-w-xl"
                    x-text="item.nama">{{ $destinasi[0]['nama'] }}</h1>
                <p class="mt-5 max-w-xl text-alabaster-200" x-text="item.deskripsi">{{ $destinasi[0]['deskripsi'] }}</p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('pesan.kategori', ['kategori' => 'umum']) }}"
                        class="inline-flex items-center gap-2 rounded-full bg-toffee-300 px-6 py-3 font-semibold text-juniper-900 transition hover:bg-toffee-200 focus:outline-none focus:ring-2 focus:ring-toffee-100 focus:ring-offset-2 focus:ring-offset-juniper-900">
                        Pesan Tiket <span aria-hidden="true">&rarr;</span>
                    </a>
                    <a href="{{ route('pesan.kategori', ['kategori' => 'rombongan']) }}"
                        class="inline-flex items-center rounded-full border border-alabaster-200/70 px-6 py-3 font-semibold text-white transition hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-toffee-100 focus:ring-offset-2 focus:ring-offset-juniper-900">
                        Ajukan Paket Rombongan
                    </a>
                </div>

                <div class="mt-8 flex items-center gap-1.5">
                    <template x-for="(d, i) in items" :key="i">
                        <button type="button" @click="pilih(i)" :aria-label="'Lihat ' + d.nama"
                            class="h-1.5 rounded-full transition-all duration-300"
                            :class="aktif === i ? 'w-6 bg-toffee-300' : 'w-1.5 bg-white/40 hover:bg-white/70'"></button>
                    </template>
                </div>
            </div>

            <div class="relative">
                <div class="mb-2 flex items-center justify-between text-xs text-white/80">
                    <span x-text="String(aktif + 1).padStart(2, '0') + ' / ' + String(items.length).padStart(2, '0')"></span>
                    <div class="flex gap-2">
                        <button type="button" @click="prev" aria-label="Destinasi sebelumnya"
                            class="h-9 w-9 rounded-full border border-white/30 bg-white/10 backdrop-blur transition hover:bg-white/20">&larr;</button>
                        <button type="button" @click="next" aria-label="Destinasi berikutnya"
                            class="h-9 w-9 rounded-full border border-white/30 bg-white/10 backdrop-blur transition hover:bg-white/20">&rarr;</button>
                    </div>
                </div>

                <div x-ref="track" class="no-scrollbar flex gap-4 overflow-x-auto snap-x snap-mandatory pb-4">
                    @foreach ($destinasi as $i => $d)
                        <button type="button" @click="pilih({{ $i }})"
                            class="group relative aspect-[3/4] w-36 shrink-0 snap-start overflow-hidden rounded-2xl shadow-xl ring-1 ring-white/20 transition-transform duration-500 sm:w-44"
                            :class="aktif === {{ $i }} ? 'scale-100' : 'scale-95 opacity-80'">
                            <img src="{{ asset($d['gambar']) }}" alt="{{ $d['nama'] }}"
                                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" loading="lazy">
                            <span class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/85 to-transparent p-3 text-left text-xs font-medium text-white">
                                {{ $d['nama'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="tentang" class="scroll-mt-20 bg-juniper-800 text-alabaster-100">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-14 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
            <div>
                <p class="text-sm font-semibold text-toffee-200">The Nice Park Rumpin</p>
                <h2 class="mt-2 text-3xl font-bold text-alabaster-50">Satu hari untuk bermain dan belajar</h2>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-alabaster-200">
                    Nikmati playground indoor dan outdoor, mini zoo edukatif, serta area kuliner. Taman keluarga di Rumpin ini cocok untuk liburan bersama maupun kegiatan sekolah.
                </p>
                <ul class="mt-5 flex flex-wrap gap-2 text-sm text-alabaster-100">
                    <li class="rounded-full bg-juniper-700 px-3 py-1.5">Playground</li>
                    <li class="rounded-full bg-juniper-700 px-3 py-1.5">Mini zoo</li>
                    <li class="rounded-full bg-juniper-700 px-3 py-1.5">Foodcourt</li>
                    <li class="rounded-full bg-juniper-700 px-3 py-1.5">Musholla</li>
                </ul>
            </div>
            <div class="rounded-xl border border-juniper-600 bg-juniper-900 p-6">
                <h3 class="font-semibold text-alabaster-50">Rencanakan kunjungan</h3>
                <dl class="mt-4 space-y-4 text-sm">
                    <div>
                        <dt class="text-alabaster-300">Lokasi</dt>
                        <dd class="mt-1 text-alabaster-100">Kp Cisirung, RT.003/RW.001, Rabak, Kec. Rumpin, Kabupaten Bogor, Jawa Barat 16350</dd>
                    </div>
                    <div>
                        <dt class="text-alabaster-300">Jam operasional</dt>
                        <dd class="mt-1 text-alabaster-100">Jadwal dapat berubah; periksa kanal resmi sebelum berkunjung.</dd>
                    </div>
                </dl>
                <a href="https://www.google.com/maps/dir/?api=1&amp;destination=-6.4708883,106.6457887" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex font-semibold text-toffee-200 underline underline-offset-4 hover:text-toffee-100">Lihat petunjuk arah</a>
            </div>
        </div>
    </section>

    <section id="ketentuan" class="scroll-mt-20 bg-toffee-700 text-alabaster-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <h2 class="text-2xl font-bold text-white">Ketentuan Pemilihan Tiket</h2>
            <p class="mt-2 max-w-2xl text-sm text-alabaster-200">Pilih kategori berdasarkan jumlah peserta. Diskon hanya berlaku sesuai kategori dan periode yang ditetapkan.</p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-juniper-600 bg-juniper-800/90 p-6">
                    <h3 class="font-semibold text-toffee-200">Tiket Umum</h3>
                    <p class="mt-2 text-sm text-alabaster-200">
                        Untuk pengunjung perorangan atau keluarga. Jumlah tiket <strong class="text-white">maksimal 10</strong> per pesanan.
                        Tidak memerlukan persetujuan admin. Diskon umum aktif diterapkan otomatis.
                    </p>
                    <a href="{{ route('pesan.kategori', ['kategori' => 'umum']) }}" class="mt-5 inline-flex rounded-md bg-toffee-300 px-4 py-2 text-sm font-semibold text-juniper-900 hover:bg-toffee-200">Pilih tiket umum</a>
                </div>
                <div id="rombongan" class="scroll-mt-24 rounded-lg border border-juniper-600 bg-juniper-800/90 p-6">
                    <h3 class="font-semibold text-toffee-200">Paket Rombongan</h3>
                    <p class="mt-2 text-sm text-alabaster-200">
                        Untuk kunjungan sekolah atau rombongan dengan jumlah <strong class="text-white">lebih dari 10</strong> tiket.
                        Pengajuan harus disetujui admin sebelum pembayaran. Diskon rombongan aktif terpisah dari diskon umum.
                    </p>
                    <a href="{{ route('pesan.kategori', ['kategori' => 'rombongan']) }}" class="mt-5 inline-flex rounded-md bg-toffee-300 px-4 py-2 text-sm font-semibold text-juniper-900 hover:bg-toffee-200">Ajukan paket rombongan</a>
                </div>
            </div>
        </div>
    </section>

    <section id="katalog" class="bg-juniper-900 py-14 scroll-mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-alabaster-50">Katalog Tiket &amp; Paket</h2>

            @foreach ($kategori as $kategoriKey => $kategoriLabel)
                <div class="mt-10">
                    <div class="flex flex-wrap items-baseline justify-between gap-2 border-b border-alabaster-300 pb-3">
                        <h3 class="text-xl font-semibold text-toffee-200">
                            {{ $kategoriLabel }}
                            <span class="text-sm font-normal text-alabaster-200">
                                ({{ $kategoriKey === \App\Models\TiketPaket::KATEGORI_UMUM ? 'maksimal 10 tiket' : 'lebih dari 10 tiket' }})
                            </span>
                        </h3>
                    </div>

                    @php($diskonKategori = $diskon->get($kategoriKey, collect()))
                    @if ($diskonKategori->isNotEmpty())
                        <ul class="mt-3 flex flex-wrap gap-2">
                            @foreach ($diskonKategori as $item)
                                <li class="rounded-full bg-toffee-500/30 text-alabaster-100 text-sm px-3 py-1">
                                    {{ $item->nama }}:
                                    @if ($item->tipe === \App\Models\Diskon::TIPE_PERSEN)
                                        {{ $item->nilai }}%
                                    @else
                                        Rp {{ number_format($item->nilai, 0, ',', '.') }}
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mt-3 text-sm text-alabaster-200">Belum ada diskon aktif untuk kategori ini.</p>
                    @endif

                    <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @forelse ($tiketPaket->get($kategoriKey, collect()) as $tiket)
                            <div class="rounded-lg border border-alabaster-200 bg-alabaster-50 p-6 flex flex-col shadow-sm">
                                <p class="text-xs font-semibold text-juniper-700">{{ $kategoriLabel }}</p>
                                <h4 class="mt-1 font-semibold text-gray-900">{{ $tiket->nama }}</h4>

                                <p class="mt-1 text-2xl font-bold text-juniper-700">
                                    Rp {{ number_format($tiket->harga, 0, ',', '.') }}
                                    <span class="text-sm font-normal text-gray-500">/ orang</span>
                                </p>

                                @if ($tiket->deskripsi)
                                    <p class="mt-3 text-sm text-gray-600">{{ $tiket->deskripsi }}</p>
                                @endif

                                @if ($tiket->fasilitas)
                                    <p class="mt-3 text-sm text-gray-600">
                                        <span class="font-medium text-gray-700">Fasilitas:</span> {{ $tiket->fasilitas }}
                                    </p>
                                @endif

                                <p class="mt-3 text-sm text-gray-500">Kuota harian: {{ $tiket->kuota_harian }} tiket</p>

                                <div class="mt-auto pt-5">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('katalog.show', $tiket) }}" class="inline-flex rounded-md border border-juniper-600 px-4 py-2 text-sm font-semibold text-juniper-700 hover:bg-juniper-100">
                                            Lihat detail
                                        </a>
                                        <a href="{{ route('pesan.kategori', ['kategori' => $tiket->kategori]) }}" class="inline-flex rounded-md bg-juniper-600 px-4 py-2 text-sm font-semibold text-white hover:bg-juniper-700">
                                            Pilih kategori
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-alabaster-200">Belum ada tiket/paket aktif untuk kategori ini.</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section id="cara-memesan" class="scroll-mt-20 bg-alabaster-50 py-14 text-juniper-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <p class="text-sm font-semibold text-toffee-700">Alur pemesanan</p>
            <h2 class="mt-2 text-3xl font-bold">Dari pilih tiket sampai masuk taman</h2>
            <div class="mt-8 grid gap-6 lg:grid-cols-2">
                <article class="rounded-xl border border-juniper-200 bg-white p-6">
                    <h3 class="text-xl font-semibold">Tiket umum</h3>
                    <ol class="mt-5 grid gap-3 text-sm sm:grid-cols-3">
                        <li class="rounded-lg bg-juniper-50 p-4"><span class="font-semibold">1. Pilih</span><p class="mt-1 text-gray-600">Tiket, tanggal, dan jumlah 1–10 orang.</p></li>
                        <li class="rounded-lg bg-juniper-50 p-4"><span class="font-semibold">2. Bayar</span><p class="mt-1 text-gray-600">Selesaikan pembayaran melalui Midtrans.</p></li>
                        <li class="rounded-lg bg-juniper-50 p-4"><span class="font-semibold">3. Tunjukkan</span><p class="mt-1 text-gray-600">E-ticket terbit setelah pembayaran terverifikasi.</p></li>
                    </ol>
                </article>
                <article class="rounded-xl border border-toffee-200 bg-white p-6">
                    <h3 class="text-xl font-semibold">Paket rombongan</h3>
                    <ol class="mt-5 grid gap-3 text-sm sm:grid-cols-4">
                        <li class="rounded-lg bg-alabaster-50 p-4"><span class="font-semibold">1. Ajukan</span><p class="mt-1 text-gray-600">Isi sekolah, tanggal, jumlah &gt;10, dan biodata.</p></li>
                        <li class="rounded-lg bg-alabaster-50 p-4"><span class="font-semibold">2. Tinjau</span><p class="mt-1 text-gray-600">Admin menyetujui atau menolak dengan alasan.</p></li>
                        <li class="rounded-lg bg-alabaster-50 p-4"><span class="font-semibold">3. Bayar</span><p class="mt-1 text-gray-600">Pembayaran terbuka setelah disetujui.</p></li>
                        <li class="rounded-lg bg-alabaster-50 p-4"><span class="font-semibold">4. Tunjukkan</span><p class="mt-1 text-gray-600">E-ticket terbit setelah pembayaran terverifikasi.</p></li>
                    </ol>
                </article>
            </div>
        </div>
    </section>

    <section id="fasilitas" class="scroll-mt-20 bg-juniper-900 py-14 text-alabaster-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold text-toffee-200">Di dalam taman</p>
                <h2 class="mt-2 text-3xl font-bold">Ruang bermain untuk semua</h2>
                <p class="mt-3 text-sm leading-6 text-alabaster-200">Kenali suasana The Nice Park Rumpin sebelum merencanakan kunjungan.</p>
            </div>
            <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <figure class="overflow-hidden rounded-xl border border-juniper-700">
                    <img src="{{ asset('images/download.jpg') }}" alt="Area taman dan playground The Nice Park" loading="lazy" class="aspect-[4/3] w-full object-cover">
                    <figcaption class="bg-juniper-800 px-4 py-3 text-sm">Area taman dan playground</figcaption>
                </figure>
                <figure class="overflow-hidden rounded-xl border border-juniper-700">
                    <img src="{{ asset('images/thenicepark.jpg') }}" alt="Keluarga menikmati kunjungan di The Nice Park" loading="lazy" class="aspect-[4/3] w-full object-cover">
                    <figcaption class="bg-juniper-800 px-4 py-3 text-sm">Waktu bersama keluarga</figcaption>
                </figure>
                <figure class="overflow-hidden rounded-xl border border-juniper-700">
                    <img src="{{ asset('images/thnicepark.jpg') }}" alt="Wahana bermain di dalam taman" loading="lazy" class="aspect-[4/3] w-full object-cover">
                    <figcaption class="bg-juniper-800 px-4 py-3 text-sm">Wahana bermain</figcaption>
                </figure>
            </div>
        </div>
    </section>

    <section id="ketentuan-kunjungan" class="scroll-mt-20 bg-alabaster-50 py-14 text-juniper-900">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold">Ketentuan kunjungan</h2>
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <article class="rounded-xl border border-juniper-200 bg-white p-5"><h3 class="font-semibold">Tanggal dan kuota</h3><p class="mt-2 text-sm leading-6 text-gray-600">Pilih tanggal yang masih tersedia. Tanggal tutup tidak dapat dipesan dan kuota dicek sebelum pesanan dibuat.</p></article>
                <article class="rounded-xl border border-juniper-200 bg-white p-5"><h3 class="font-semibold">Pembayaran</h3><p class="mt-2 text-sm leading-6 text-gray-600">Pesanan umum dapat langsung dibayar. Rombongan harus disetujui admin lebih dahulu.</p></article>
                <article class="rounded-xl border border-juniper-200 bg-white p-5"><h3 class="font-semibold">E-ticket</h3><p class="mt-2 text-sm leading-6 text-gray-600">Tiket terbit setelah pembayaran terverifikasi dan hanya berlaku pada tanggal kunjungan.</p></article>
                <article class="rounded-xl border border-juniper-200 bg-white p-5"><h3 class="font-semibold">Satu kali pakai</h3><p class="mt-2 text-sm leading-6 text-gray-600">Tunjukkan QR atau kode tiket saat masuk. Tiket yang sudah digunakan tidak bisa dipakai kembali.</p></article>
            </div>
        </div>
    </section>

    <section id="faq" class="scroll-mt-20 bg-juniper-800 py-14 text-alabaster-50">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold">Pertanyaan yang sering diajukan</h2>
            <div class="mt-6 divide-y divide-juniper-600 rounded-xl border border-juniper-600">
                <details class="group p-5">
                    <summary class="cursor-pointer font-semibold focus:outline-none focus-visible:ring-2 focus-visible:ring-toffee-300">Kapan e-ticket terbit?</summary>
                    <p class="mt-3 text-sm leading-6 text-alabaster-200">Setelah status pembayaran berhasil diverifikasi oleh sistem. Tiket dapat dilihat dari riwayat pesanan.</p>
                </details>
                <details class="group p-5">
                    <summary class="cursor-pointer font-semibold focus:outline-none focus-visible:ring-2 focus-visible:ring-toffee-300">Bagaimana jika pengajuan rombongan ditolak?</summary>
                    <p class="mt-3 text-sm leading-6 text-alabaster-200">Alasan penolakan admin tampil di detail pesanan pada halaman riwayat. Pesanan yang ditolak tidak dapat dibayar.</p>
                </details>
                <details class="group p-5">
                    <summary class="cursor-pointer font-semibold focus:outline-none focus-visible:ring-2 focus-visible:ring-toffee-300">Bisakah saya mengganti nama peserta?</summary>
                    <p class="mt-3 text-sm leading-6 text-alabaster-200">Perubahan nama mandiri belum tersedia. Hubungi kanal resmi The Nice Park sebelum hari kunjungan.</p>
                </details>
            </div>
        </div>
    </section>

    <section class="bg-toffee-700 text-white">
        <div id="rombongan-cta" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
            <div>
                <h2 class="text-2xl font-bold">Rencanakan kunjungan sekolah Anda</h2>
                <p class="mt-2 text-alabaster-100 max-w-xl">
                    Ajukan paket rombongan lebih dari 10 peserta, isi biodata siswa, lalu bayar setelah disetujui admin.
                </p>
            </div>
            <a href="{{ route('pesan.kategori', ['kategori' => 'rombongan']) }}" class="shrink-0 px-6 py-3 rounded-md bg-toffee-300 text-juniper-900 font-semibold hover:bg-toffee-200">
                Mulai Ajukan
            </a>
        </div>
    </section>
@endsection
