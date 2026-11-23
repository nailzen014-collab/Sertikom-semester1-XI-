# PRD - Sistem Pemesanan Tiket & Paket Wisata The Nice Park

> Dokumen ini dibuat agar AI agent dan developer memahami proyek secara utuh sebelum menulis kode.
> Bahasa antarmuka aplikasi: **Bahasa Indonesia**. Identifier kode (tabel, kolom, variabel): sesuai penamaan di dokumen ini.

## 1. Ringkasan

Aplikasi web untuk **The Nice Park (Rumpin)** agar pengunjung dapat memesan tiket umum dan paket rombongan (sekolah) secara online, membayar lewat payment gateway, dan menerima e-ticket. Admin mengelola tiket, paket, diskon, pengajuan rombongan, pesanan, dan memvalidasi tiket di pintu masuk.

Proyek ini adalah tugas LKPD (project-based learning), tetapi dirancang seperti kasus nyata dengan klien.

## 2. Latar Belakang & Masalah Klien

- Pemesanan masih manual.
- Pengunjung bingung soal pilihan paket, harga, dan cara memesan.
- Pemesanan rombongan dicatat manual oleh petugas (data sekolah, penanggung jawab, tanggal, jumlah peserta).
- Saat banyak pesanan bersamaan, petugas sulit memastikan pesanan tercatat benar dan pembayaran tepat waktu.
- Pengunjung harus kembali ke petugas untuk menanyakan status pesanan.

## 3. Tujuan Produk

1. Mempermudah pengunjung membeli tiket umum dan memesan paket rombongan secara online.
2. Menyediakan informasi tiket, paket, harga, diskon, dan ketentuan kunjungan secara jelas.
3. Membuat pencatatan terpusat, rapi, dan minim kesalahan.
4. Memudahkan admin meninjau, menyetujui, atau menolak pengajuan sekolah.
5. Menerbitkan e-ticket otomatis setelah pembayaran terverifikasi.

## 4. Role Pengguna (hanya 2)

| Role | Keterangan |
|---|---|
| **pengunjung** | Pengunjung umum **dan** guru/penanggung jawab rombongan. Tidak ada role "guru" terpisah. |
| **admin** | Pengelola The Nice Park. Termasuk memvalidasi tiket di pintu masuk. Tidak ada role "petugas" terpisah. |

## 5. Aturan Bisnis (WAJIB dipatuhi)

Ini aturan paling penting. Validasi harus ada di **server** (bukan hanya di tampilan).

1. Tiket/paket dibagi **2 kategori**: `umum` dan `rombongan`.
2. **Umum:** jumlah tiket per pesanan **maksimal 10** (1 sampai 10). Jika pengunjung memasukkan lebih dari 10, arahkan ke paket rombongan.
3. **Rombongan:** jumlah tiket **lebih dari 10** (minimal 11). Jika 10 atau kurang, arahkan ke tiket umum.
4. **Diskon** dikelola admin dan **dipisah per kategori**: diskon umum hanya berlaku untuk pesanan umum, diskon rombongan hanya untuk pesanan rombongan. Diskon punya periode dan status aktif.
5. **Rombongan wajib disetujui admin sebelum pembayaran.** Urutan: ajukan -> isi biodata -> admin tinjau -> (disetujui) -> bayar -> e-ticket. Jika ditolak, admin wajib mengisi alasan dan guru dapat melihatnya.
6. **Umum tidak perlu persetujuan:** pesan -> bayar -> e-ticket.
7. E-ticket **hanya terbit setelah pembayaran terverifikasi** (status `berhasil` dari payment gateway).
8. **Kuota harian** dicek per tanggal kunjungan. Kuota dihitung dari pesanan berstatus `lunas`. Tanggal di tabel `tanggal_tutup` tidak bisa dipesan.
9. Jumlah baris biodata peserta rombongan **harus sama** dengan `jumlah_tiket`.
10. Tiket yang sudah dipakai **tidak boleh dipakai lagi**. Validasi: kode ada, tanggal kunjungan sesuai, belum dipakai.
11. Pesanan yang tidak dibayar sampai batas waktu menjadi `kedaluwarsa`.
12. Landing page **harus menampilkan ketentuan pemilihan tiket**: umum maksimal 10, rombongan lebih dari 10, beserta informasi diskon masing-masing.

