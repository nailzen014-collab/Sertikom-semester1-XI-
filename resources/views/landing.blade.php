@extends('layouts.public')

@section('title', 'The Nice Park - Tiket & Paket Wisata')

@section('content')
    @php($destinasiImages = array_map(fn ($destination) => asset($destination['gambar']), $destinasi))

    <section
        class="park-hero park-entrance park-entrance--delay-1"
        x-data="{
            aktif: 0,
            items: JSON.parse(document.getElementById('destinasi-data').textContent),
            gambar: JSON.parse(document.getElementById('destinasi-images').textContent),
            imageA: 0,
            imageB: 0,
            copyA: 0,
            copyB: 0,
            activeImage: 'a',
            activeCopy: 'a',
            timer: null,
            pausedExplicit: false,
            pointerInside: false,
            focusStopped: false,
            documentHidden: document.visibilityState === 'hidden',
            reducedMotion: window.matchMedia('(prefers-reduced-motion: reduce)').matches,
            reduceMedia: null,
            visibilityHandler: null,
            motionHandler: null,
            clearTimer() {
                if (this.timer !== null) {
                    window.clearTimeout(this.timer);
                    this.timer = null;
                }
            },
            canRotate() {
                return !this.pausedExplicit && !this.pointerInside && !this.focusStopped && !this.documentHidden && !this.reducedMotion;
            },
            schedule() {
                this.clearTimer();
                if (!this.canRotate()) {
                    return;
                }
                this.timer = window.setTimeout(() => this.next(), 7000);
            },
            isPlaybackPaused() {
                return this.pausedExplicit || this.pointerInside || this.focusStopped || this.documentHidden || this.reducedMotion;
            },
            togglePlayback() {
                if (this.reducedMotion) {
                    return;
                }
                if (this.pausedExplicit || this.focusStopped) {
                    this.pausedExplicit = false;
                    this.focusStopped = false;
                    this.schedule();
                    return;
                }
                this.pausedExplicit = true;
                this.clearTimer();
            },
            pointerEnter() {
                this.pointerInside = true;
                this.clearTimer();
            },
            pointerLeave() {
                this.pointerInside = false;
                this.schedule();
            },
            focusEnter() {
                this.focusStopped = true;
                this.clearTimer();
            },
            focusLeave(event) {
                if (!event.currentTarget.contains(event.relatedTarget)) {
                    this.clearTimer();
                }
            },
            visibilityChange() {
                this.documentHidden = document.visibilityState === 'hidden';
                this.documentHidden ? this.clearTimer() : this.schedule();
            },
            motionChange(event) {
                this.reducedMotion = event.matches;
                this.reducedMotion ? this.clearTimer() : this.schedule();
            },
            destroy() {
                this.clearTimer();
                if (this.reduceMedia && this.motionHandler) {
                    this.reduceMedia.removeEventListener('change', this.motionHandler);
                }
                if (this.visibilityHandler) {
                    document.removeEventListener('visibilitychange', this.visibilityHandler);
                }
            },
            init() {
                this.reduceMedia = window.matchMedia('(prefers-reduced-motion: reduce)');
                this.reducedMotion = this.reduceMedia.matches;
                this.motionHandler = (event) => this.motionChange(event);
                this.visibilityHandler = () => this.visibilityChange();
                this.reduceMedia.addEventListener('change', this.motionHandler);
                document.addEventListener('visibilitychange', this.visibilityHandler);
                this.schedule();
            },
            pilih(i) {
                i = Number(i);
                if (i === this.aktif) {
                    this.schedule();
                    return;
                }
                const incoming = i;
                if (this.activeImage === 'a') {
                    this.imageB = incoming;
                    this.activeImage = 'b';
                } else {
                    this.imageA = incoming;
                    this.activeImage = 'a';
                }
                if (this.activeCopy === 'a') {
                    this.copyB = incoming;
                    this.activeCopy = 'b';
                } else {
                    this.copyA = incoming;
                    this.activeCopy = 'a';
                }
                this.aktif = incoming;
                const track = this.$refs.track;
                const el = track.children[i];
                if (el) {
                    track.scrollTo({
                        left: track.scrollLeft + el.getBoundingClientRect().left - track.getBoundingClientRect().left,
                        behavior: 'auto',
                    });
                }
                this.schedule();
            },
            next() { this.pilih((this.aktif + 1) % this.items.length); },
            prev() { this.pilih((this.aktif - 1 + this.items.length) % this.items.length); },
            get item() { return this.items[this.aktif]; },
            get gambarAktif() { return this.gambar[this.aktif]; }
        }"
        @mouseenter="pointerEnter"
        @mouseleave="pointerLeave"
        @focusin="focusEnter"
        @focusout="focusLeave($event)"
        @alpine:destroyed="destroy"
    >
        <script type="application/json" id="destinasi-data">@json($destinasi)</script>
        <script type="application/json" id="destinasi-images">@json($destinasiImages)</script>

        <div class="park-hero__layout">
            <div class="park-hero__copy">
                <div class="park-hero__copy-content">
                    <p class="park-hero__eyebrow">Playground &amp; Mini Zoo &middot; Rumpin</p>
                    <div class="park-hero__copy-stack">
                        <div class="park-hero__copy-layer is-visible" :class="{ 'is-visible': activeCopy === 'a' }" :aria-hidden="activeCopy !== 'a'">
                            <h1 class="park-hero__title" x-text="items[copyA].nama">{{ $destinasi[0]['nama'] }}</h1>
                            <p class="park-hero__description" x-text="items[copyA].deskripsi">{{ $destinasi[0]['deskripsi'] }}</p>
                        </div>
                        <div class="park-hero__copy-layer" :class="{ 'is-visible': activeCopy === 'b' }" :aria-hidden="activeCopy !== 'b'">
                            <h1 class="park-hero__title" x-text="items[copyB].nama">{{ $destinasi[0]['nama'] }}</h1>
                            <p class="park-hero__description" x-text="items[copyB].deskripsi">{{ $destinasi[0]['deskripsi'] }}</p>
                        </div>
                    </div>
                </div>
                <div class="park-hero__actions">
                    <a href="{{ route('pesan.kategori', ['kategori' => 'umum']) }}" class="park-hero__button park-hero__button--primary">Pesan Tiket</a>
                    <a href="{{ route('pesan.kategori', ['kategori' => 'rombongan']) }}" class="park-hero__button park-hero__button--secondary">Ajukan Paket Rombongan</a>
                </div>
            </div>

            <div class="park-hero__media">
                <figure class="park-hero__photo">
                    <img
                        class="is-visible"
                        :class="{ 'is-visible': activeImage === 'a' }"
                        src="{{ $destinasiImages[0] }}"
                        :src="gambar[imageA]"
                        :alt="item.nama"
                        :aria-hidden="activeImage !== 'a'"
                        width="1200"
                        height="800"
                        fetchpriority="high"
                    >
                    <img
                        :class="{ 'is-visible': activeImage === 'b' }"
                        src="{{ $destinasiImages[0] }}"
                        :src="gambar[imageB]"
                        :alt="item.nama"
                        :aria-hidden="activeImage !== 'b'"
                        width="1200"
                        height="800"
                        loading="lazy"
                        fetchpriority="low"
                    >
                </figure>

                <div class="park-hero__controls">
                    <span aria-live="polite" x-text="String(aktif + 1).padStart(2, '0') + ' / ' + String(items.length).padStart(2, '0')"></span>
                    <div class="park-hero__arrows">
                        <button type="button" @click="togglePlayback" :disabled="reducedMotion" :aria-pressed="isPlaybackPaused()" :aria-label="reducedMotion ? 'Pemutaran otomatis dinonaktifkan oleh preferensi gerak' : (isPlaybackPaused() ? 'Lanjutkan pemutaran otomatis' : 'Jeda pemutaran otomatis')"
                            class="park-hero__pause" x-text="reducedMotion ? 'Dinonaktifkan' : (isPlaybackPaused() ? 'Lanjutkan' : 'Jeda')">Jeda</button>
                        <button type="button" @click="prev" aria-label="Destinasi sebelumnya"
                            class="park-hero__arrow">&larr;</button>
                        <button type="button" @click="next" aria-label="Destinasi berikutnya"
                            class="park-hero__arrow">&rarr;</button>
                    </div>
                </div>

                <div x-ref="track" class="park-destination-track no-scrollbar" role="group" aria-label="Pilih destinasi">
                    @foreach ($destinasi as $i => $d)
                        <button type="button" @click="pilih({{ $i }})" :aria-pressed="aktif === {{ $i }}"
                            class="park-destination-card">
                            <img src="{{ asset($d['gambar']) }}" alt="" width="300" height="200" loading="lazy" fetchpriority="low">
                            <span>
                                {{ $d['nama'] }}
                            </span>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <section id="tentang" class="scroll-mt-20 bg-alabaster-50 text-juniper-900 park-entrance park-entrance--delay-2">
        <div class="mx-auto grid max-w-7xl gap-8 px-4 py-14 sm:px-6 lg:grid-cols-[1.1fr_0.9fr] lg:px-8">
            <div>
                <p class="text-sm font-semibold text-toffee-700">The Nice Park Rumpin</p>
                <h2 class="mt-2 text-3xl font-bold text-juniper-900">Satu hari untuk bermain dan belajar</h2>
                <p class="mt-4 max-w-2xl text-sm leading-7 text-gray-600">
                    Nikmati playground indoor dan outdoor, mini zoo edukatif, serta area kuliner. Taman keluarga di Rumpin ini cocok untuk liburan bersama maupun kegiatan sekolah.
                </p>
                <ul class="mt-5 flex flex-wrap gap-2 text-sm text-juniper-800">
                    <li class="rounded-full bg-juniper-100 px-3 py-1.5">Playground</li>
                    <li class="rounded-full bg-juniper-100 px-3 py-1.5">Mini zoo</li>
                    <li class="rounded-full bg-juniper-100 px-3 py-1.5">Foodcourt</li>
                    <li class="rounded-full bg-juniper-100 px-3 py-1.5">Musholla</li>
                </ul>
            </div>
            <div class="rounded-xl border border-juniper-200 bg-white p-6">
                <h3 class="font-semibold text-juniper-900">Rencanakan kunjungan</h3>
                <dl class="mt-4 space-y-4 text-sm text-gray-600">
                    <div>
                        <dt class="text-gray-500">Lokasi</dt>
                        <dd class="mt-1 text-juniper-900">Kp Cisirung, RT.003/RW.001, Rabak, Kec. Rumpin, Kabupaten Bogor, Jawa Barat 16350</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Jam operasional</dt>
                        <dd class="mt-1 text-juniper-900">Jadwal dapat berubah; periksa kanal resmi sebelum berkunjung.</dd>
                    </div>
                </dl>
                <a href="https://www.google.com/maps/dir/?api=1&amp;destination=-6.4708883,106.6457887" target="_blank" rel="noopener noreferrer" class="mt-5 inline-flex font-semibold text-toffee-700 underline underline-offset-4 hover:text-toffee-900">Lihat petunjuk arah</a>
            </div>
        </div>
    </section>

    <section id="ketentuan" class="scroll-mt-20 bg-juniper-50 text-juniper-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
            <h2 class="text-2xl font-bold text-juniper-900">Ketentuan Pemilihan Tiket</h2>
            <p class="mt-2 max-w-2xl text-sm text-gray-600">Pilih kategori berdasarkan jumlah peserta. Diskon hanya berlaku sesuai kategori dan periode yang ditetapkan.</p>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg border border-juniper-200 bg-white p-6">
                    <h3 class="font-semibold text-juniper-800">Tiket Umum</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Untuk pengunjung perorangan atau keluarga. Jumlah tiket <strong class="text-juniper-900">maksimal 10</strong> per pesanan.
                        Tidak memerlukan persetujuan admin. Diskon umum aktif diterapkan otomatis.
                    </p>
                    <a href="{{ route('pesan.kategori', ['kategori' => 'umum']) }}" class="mt-5 inline-flex rounded-md bg-juniper-600 px-4 py-2 text-sm font-semibold text-white hover:bg-juniper-700">Pilih tiket umum</a>
                </div>
                <div id="rombongan" class="scroll-mt-24 rounded-lg border border-juniper-200 bg-white p-6">
                    <h3 class="font-semibold text-juniper-800">Paket Rombongan</h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Untuk kunjungan sekolah atau rombongan dengan jumlah <strong class="text-juniper-900">lebih dari 10</strong> tiket.
                        Pengajuan harus disetujui admin sebelum pembayaran. Diskon rombongan aktif terpisah dari diskon umum.
                    </p>
                    <a href="{{ route('pesan.kategori', ['kategori' => 'rombongan']) }}" class="mt-5 inline-flex rounded-md bg-juniper-600 px-4 py-2 text-sm font-semibold text-white hover:bg-juniper-700">Ajukan paket rombongan</a>
                </div>
            </div>
        </div>
    </section>

    <section id="katalog" class="bg-alabaster-50 py-14 scroll-mt-16 text-juniper-900 park-entrance park-entrance--delay-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold text-juniper-900">Katalog Tiket &amp; Paket</h2>

            @foreach ($kategori as $kategoriKey => $kategoriLabel)
                <div class="mt-10">
                    <div class="flex flex-wrap items-baseline justify-between gap-2 border-b border-juniper-200 pb-3">
                        <h3 class="text-xl font-semibold text-juniper-800">
                            {{ $kategoriLabel }}
                            <span class="text-sm font-normal text-gray-600">
                                ({{ $kategoriKey === \App\Models\TiketPaket::KATEGORI_UMUM ? 'maksimal 10 tiket' : 'lebih dari 10 tiket' }})
                            </span>
                        </h3>
                    </div>

                    @php($diskonKategori = $diskon->get($kategoriKey, collect()))
                    @if ($diskonKategori->isNotEmpty())
                        <ul class="mt-3 flex flex-wrap gap-2">
                            @foreach ($diskonKategori as $item)
                                <li class="rounded-full bg-toffee-100 text-toffee-800 text-sm px-3 py-1">
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
                        <p class="mt-3 text-sm text-gray-600">Belum ada diskon aktif untuk kategori ini.</p>
                    @endif

                    <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        @forelse ($tiketPaket->get($kategoriKey, collect()) as $tiket)
                            <div class="park-card-hover reveal-on-scroll rounded-xl border border-juniper-200 bg-white p-6 flex flex-col">
                                <p class="text-xs font-semibold text-toffee-700">{{ $kategoriLabel }}</p>
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
                                        <a href="{{ route('katalog.show', $tiket) }}" class="park-button-magnetic inline-flex rounded-lg border border-juniper-600 px-4 py-2 text-sm font-semibold text-juniper-700 hover:bg-juniper-50">
                                            Lihat detail
                                        </a>
                                        <a href="{{ route('pesan.kategori', ['kategori' => $tiket->kategori]) }}" class="park-button-magnetic inline-flex rounded-lg bg-juniper-600 px-4 py-2 text-sm font-semibold text-white hover:bg-juniper-700">
                                            Pilih kategori
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Belum ada tiket/paket aktif untuk kategori ini.</p>
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
                <article class="park-card-hover reveal-on-scroll rounded-xl border border-juniper-200 bg-white p-6">
                    <h3 class="text-xl font-semibold">Tiket umum</h3>
                    <ol class="mt-5 grid gap-3 text-sm sm:grid-cols-3">
                        <li class="rounded-lg bg-juniper-50 p-4 transition-colors hover:bg-juniper-100"><span class="font-semibold">1. Pilih</span><p class="mt-1 text-gray-600">Tiket, tanggal, dan jumlah 1–10 orang.</p></li>
                        <li class="rounded-lg bg-juniper-50 p-4 transition-colors hover:bg-juniper-100"><span class="font-semibold">2. Bayar</span><p class="mt-1 text-gray-600">Selesaikan pembayaran melalui Midtrans.</p></li>
                        <li class="rounded-lg bg-juniper-50 p-4 transition-colors hover:bg-juniper-100"><span class="font-semibold">3. Tunjukkan</span><p class="mt-1 text-gray-600">E-ticket terbit setelah pembayaran terverifikasi.</p></li>
                    </ol>
                </article>
                <article class="park-card-hover reveal-on-scroll rounded-xl border border-toffee-200 bg-white p-6">
                    <h3 class="text-xl font-semibold">Paket rombongan</h3>
                    <ol class="mt-5 grid gap-3 text-sm sm:grid-cols-4">
                        <li class="rounded-lg bg-alabaster-50 p-4 transition-colors hover:bg-toffee-50"><span class="font-semibold">1. Ajukan</span><p class="mt-1 text-gray-600">Isi sekolah, tanggal, jumlah &gt;10, dan biodata.</p></li>
                        <li class="rounded-lg bg-alabaster-50 p-4 transition-colors hover:bg-toffee-50"><span class="font-semibold">2. Tinjau</span><p class="mt-1 text-gray-600">Admin menyetujui atau menolak dengan alasan.</p></li>
                        <li class="rounded-lg bg-alabaster-50 p-4 transition-colors hover:bg-toffee-50"><span class="font-semibold">3. Bayar</span><p class="mt-1 text-gray-600">Pembayaran terbuka setelah disetujui.</p></li>
                        <li class="rounded-lg bg-alabaster-50 p-4 transition-colors hover:bg-toffee-50"><span class="font-semibold">4. Tunjukkan</span><p class="mt-1 text-gray-600">E-ticket terbit setelah pembayaran terverifikasi.</p></li>
                    </ol>
                </article>
            </div>
        </div>
    </section>

    <section id="fasilitas" class="scroll-mt-20 bg-juniper-900 py-14 text-alabaster-50">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl">
                <p class="text-sm font-semibold text-toffee-200">Di dalam taman</p>
                <h1 class="mt-2 text-3xl font-bold text-white">Ruang bermain untuk semua</h1>
                <p class="mt-3 text-sm leading-6 text-alabaster-200">Kenali suasana The Nice Park Rumpin sebelum merencanakan kunjungan.</p>
            </div>
            <div class="mt-7 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <figure class="group overflow-hidden rounded-xl border border-juniper-700 reveal-on-scroll">
                    <img src="{{ asset('images/download.jpg') }}" alt="Area taman dan playground The Nice Park" loading="lazy" class="aspect-[4/3] w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                    <figcaption class="bg-juniper-800 px-4 py-3 text-sm">Area taman dan playground</figcaption>
                </figure>
                <figure class="group overflow-hidden rounded-xl border border-juniper-700 reveal-on-scroll">
                    <img src="{{ asset('images/thenicepark.jpg') }}" alt="Keluarga menikmati kunjungan di The Nice Park" loading="lazy" class="aspect-[4/3] w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
                    <figcaption class="bg-juniper-800 px-4 py-3 text-sm">Waktu bersama keluarga</figcaption>
                </figure>
                <figure class="group overflow-hidden rounded-xl border border-juniper-700 reveal-on-scroll">
                    <img src="{{ asset('images/thnicepark.jpg') }}" alt="Wahana bermain di dalam taman" loading="lazy" class="aspect-[4/3] w-full object-cover transition-transform duration-500 ease-out group-hover:scale-105">
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

    <section id="faq" class="scroll-mt-20 bg-juniper-50 py-14 text-juniper-900">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold">Pertanyaan yang sering diajukan</h2>
            <div class="mt-6 divide-y divide-juniper-200 rounded-xl border border-juniper-200 bg-white">
                <details class="group p-5">
                    <summary class="cursor-pointer font-semibold focus:outline-none focus-visible:ring-2 focus-visible:ring-juniper-700">Kapan e-ticket terbit?</summary>
                    <p class="mt-3 text-sm leading-6 text-gray-600">Setelah status pembayaran berhasil diverifikasi oleh sistem. Tiket dapat dilihat dari riwayat pesanan.</p>
                </details>
                <details class="group p-5">
                    <summary class="cursor-pointer font-semibold focus:outline-none focus-visible:ring-2 focus-visible:ring-juniper-700">Bagaimana jika pengajuan rombongan ditolak?</summary>
                    <p class="mt-3 text-sm leading-6 text-gray-600">Alasan penolakan admin tampil di detail pesanan pada halaman riwayat. Pesanan yang ditolak tidak dapat dibayar.</p>
                </details>
                <details class="group p-5">
                    <summary class="cursor-pointer font-semibold focus:outline-none focus-visible:ring-2 focus-visible:ring-juniper-700">Bisakah saya mengganti nama peserta?</summary>
                    <p class="mt-3 text-sm leading-6 text-gray-600">Perubahan nama mandiri belum tersedia. Hubungi kanal resmi The Nice Park sebelum hari kunjungan.</p>
                </details>
            </div>
        </div>
    </section>

    <section class="bg-juniper-700 text-white">
        <div id="rombongan-cta" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
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