## 6. Fitur & Prioritas

Prioritas: **P1** = wajib (MVP), **P2** = penting, **P3** = tambahan. Nomor mengikuti Product Backlog.

### Pengunjung / Guru

| No | Fitur | Prioritas | Kriteria penerimaan (ringkas) |
|---|---|---|---|
| 1 | Registrasi, login, edit profil | P1 | Email unik, password di-hash, role default `pengunjung`. |
| 5 | Landing page + katalog + ketentuan | P1 | Katalog dibagi kategori umum/rombongan; tampil harga, fasilitas, diskon, dan ketentuan pemilihan tiket (aturan 12). |
| 6 | Pesan tiket umum | P1 | Pilih tiket, tanggal, jumlah (1-10); cek kuota; diskon umum otomatis; >10 diarahkan ke rombongan. |
| 7 | Pembayaran payment gateway | P1 | Status diperbarui lewat webhook; pesanan lunas hanya jika webhook valid. |
| 8 | E-ticket (kode unik/QR, PDF) | P1 | Terbit otomatis setelah pembayaran berhasil; bisa diunduh PDF. |
| 9 | Riwayat & status pesanan | P1 | Hanya melihat pesanan milik sendiri. |
| 10 | Ajukan paket rombongan | P1 | Jumlah >10; data sekolah & penanggung jawab; diskon rombongan; status `diajukan`. |
| 11 | Isi biodata peserta | P1 | Jumlah baris = jumlah tiket. |
| 12 | Import nama siswa Excel/CSV | P2 | Template unduhan; validasi jumlah baris dan format. |
| 14 | Lihat status pengajuan & bayar | P1 | Tombol bayar hanya muncul jika disetujui. |
| 15 | E-ticket rombongan 2 tampilan | P2 | Per siswa (sesuai nama) dan per rombongan (nama rombongan + data siswa). Satu data, dua tampilan. |
| 20 | Ganti nama peserta sebelum hari kunjungan | P3 | Jumlah peserta tidak berubah. |
| 21 | Notifikasi email | P3 | Opsional. |

### Admin

| No | Fitur | Prioritas | Kriteria penerimaan (ringkas) |
|---|---|---|---|
| 2 | Login admin | P1 | Halaman admin hanya untuk role `admin` (middleware). |
| 3 | CRUD tiket & paket | P1 | Kategori umum/rombongan, harga, fasilitas, kuota harian, aktif/nonaktif. |
| 4 | Kelola diskon umum & rombongan | P1 | Terpisah per kategori; periode dan status aktif. |
| 13 | Tinjau, setujui, tolak pengajuan | P1 | Tolak wajib mengisi `catatan_admin`. |
| 16 | Pantau pesanan & status pembayaran | P1 | Filter tanggal, status, kategori. |
| 17 | Chart statistik dashboard | P2 | Pesanan/pembayaran per tanggal. |
| 18 | Validasi e-ticket di pintu masuk | P2 | Input/scan kode; tolak jika tidak valid, salah tanggal, atau sudah dipakai. |
| 19 | Laporan & ekspor PDF/Excel | P3 | Rekap pesanan dan pendapatan per periode. |

## 7. Alur Utama

**Tiket umum:** landing page -> login -> pilih kategori Umum -> pilih tiket/tanggal/jumlah -> cek (<=10, kuota) -> diskon umum -> bayar -> (berhasil) e-ticket -> riwayat.

**Rombongan:** landing page -> login -> pilih kategori Rombongan -> pilih paket/jumlah/tanggal/data sekolah -> cek (>10, kuota) -> diskon rombongan -> isi biodata (manual/import) -> kirim pengajuan -> admin tinjau -> (disetujui) bayar -> (berhasil) e-ticket per siswa atau per rombongan.

**Hari kunjungan:** admin scan/input kode tiket -> cek valid, tanggal, belum dipakai -> tandai `dipakai`.

## 8. Model Data (ERD ringkas)

Relasi utama: `users 1-N pesanan`, `tiket_paket 1-N pesanan`, `diskon 0..1-N pesanan`, `pesanan 1-0..1 pengajuan_rombongan`, `pesanan 1-N peserta_rombongan`, `pesanan 1-N pembayaran`, `pesanan 1-N tiket`, `peserta_rombongan 0..1-0..1 tiket`.

| Tabel | Kolom penting |
|---|---|
| `users` | id, nama, email (unik), password, no_hp, role (`admin`/`pengunjung`) |
| `tiket_paket` | id, nama, kategori (`umum`/`rombongan`), deskripsi, fasilitas, harga, kuota_harian, status_aktif |
| `diskon` | id, admin_id, nama, kategori, tipe (`persen`/`nominal`), nilai, tanggal_mulai, tanggal_selesai, status_aktif |
| `pesanan` | id, kode_pesanan (unik), user_id, tiket_paket_id, diskon_id (null), kategori, tanggal_kunjungan, jumlah_tiket, harga_satuan, potongan_diskon, total_bayar, status |
| `pengajuan_rombongan` | id, pesanan_id, admin_id (null), nama_sekolah, penanggung_jawab, no_hp, status (`diajukan`/`disetujui`/`ditolak`), catatan_admin, ditinjau_pada |
| `peserta_rombongan` | id, pesanan_id, nama, kelas (opsional) |
| `pembayaran` | id, pesanan_id, order_id_gateway (unik), metode, jumlah, status (`pending`/`berhasil`/`gagal`/`kedaluwarsa`), dibayar_pada |
| `tiket` | id, pesanan_id, peserta_id (null), divalidasi_oleh (null), kode_tiket (unik), jenis (`individu`/`rombongan`), status (`aktif`/`dipakai`), dipakai_pada |
| `tanggal_tutup` | id, tanggal (unik), keterangan |

Catatan desain:
- E-ticket per siswa = baris `tiket` dengan `peserta_id` terisi. E-ticket per rombongan = baris `tiket` `jenis = rombongan` tanpa `peserta_id`.
- Untuk tiket umum, buat satu baris `tiket` per jumlah tiket.
- Simpan `harga_satuan`, `potongan_diskon`, `total_bayar` di `pesanan` sebagai snapshot supaya perubahan harga/diskon tidak mengubah pesanan lama.

### Status `pesanan`

`menunggu_persetujuan` -> `disetujui` -> `menunggu_pembayaran` -> `lunas`
Cabang: `ditolak` (rombongan), `kedaluwarsa` (tidak dibayar).
Pesanan umum langsung mulai dari `menunggu_pembayaran`.

## 9. Kebutuhan Non-Fungsional

- **Keamanan:** password di-hash, proteksi CSRF, middleware role untuk route admin, pengguna hanya bisa mengakses pesanan miliknya, verifikasi signature webhook payment gateway, jangan percaya harga/total dari input klien (hitung ulang di server).
- **Keandalan:** pesanan bersamaan tidak boleh melampaui kuota (gunakan transaksi database/locking saat mengecek dan mengurangi kuota). Webhook harus idempotent.
- **Kegunaan:** responsif (mobile-friendly), pesan error berbahasa Indonesia yang jelas.
- **Kinerja:** halaman katalog dan riwayat memakai pagination/eager loading.

## 10. Tech Stack

> Berikut default yang diasumsikan dari lingkungan developer. Ubah jika berbeda.

- Backend: **Laravel** (PHP), MySQL, Composer
- Frontend: Blade + **Tailwind CSS**, Alpine.js (opsional Flowbite, SweetAlert2)
- Payment gateway: **Midtrans Sandbox** (wajib sesuai instrumen penilaian, bagian 14)
- PDF e-ticket: `barryvdh/laravel-dompdf`; QR: `simplesoftwareio/simple-qrcode`
- Import/ekspor Excel: `maatwebsite/excel`
- Dev environment: Laragon (Windows), VS Code, Git

## 11. Struktur Aplikasi yang Disarankan (Laravel)

- Model: `User`, `TiketPaket`, `Diskon`, `Pesanan`, `PengajuanRombongan`, `PesertaRombongan`, `Pembayaran`, `Tiket`, `TanggalTutup`
- Controller pengunjung: `LandingController`, `KatalogController`, `PesananController`, `RombonganController`, `PembayaranController`, `TiketController`, `RiwayatController`
- Controller admin (prefix `/admin`): `DashboardController`, `TiketPaketController`, `DiskonController`, `PengajuanController`, `PesananController`, `ValidasiTiketController`, `LaporanController`
- Webhook: `PaymentWebhookController` (di luar middleware auth, diverifikasi signature)
- Service/logic: `HitungHargaService` (harga + diskon), `KuotaService` (cek kuota), `TiketGenerator` (kode unik/QR)
- Form Request: validasi aturan bisnis (jumlah umum <=10, rombongan >10, jumlah biodata = jumlah tiket)

## 12. Di Luar Lingkup (Out of Scope)

- Aplikasi mobile native.
- Refund otomatis dan reschedule tanggal setelah bayar.
- Multi-cabang / multi-taman wisata.
- Role selain admin dan pengunjung.

## 13. Pertanyaan Terbuka (konfirmasi ke klien)

1. Batas waktu pembayaran sebelum pesanan kedaluwarsa (mis. 1 jam atau 24 jam)?
2. Boleh ubah/tambah/kurangi jumlah peserta setelah bayar? (Asumsi saat ini: hanya ganti nama.)
3. Biodata diisi sebelum atau sesudah pembayaran? (Asumsi saat ini: sebelum pengajuan dikirim.)
4. Tipe diskon: persen atau nominal, atau keduanya? Boleh digabung dengan diskon lain?
5. Apakah kuota harian berlaku per tiket/paket atau total taman?
6. ~~Payment gateway~~ sudah ditentukan: Midtrans Sandbox (lihat bagian 14).

## 14. Instrumen Penilaian (Sertifikasi Laravel Web App)

> Proyek ini dinilai memakai instrumen "SMK PESAT - Sertifikasi Laravel Web App" (studi kasus e-ticket). Semua fitur P1 harus memenuhi instrumen ini. **Jika ada konflik antara dokumen ini dan instrumen, instrumen yang menang.**

### 14.1 Cara penilaian

- Ada **13 komponen** (R01-R13). Tiap komponen punya **4 aspek (bullet)**.
- Skor 0-4 = jumlah aspek yang terpenuhi **seluruhnya**. Aspek yang hanya terpenuhi sebagian dianggap belum terpenuhi.
- Nilai komponen = skor x 25. Nilai berbobot = nilai komponen x bobot. **Nilai akhir = jumlah nilai berbobot.**
- Predikat: **0-74 Tidak Kompeten**, **75-87 Kompeten**, **88-100 Sangat Kompeten**.
- Penilaian berdasarkan bukti: repository, aplikasi berjalan, database, kode, laporan, dan demonstrasi langsung.

### 14.2 Komponen dan bobot

| Kode | Komponen | Bobot |
|---|---|---|
| R01 | Pra Development | 15% |
| R02 | Proyek, GitHub, dan MVC | 5% |
| R03 | Blade dan antarmuka | 5% |
| R04 | Autentikasi dan otorisasi | 10% |
| R05 | CRUD produk dan katalog | 8% |
| R06 | Pemesanan tiket umum | 10% |
| R07 | Pengajuan paket sekolah | 10% |
| R08 | Integrasi pembayaran Sandbox | 15% |
| R09 | E-ticket dan riwayat | 5% |
| R10 | Validasi dan konfigurasi | 5% |
| R11 | Optimasi N+1 Query | 4% |
| R12 | Pengujian dan pengumpulan | 4% |
| R13 | Demonstrasi dan pemahaman kode | 4% |
| | **Total** | **100%** |

Bobot terbesar: **Pra Development (15%)** dan **Integrasi pembayaran Sandbox (15%)**.

### 14.3 Aspek yang dinilai per komponen

**R01 - Pra Development (15%)**
1. Dokumen kebutuhan memuat masalah client dan kebutuhan tiket umum serta paket sekolah.
2. Daftar fitur membedakan hak admin dan pelanggan, termasuk kepemilikan pesanan (matriks hak akses).
3. **User flow memuat dua alur:** tiket umum sampai e-ticket, dan paket sekolah melalui persetujuan admin sampai e-ticket.
4. ERD memuat entitas pengguna, produk/paket, pesanan, pembayaran, dan tiket (atau setara); **PK, FK, dan kardinalitas** ditunjukkan.

**R02 - Proyek, GitHub, dan MVC (5%)**
1. Repository GitHub dapat diakses penguji, berisi source Laravel dan **riwayat commit** pengerjaan.
2. Proyek berjalan lokal mengikuti README tanpa mengubah source code.
3. Migration dan seeder berhasil dan menghasilkan **akun uji dan data produk**.
4. Pada alur pemesanan: route -> controller -> model/relasi -> Blade.

**R03 - Blade dan antarmuka (5%)**
1. Halaman katalog, detail produk, formulir pemesanan, riwayat, dan admin memakai Blade dan terbuka tanpa error.
2. Layout konsisten (navigasi, font, warna utama).
3. Navigasi pelanggan dan admin mengarah ke halaman yang sesuai; tidak ada tautan fitur wajib yang buntu.
4. Formulir punya label; total biaya, status pesanan, dan pesan hasil tindakan ditampilkan dengan teks jelas.

**R04 - Autentikasi dan otorisasi (10%)**
1. Registrasi membuat akun pelanggan; bisa login dan logout.
2. Pengguna belum login ditolak saat membuka halaman pemesanan, riwayat, atau admin lewat URL.
3. Pelanggan ditolak membuka URL admin atau mengirim tindakan pengelolaan produk/persetujuan; **pembatasan di server**.
4. **Pelanggan B ditolak** membuka detail pesanan atau e-ticket pelanggan A dengan mengganti ID di URL.

**R05 - CRUD produk dan katalog (8%)**
1. Admin menambah tiket umum dan paket sekolah; tersimpan di database.
2. Admin mengubah produk; perubahan tampil saat dibuka kembali.
3. Admin menghapus produk yang **belum** dipakai pesanan, atau **menonaktifkan** produk yang sudah dipakai; riwayat pesanan tetap bisa dibuka.
4. Pelanggan melihat katalog dan detail produk: **nama, jenis, deskripsi, harga, dan ketentuan paket**.

**R06 - Pemesanan tiket umum (10%)**
1. Pelanggan memilih tiket, tanggal kunjungan, dan jumlah orang; pesanan tersimpan.
2. **Total dihitung di server** dari harga tersimpan x jumlah orang; mengubah nilai total lewat request tidak berpengaruh.
3. Ringkasan sebelum pembayaran memuat produk, tanggal, jumlah, harga, dan total yang sama dengan detail pesanan.
4. Pesanan punya kode unik dan terhubung ke pemesan; **total pesanan lama tetap** setelah harga produk diubah (simpan snapshot harga).

**R07 - Pengajuan paket sekolah (10%)**
1. Pengajuan menyimpan nama sekolah, penanggung jawab, kontak, tanggal, paket, dan jumlah peserta.
2. Pengajuan baru berstatus **menunggu persetujuan** dan **tidak bisa memulai pembayaran**, termasuk lewat request langsung.
3. Persetujuan admin mengubah status dan membuka pembayaran untuk pelanggan pemiliknya.
4. Penolakan admin menyimpan alasan yang tampil bagi pelanggan; pesanan yang ditolak tidak bisa dibayar.

**R08 - Integrasi pembayaran Sandbox (15%)**
1. Pesanan yang boleh dibayar membuka transaksi **Midtrans Sandbox** dengan identitas transaksi unik dan nominal sesuai total di server.
2. Status diperiksa di server lewat **API status atau webhook yang diverifikasi**; kode mencocokkan identitas transaksi dan nominal dengan pesanan.
3. Pembayaran berhasil yang terverifikasi mengubah status di database menjadi **lunas**; **callback sukses di browser saja tidak boleh menetapkan lunas**.
4. Status pending/gagal/kedaluwarsa tidak menghasilkan tiket; **pemeriksaan berulang tidak menggandakan tiket** atau menurunkan status lunas.

**R09 - E-ticket dan riwayat (5%)**
1. E-ticket hanya tersedia setelah pembayaran terverifikasi di server.
2. E-ticket menampilkan kode unik, identitas pemesan/sekolah, produk, tanggal kunjungan, dan jumlah orang.
3. Riwayat pelanggan menampilkan pesanan miliknya beserta status pesanan dan pembayaran dari database.
4. Detail pesanan bisa dibuka dari riwayat dan menampilkan rincian biaya serta tautan e-ticket jika sudah lunas.

**R10 - Validasi dan konfigurasi (5%)**
1. Field wajib kosong ditolak di server dan pesan error tampil di formulir.
2. Jumlah orang nol/negatif/bukan bilangan bulat ditolak; jumlah **di bawah minimum paket** juga ditolak.
3. Tanggal lampau dan produk nonaktif ditolak saat pemesanan, termasuk lewat request langsung.
4. Konfigurasi Midtrans lewat environment; **repository tidak memuat `.env` atau Server Key asli**, dan Server Key tidak tampil di Blade/JavaScript.

**R11 - Optimasi N+1 Query (4%)**
1. Ada bukti awal kueri berulang pada halaman daftar yang menampilkan data relasi; relasi penyebabnya bisa ditunjuk.
2. Kode akhir memakai **eager loading** (`with()`).
3. Bukti sebelum/sesudah memakai halaman dan data yang sama, dengan jumlah kueri tercatat.
4. Jumlah kueri sesudah lebih kecil, data yang tampil tetap sama.

**R12 - Pengujian dan pengumpulan (4%)**
1. Laporan pengujian memuat langkah, hasil diharapkan, hasil aktual, dan lulus/gagal untuk alur pemesanan umum dan sekolah.
2. Laporan juga memuat uji hak akses, total biaya, validasi, pembayaran berhasil/belum berhasil, dan cek status berulang, dengan bukti.
3. README memuat prasyarat, instalasi, konfigurasi tanpa kunci asli, akun uji, dan cara memakai aplikasi; **`.env.example` disertakan**.
4. Google Classroom memuat tautan repo yang bisa diakses, dokumen Pra Development, laporan pengujian, bukti Sandbox, dan perbandingan kueri.

**R13 - Demonstrasi dan pemahaman kode (4%)**
1. Demo alur tiket umum: login, pemesanan, pembayaran Sandbox, e-ticket.
2. Demo alur sekolah: pengajuan, keputusan admin, pembayaran, e-ticket, **termasuk cabang penolakan**.
3. Menunjukkan dan menjelaskan kode middleware/otorisasi dan perhitungan total di server.
4. Menjelaskan asal status lunas dari verifikasi server dan cara eager loading mengurangi kueri.

### 14.4 Dampak ke pengerjaan (checklist agent)

- **Dua user flow terpisah** (tiket umum, paket sekolah) wajib ada sebagai bukti R01. Gambar gabungan boleh dibuat tambahan, bukan pengganti.
- ERD harus menampilkan **PK, FK, dan kardinalitas**.
- Buat **matriks hak akses** (admin vs pelanggan, termasuk kepemilikan pesanan).
- Wajib ada **seeder**: akun admin, pelanggan A, pelanggan B (untuk uji IDOR), serta data tiket umum dan paket sekolah.
- Cek kepemilikan di server (policy/gate atau query dibatasi `user_id`) untuk pesanan dan e-ticket.
- **Midtrans Sandbox bersifat wajib** (bukan opsional). Cocokkan `order_id` dan `gross_amount` dengan pesanan, verifikasi signature, dan buat proses idempotent.
- Produk yang sudah dipakai pesanan **dinonaktifkan**, bukan dihapus.
- Siapkan bukti N+1 (sebelum/sesudah) di halaman daftar, misalnya riwayat pesanan atau daftar pesanan admin, dan catat jumlah kuerinya.
- Siapkan README, `.env.example`, laporan pengujian, bukti Sandbox, dan commit yang rutin sejak awal.
- Siapkan diri menjelaskan: middleware, hitung total di server, asal status lunas, dan eager loading.

## 15. Petunjuk untuk AI Agent

- Baca bagian **5 (Aturan Bisnis)**, **8 (Model Data)**, dan **14 (Instrumen Penilaian)** sebelum menulis kode.
- Kerjakan berurutan sesuai sprint di `SDLC.md`. Jangan mengerjakan fitur P3 sebelum P1 selesai.
- Jangan menambah role, tabel, atau fitur di luar dokumen ini tanpa konfirmasi.
- Pastikan setiap fitur memenuhi aspek penilaian di bagian 14 (terutama otorisasi, total di server, dan verifikasi pembayaran).
- Setiap fitur: migration, model, validasi server, tampilan Blade (bahasa Indonesia), dan minimal satu feature test untuk aturan bisnisnya.
- Jika ada ambiguitas, catat sebagai pertanyaan (lihat bagian 13) dan pakai asumsi yang tertulis, jangan menebak diam-diam.
