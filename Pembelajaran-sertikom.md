# Pembelajaran Sistem Website The Nice Park

Dokumentasi ini berisi ringkasan lengkap struktur sistem, alur bisnis, dan komponen utama website **The Nice Park** (sistem pemesanan tiket & paket wisata). Ditulis untuk referensi studi dan penjelasan ke depan.

---

## 1. Ringkasan Proyek

- **Nama:** The Nice Park
- **Tipe:** Laravel 13, PHP 8.5
- **Database:** MySQL (default), bisa SQLite
- **Payment Gateway:** Midtrans (Snap API)
- **PDF:** barryvdh/laravel-dompdf
- **Excel:** maatwebsite/excel
- **Frontend:** Tailwind CSS, Vite, Alpine.js, SweetAlert2
- **Testing:** Pest
- **Formatter:** Laravel Pint

Fitur utama:
- Halaman publik (landing) untuk lihat destinasi dan katalog tiket
- Pemesanan tiket umum (1-10 orang, tanpa persetujuan admin)
- Pengajuan paket rombongan (11+ orang, perlu persetujuan admin)
- Pembayaran melalui Midtrans
- E-tiket dengan QR code dan validasi tiket oleh admin
- Dashboard admin & dashboard pengunjung
- Export laporan Excel & PDF

---

## 2. Struktur Direktori Penting

```
app/
  Http/Controllers/        # Semua controller
  Http/Controllers/Admin/  # Controller admin
  Http/Controllers/Auth/   # Controller autentikasi
  Http/Controllers/User/   # Controller user
  Http/Middleware/         # Middleware custom
  Http/Requests/           # Form Request (validasi)
  Models/                  # Eloquent models
  Policies/                # Authorization policies
  Providers/               # Service providers
  Services/                # Business logic services
  Exports/                 # Excel export
  Imports/                 # Excel import
config/                    # Konfigurasi Laravel
database/
  migrations/              # Tabel database
  seeders/                 # Data awal (seeder)
  factories/               # Model factories
resources/
  views/                   # Blade templates
  views/layouts/           # Layout master
  views/components/        # Blade components
  css/app.css              # CSS kustom + Tailwind
routes/
  web.php                  # Entry route (include semua route file)
  public.php               # Route publik (landing, katalog, payment demo)
  auth.php                 # Route auth (login, register, password, profile)
  user.php                 # Route user (pengunjung)
  admin.php                # Route admin
  webhook.php              # Webhook Midtrans
tests/                     # Test Pest
```

---

## 3. Database Schema (Migrasi)

Tabel utama dan relasinya:

### users
- `id`, `nama` (digantikan dari `name`), `email`, `password`, `role` (admin/pengunjung), `no_hp`, `email_verified_at`, `remember_token`, timestamps

### tiket_paket
- `id`, `nama`, `kategori` (umum/rombongan), `deskripsi`, `fasilitas`, `harga`, `kuota_harian`, `status_aktif`, timestamps

### diskon
- `id`, `admin_id`, `nama`, `kategori`, `tipe` (persen/nominal), `nilai`, `tanggal_mulai`, `tanggal_selesai`, `status_aktif`, timestamps

### pesanan
- `id`, `kode_pesanan` (unik), `user_id`, `tiket_paket_id`, `diskon_id`, `kategori`, `tanggal_kunjungan`, `jumlah_tiket`, `harga_satuan`, `potongan_diskon`, `total_bayar`, `status`, timestamps

### pembayaran
- `id`, `pesanan_id`, `order_id_gateway` (unik), `metode`, `jumlah`, `status` (pending/berhasil/gagal/kedaluwarsa), `dibayar_pada`, timestamps

### tiket
- `id`, `pesanan_id`, `peserta_id`, `divalidasi_oleh`, `kode_tiket` (unik), `jenis` (individu/rombongan), `status` (aktif/dipakai), `dipakai_pada`, timestamps

### tanggal_tutup
- `id`, `tanggal` (unik), `keterangan`, timestamps

### pengajuan_rombongan
- `id`, `pesanan_id` (unik), `admin_id`, `nama_sekolah`, `penanggung_jawab`, `no_hp`, `mode_tiket` (per_peserta/per_rombongan), `status` (diajukan/disetujui/ditolak), `catatan_admin`, `ditinjau_pada`, timestamps

### peserta_rombongan
- `id`, `pesanan_id`, `nama`, `kelas`, timestamps

---

## 4. Models & Hubungan (Eloquent)

### User (`app/Models/User.php`)
- Konstanta: `ROLE_ADMIN = 'admin'`, `ROLE_PENGUNJUNG = 'pengunjung'`
- Method: `isAdmin()` cek role === admin
- Relasi: `diskonDibuat()` (HasMany Diskon), `pesanan()` (HasMany Pesanan)
- Fillable: `nama`, `email`, `password`, `no_hp`
- Default role: `pengunjung`

### TiketPaket (`app/Models/TiketPaket.php`)
- Konstanta: `KATEGORI_UMUM = 'umum'`, `KATEGORI_ROMBONGAN = 'rombongan'`, `KATEGORI = [...]`
- Scope: `aktif()`, `kategori($kategori)`
- Relasi: `pesanan()` (HasMany)

### Pesanan (`app/Models/Pesanan.php`)
- Konstanta status: `MENUNGGU_PERSETUJUAN`, `DISETUJUI`, `MENUNGGU_PEMBAYARAN`, `LUNAS`, `DITOLAK`, `KEDALUWARSA`, `BATAS_PESANAN_UMUM_PER_AKUN = 5`
- Scope: `lunas()`, `kategori($kategori)`
- Relasi: `user()`, `tiketPaket()`, `diskon()`, `pembayaran()`, `tiket()`, `pengajuanRombongan()` (HasOne), `pesertaRombongan()`

### Pembayaran (`app/Models/Pembayaran.php`)
- Konstanta status: `PENDING`, `BERHASIL`, `GAGAL`, `KEDALUWARSA`
- Relasi: `pesanan()`

### Tiket (`app/Models/Tiket.php`)
- Konstanta: `JENIS_INDIVIDU`, `JENIS_ROMBONGAN`, `STATUS_AKTIF`, `STATUS_DIPAKAI`
- Relasi: `pesanan()`, `peserta()`, `divalidasiOleh()`

### Diskon (`app/Models/Diskon.php`)
- Konstanta: `TIPE_PERSEN`, `TIPE_NOMINAL`, `TIPE`
- Scope: `aktif()`, `kategori()`, `berlakuPada($tanggal)` (aktif + dalam range tanggal)

### PengajuanRombongan (`app/Models/PengajuanRombongan.php`)
- Konstanta: `STATUS_DIAJUKAN`, `STATUS_DISETUJUI`, `STATUS_DITOLAK`, `MODE_PER_PESERTA`, `MODE_PER_ROMBONGAN`
- Relasi: `pesanan()`, `admin()`

### PesertaRombongan (`app/Models/PesertaRombongan.php`)
- Relasi: `pesanan()`, `tiket()` (HasOne)

### TanggalTutup (`app/Models/TanggalTutup.php`)
- Tabel untuk tanggal yang tutup (tidak bisa dipesan)

---

## 5. Routes (Semua Grup)

Route di-load melalui `routes/web.php` yang include: `public.php`, `auth.php`, `user.php`, `webhook.php`, `admin.php`.

### Public (Tanpa auth)
| Route | Method | Nama | Aksi |
|-------|--------|------|------|
| `/` | GET | `landing` | LandingController (`__invoke`) |
| `/katalog/{tiketPaket}` | GET | `katalog.show` | KatalogController@show |
| `/payment` | GET/POST | `demo-payment.index/pay` | PaymentController |
| `/payment/check` | POST | `demo-payment.check` | PaymentController@check |

### Auth
| Route | Method | Nama | Catatan |
|-------|--------|------|---------|
| `/register` | GET/POST | `register` | Hanya guest |
| `/login` | GET/POST | `login` | Hanya guest, rate-limited |
| `/forgot-password` | GET/POST | `password.request/email` | Lupa password |
| `/reset-password/{token}` | GET/POST | `password.reset/store` | Reset password |
| `/verify-email` | GET | `verification.notice` | Butuh auth |
| `/verify-email/{id}/{hash}` | GET | `verification.verify` | Signed URL |
| `/email/verification-notification` | POST | `verification.send` | Kirim ulang verifikasi |
| `/confirm-password` | GET/POST | `password.confirm` | Konfirmasi password |
| `/password` | PUT | `password.update` | Ganti password |
| `/logout` | POST | `logout` | Keluar |
| `/dashboard` | GET | `dashboard` | Redirect ke admin atau user dashboard |
| `/profile` | GET/PATCH/DELETE | `profile.edit/update/destroy` | Manajemen profil |

### User (Middleware: `auth`, `pengunjung`)
| Route | Method | Nama | Controller |
|-------|--------|------|------------|
| `/akun` | GET | `user.dashboard` | User\DashboardController |
| `/akun/pesan` | GET | `user.booking` | User\DashboardController@booking |
| `/akun/pesanan` | GET | `user.orders` | RiwayatController@orders |
| `/pesan` | GET | `pesan.kategori` | PesananController@kategori |
| `/pesan/rombongan` | GET | `pesan.rombongan.create` | RombonganController@create |
| `/pesan/rombongan/periksa` | POST | `pesan.rombongan.periksa` | RombonganController@periksa |
| `/pesan/rombongan/peserta` | GET/POST | `pesan.rombongan.peserta/simpan-peserta` | RombonganController |
| `/pesan/rombongan/template` | GET | `pesan.rombongan.template` | Download CSV template |
| `/pesan/{tiketPaket}` | GET/POST | `pesan.create/store` | PesananController |
| `/pembayaran/{pesanan}` | GET/POST | `pembayaran.show/process` | PembayaranController |
| `/pembayaran/{pesanan}/selesai` | GET | `pembayaran.selesai` | PembayaranController@selesai |
| `/riwayat` | GET | `riwayat.index` | RiwayatController@index |
| `/riwayat/{pesanan}` | GET | `riwayat.show` | RiwayatController@show |
| `/riwayat/{pesanan}/peserta/{peserta}` | POST | `pesan.rombongan.update-peserta` | Update biodata peserta |
| `/tiket/{tiket}` | GET | `tiket.show` | TiketController@show |
| `/tiket/{tiket}/pdf` | GET | `tiket.pdf` | TiketController@pdf |
| `/riwayat/{pesanan}/pdf-rombongan` | GET | `pesanan.pdf-rombongan` | TiketController@pdfRombongan |

### Admin (Middleware: `auth`, `admin`, prefix: `admin`)
| Route | Method | Nama | Controller |
|-------|--------|------|------------|
| `/` | GET | `admin.dashboard` | DashboardController |
| `/pengajuan` | GET | `admin.pengajuan.index` | PengajuanRombonganController@index |
| `/pengajuan/{pesanan}/setujui` | POST | `admin.pengajuan.approve` | Approve pengajuan |
| `/pengajuan/{pesanan}/tolak` | POST | `admin.pengajuan.reject` | Tolak pengajuan |
| `/validasi-tiket` | GET/POST | `admin.tiket.validasi/.store` | ValidasiTiketController |
| `/tiket-paket` | resource | `admin.tiket-paket.*` | TiketPaketController (kecuali show) |
| `/diskon` | resource | `admin.diskon.*` | DiskonController (kecuali show) |
| `/pesanan` | GET | `admin.pesanan.index/show` | PesananController |
| `/laporan/excel` | GET | `admin.laporan.excel` | LaporanController@exportExcel |
| `/laporan/pdf` | GET | `admin.laporan.pdf` | LaporanController@exportPdf |

### Webhook
| Route | Method | Nama | Controller |
|-------|--------|------|------------|
| `/webhook/midtrans` | POST | `payment.webhook` | PaymentWebhookController@handle |

---

## 6. Controllers

### Publik
- **LandingController** (`__invoke`): Ambil tiket paket aktif + diskon berlaku hari ini, group by kategori, kirim ke view `landing`
- **KatalogController** (`show`): Detail satu tiket paket, hitung diskon terbaik via `HitungHargaService`
- **PaymentController**: Demo payment Midtrans (index, pay, check)

### Pembayaran & Pesanan
- **PembayaranController**: `show` (cek status), `process` (redirect ke Midtrans), `selesai` (sinkronkan status dari gateway)
- **PesananController**: `kategori` (redirect), `create` (redirect ke booking), `store` (buat pesanan umum, transaksi DB, validasi kuota & batas 5 pesanan/akun)
- **RombonganController**: `create`, `periksa` (validasi kuota, simpan draft ke session), `peserta`, `simpanPeserta` (buat pesanan + pengajuan + peserta, transaksi DB), `template` (download CSV), `updatePeserta` (edit biodata peserta)
- **TiketController**: `show` (redirect ke riwayat), `pdf` (unduh PDF tiket individu), `pdfRombongan` (unduh PDF tiket rombongan induk)

### User Dashboard
- **User\DashboardController** (`__invoke`): Ringkasan akun (total pesanan, menunggu, tiket terbit)
- **User\DashboardController** (`booking`): Halaman pilih tiket (paket umum + rombongan, draft rombongan)
- **RiwayatController**: `index` (redirect ke orders), `orders` (daftar pesanan user), `show` (detail pesanan)

### Admin
- **Admin\DashboardController**: Statistik (total tiket, aktif, diskon, pesanan, pendapatan bulan ini, tiket terbit, pengajuan menunggu, grafik pendapatan 6 bulan, pesanan terbaru)
- **Admin\TiketPaketController**: CRUD tiket paket
- **Admin\DiskonController**: CRUD diskon
- **Admin\PengajuanRombonganController**: Daftar pengajuan menunggu, approve/setujui, tolak (dengan catatan admin)
- **Admin\PesananController**: Daftar pesanan dengan filter, detail pesanan
- **Admin\ValidasiTiketController**: Form input kode tiket, validasi (cek lunas, tanggal hari ini, belum dipakai), tandai dipakai
- **Admin\LaporanController**: Export Excel dan PDF laporan pesanan

### Auth & Profile
- **ProfileController**: edit, update (nama, email, no_hp), delete (akun)
- **Auth\***: Standard Laravel Breeze auth controllers (login, register, password reset, email verification)

---

## 7. Services (Logic Bisnis)

### HitungHargaService (`app/Services/HitungHargaService.php`)
- `hitung($tiketPaket, $jumlahTiket, $diskon)`: Hitung harga satuan, potongan diskon, total bayar. Potongan dibatasi maksimal subtotal.
- `diskonTerbaik($tiketPaket, $jumlahTiket, $tanggal)`: Pilih diskon yang aktif, sesuai kategori, berlaku pada tanggal, yang memberikan potongan terbesar.

### KuotaService (`app/Services/KuotaService.php`)
- `tanggalTutup($tanggal)`: Cek apakah tanggal tutup
- `terpakai($tiketPaket, $tanggal, $userId)`: Jumlah tiket yang sudah lunas pada tanggal tersebut
- `sisa(...)`: Kuota harian dikurangi terpakai
- `cukup(...)`: Cek apakah kuota cukup untuk jumlah tiket yang diminta

### PembayaranService (`app/Services/PembayaranService.php`)
- `pastikanPembayaran($pesanan)`: Buat atau ambil record pembayaran (order_id_gateway = kode_pesanan)
- `redirectUrl($pesanan)`: Buat transaksi Midtrans Snap, return redirect URL
- `sinkronkanDariGateway($pesanan)`: Cek status transaksi ke Midtrans
- `proses($pembayaran, $payload)`: Proses webhook payload, update status pembayaran & pesanan, generate tiket jika berhasil
- `konfigurasi()`: Set config Midtrans (server key, production, sanitize, 3DS)
- `petakanStatus($transactionStatus, $fraudStatus)`: Map status Midtrans ke status lokal

### TiketGenerator (`app/Services/TiketGenerator.php`)
- `kodePesanan()`: Generate `TNP-YYYYMMDD-XXXXXX`
- `kodeTiket()`: Generate `TKT-XXXXXXXXXX`
- `buatUntukPesanan($pesanan)`: Buat tiket setelah pembayaran lunas. Untuk rombongan mode `per_rombongan` = 1 tiket, `per_peserta` = satu tiket per peserta. Untuk umum = jumlah_tiket tiket.

---

## 8. Middleware

### EnsureUserIsAdmin (`app/Http/Middleware/EnsureUserIsAdmin.php`)
- Abort 403 jika user bukan admin (`isAdmin()`)

### EnsureUserIsPengunjung (`app/Http/Middleware/EnsureUserIsPengunjung.php`)
- Abort 403 jika role user bukan `pengunjung`

Middleware terdaftar di `bootstrap/app.php` atau `app/Http/Kernel.php` (Laravel 13 biasanya di bootstrap). Nama middleware: `admin` dan `pengunjung`.

---

## 9. Form Request (Validasi)

### StorePesananRequest
- `tanggal_kunjungan`: required, date, after_or_equal:today
- `jumlah_tiket`: required, integer, min:1

### StorePengajuanRombonganRequest
- `tiket_paket_id`: required, integer, exists
- `tanggal_kunjungan`: required, date, after_or_equal:today
- `jumlah_tiket`: required, integer, min:11
- `nama_sekolah`, `penanggung_jawab`: required, string, max:255
- `no_hp`: required, string, max:20
- `mode_tiket`: required, in [per_peserta, per_rombongan]

### StorePesertaRombonganRequest
- `sumber_data`: required, in [manual, file]
- `peserta` (array): required jika manual
- `file_peserta`: required, file, mimes csv/txt/xlsx/xls, max 2MB jika file
- `after()`: validasi jumlah peserta manual sama dengan jumlah tiket draft

### Admin Requests
- **TiketPaketRequest**: nama, kategori, deskripsi, fasilitas, harga, kuota_harian, status_aktif
- **DiskonRequest**: nama, kategori, tipe, nilai (max 100 jika persen, max 100jt jika nominal), tanggal_mulai, tanggal_selesai, status_aktif
- **ValidasiTiketRequest**: kode_tiket required
- **RejectPengajuanRombonganRequest**: catatan_admin required, max 2000

### Auth
- **LoginRequest**: email, password, rate limiting (max 3 attempt, lockout 300 detik)

### Profile
- **ProfileUpdateRequest**: nama required, email unique (ignore self), no_hp nullable

---

## 10. Policy

### PesananPolicy (`app/Policies/PesananPolicy.php`)
- `view(User $user, Pesanan $pesanan)`: hanya boleh melihat pesanan milik sendiri (`pesanan->user_id === $user->id`)

Terdaftar di `AuthServiceProvider`: `Pesanan::class => PesananPolicy::class`

---

## 11. Providers

### AppServiceProvider
- `View::composer('layouts.admin-dashboard')`: inject `pengajuanMenunggu` count ke layout admin

### AuthServiceProvider
- Register policy `PesananPolicy`

---

## 12. Alur Bisnis Utama

### A. Pemesanan Tiket Umum (Pengunjung)
1. User buka landing → pilih kategori "Tiket Umum"
2. Pilih paket → form tanggal + jumlah (maks 10)
3. Sistem validasi: kuota harian, tanggal tutup, batas 5 pesanan/akun
4. Diskon otomatis diterapkan (diskon terbaik untuk kategori umum pada tanggal kunjungan)
5. Buat pesanan status `menunggu_pembayaran`
6. Redirect ke halaman pembayaran → tombol "Lanjut ke pembayaran aman" → Midtrans Snap
7. Midtrans webhook → update pembayaran ke `berhasil` → pesanan menjadi `lunas` → generate tiket

### B. Pengajuan Paket Rombongan (Pengunjung)
1. User pilih paket rombongan → form tanggal, jumlah (min 11), data sekolah, penanggung jawab, no_hp, mode tiket
2. Sistem cek kuota → simpan draft ke session
3. Isi biodata peserta (manual atau impor Excel/CSV)
4. Buat pesanan status `menunggu_persetujuan` + pengajuan rombongan status `diajukan` + peserta
5. Admin meninjau di dashboard admin → setujui atau tolak
6. Jika disetujui: pesanan jadi `menunggu_pembayaran`, pembayaran dibuka
7. Jika ditolak: pesanan jadi `ditolak`, catatan admin disimpan

### C. Validasi Tiket (Admin di pintu masuk)
1. Admin masuk halaman validasi tiket
2. Masukkan kode tiket (atau scan QR)
3. Sistem cek: tiket ditemukan, pesanan lunas, tanggal kunjungan hari ini, status belum dipakai
4. Jika valid: tandai tiket `dipakai`, catat `divalidasi_oleh` dan `dipakai_pada`

### D. Pembayaran (Midtrans)
- Order ID = `kode_pesanan` (contoh: `TNP-20261124-ABCDEF`)
- Webhook di `/webhook/middivtrans` memverifikasi signature key (SHA512 dari order_id + status_code + gross_amount + server_key)
- Status berhasil → pesanan lunas → generate tiket

---

## 13. Tampilan & Layout (Blade Views)

### Layouts
- **`layouts/public.blade.php`**: Halaman publik (landing, katalog, pesan, pembayaran). Header sticky dengan navigasi, badge "Aktif" di sebelah nama user kalau login sebagai pengunjung, footer dengan kontak dan media sosial.
- **`layouts/user-dashboard.blade.php`**: Portal pengunjung (akun, booking, pesanan). Topbar dengan navigasi Ringkasan/Pilih tiket/Pesanan saya.
- **`layouts/admin-dashboard.blade.php`**: Admin panel. Sidebar navigasi (Ringkasan, Pengajuan, Pesanan, Tiket & paket, Diskon, Validasi tiket), topbar, metrics cards, grafik pendapatan.
- **`layouts/app.blade.php`**: Layout bawaaan Laravel (digunakan oleh view riwayat/tiket yang pakai `<x-app-layout>`)
- **`layouts/guest.blade.php`**: Layout halaman auth (login/register)
- **`layouts/navigation.blade.php`**: Navbar komponen (digunakan app.blade.php)

### Views Utama
- `landing.blade.php` — Hero carousel destinasi, tentang, ketentuan tiket, katalog, cara memesan, fasilitas, ketentuan kunjungan, FAQ, CTA rombongan
- `user/dashboard.blade.php` — Ringkasan akun, daftar pesanan dengan detail (expandable), halaman booking (pilih paket umum/rombongan)
- `user/orders/show.blade.php` — Detail pesanan lengkap (pembayaran, e-tiket, peserta rombongan)
- `admin/dashboard.blade.php` — Dashboard admin dengan metrics, grafik, pesanan terbaru, quick links
- `admin/pengajuan/index.blade.php` — Daftar pengajuan rombongan menunggu
- `admin/pesanan/index.blade.php` — Daftar semua pesanan dengan filter
- `admin/pesanan/show.blade.php` — Detail pesanan + tombol setujui/tolak pengajuan
- `admin/tiket-paket/{index,create,edit}.blade.php` — CRUD paket tiket
- `admin/diskon/{index,create,edit}.blade.php` — CRUD diskon
- `admin/tiket/validasi.blade.php` — Form validasi kode tiket
- `admin/laporan/pdf.blade.php` — Template PDF laporan
- `pesan/kategori.blade.php` — Pilih kategori umum vs rombongan
- `pesan/create.blade.php` — Form pesan tiket umum (dengan hitungan total dinamis via Alpine.js)
- `pesan/rombongan/create.blade.php` & `peserta.blade.php` — Form pengajuan rombongan dan biodata peserta
- `pembayaran/show.blade.php` — Halaman pembayaran (tombol lanjut ke Midtrans)
- `tiket/{show,pdf,pdf-rombongan}.blade.php` — Tampilan e-tiket dan unduhan PDF
- `demo-payment/{index,pas,check}.blade.php` — Demo payment Midtrans (kosong)
- `auth/{login,register,forgot-password,reset-password,verify-email,confirm-password}.blade.php` — Halaman auth
- `profile/{edit,edit-admin}.blade.php` — Form edit profil

### Components (`resources/views/components/`)
- `status-badge.blade.php` — Badge status dengan warna menyesuaikan status (menunggu_persetujuan, disetujui, lunas, ditolak, berhasil, aktif, dll)
- `primary-button.blade.php`, `secondary-button.blade.php`, `danger-button.blade.php` — Tombol standar
- `text-input.blade.php`, `input-label.blade.php`, `input-error.blade.php` — Komponen form
- `dropdown.blade.php`, `dropdown-link.blade.php` — Dropdown menu
- `modal.blade.php` — Modal dialog
- `nav-link.blade.php`, `responsive-nav-link.blade.php` — Link navigasi
- `application-logo.blade.php` — SVG logo
- `auth-session-status.blade.php` — Status sesi auth

---

## 14. Design System (Warna)

Didefinisikan di `tailwind.config.js` dan `resources/css/app.css`:

| Warna | Nilai Hex | Penggunaan |
|-------|-----------|------------|
| juniper-500 | `#2f5d4c` | Warna utama, teks utama, tombol utama |
| juniper-600 | `#1f3d33` | Tombol utama hover, background section |
| juniper-700 | `#173028` | Background gelap, teks kontras |
| juniper-900 | `#0b1814` | Background section gelap, teks judul |
| toffee-300 | `#cf936b` | Aksen oranye, tombol CTA |
| toffee-500 | `#a25f31` | Tombol pembayaran |
| toffee-600 | `#8a4b2a` | Tombol pembayaran hover |
| alabaster-50/100/200 | `#fbf8f2`... | Background krem, teks pudar |
| park-copper | `#b4663b` | Badge "Aktif" di navbar (CSS custom) |
| park-copper-dark | `#7c3f22` | Gelap dari copper |
| park-marigold | `#e8a33c` | Grafik pendapatan |
| park-green | `#1c4335` | Border/header PDF, elemen admin |

Font: **Fraunces** (display/judul), **Plus Jakarta Sans** (body/sans)

---

## 15. Seeder & Data Awal (`DatabaseSeeder.php`)

Saai menjalakan `php artisan db_seed`:

### Users
| Email | Password | Role | Nama |
|-------|----------|------|------|
| admin@thenicepark.test | password | admin | Admin The Nice Park |
| pengunjung@example.com | password | pengunjung | Pengunjung Demo |

### Tiket Paket
| Nama | Kategori | Harga | Kuota/Hari |
|------|----------|-------|------------|
| Tiket Masuk Reguler | umum | Rp 10.000 | 200 |
| Tiket Terusan Keluarga | umum | Rp 25.000 | 100 |
| Paket Edukasi Sekolah | rombongan | Rp 15.000 | 150 |

### Diskon
| Nama | Kategori | Tipe | Nilai | Masa Berlaku |
|------|----------|------|-------|--------------|
| Promo Pengunjung Umum | umum | persen | 10% | 1 minggu lalu s/d 1 bulan lagi |
| Diskon Rombongan Sekolah | rombongan | nominal | Rp 2.000 | 1 minggu lalu s/d 1 bulan lagi |

---

## 16. Konfigurasi Penting

### `.env`
- `DB_CONNECTION=mysql`, host `127.0.0.1`, database `the_nice_park`, user `root`, password kosong
- `APP_DEBUG=true`, `APP_URL=http://localhost:8000`
- `MAIL_MAILER=log` (email hanya ditulis ke log, tidak terkirim nyata)
- `KEY_MIDTRANS_SERVER_KEY=SB-Mid-server-...` (Midtrans sandbox key)
- `SESSION_DRIVER=database`, `QUEUE_CONNECTION=database`, `CACHE_STORE=database`

### `config/destinasi.php`
Data destinasi "The Nice" dari `thenice.co.id` (15 destinasi di seluruh Indonesia, Rumpin sebagai pertama). Digunakan di halaman landing carousel.

### `config/services.php`
Konfigurasi Midtrans: `server_key` dan `is_production`.

---

## 17. Cara Menjalankan / Setup

```bash
# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Migrasi + seeder
php artisan migrate --seed

# Build frontend
npm run build

# Jalankan server
php artisan serve

# Untuk development (server + queue + vite secara bersamaan)
composer run dev
# atau
npx concurrently "php artisan serve" "php artisan queue:listen --tries=1" "npm run dev"
```

### Perintah Berguna
- `php artisan route:list` — Lihat semua route
- `php artisan config:show app.name` — Lihat nilai config
- `php artisan tinker --execute '...'` — Eksekusi PHP dalam konteks aplikasi
- `vendor/bin/pint --dirty --format agent` — Format file PHP yang diubah
- `vendor/bin/pest` — Jalankan semua test
- `php artisan view:clear` / `php artisan config:clear` / `php artisan route:clear` — Bersihkan cache

---

## 18. Catatan Penting untuk Pengembangan

1. **Kolom `nama` bukan `name`** — Tabel users menggunakan kolom `nama` (migrasi `rename_name_to_nama_on_users_table`). Selalu gunakan `$user->nama`, bukan `$user->name`.
2. **Role default `pengunjung`** — User baru secara default adalah pengunjung, bukan admin.
3. **Batas pesanan umum** — Maksimal 5 pesanan tiket umum per akun (`BATAS_PESANAN_UMUM_PER_AKUN = 5`). Di atas 10 tiket harus pakai rombongan.
4. **Diskon otomatis** — Sistem memilih diskon terbaik (potongan terbesar) yang aktif dan berlaku pada tanggal kunjungan.
5. **Tiket hanya dibuat setelah lunas** — `TiketGenerator::buatUntukPesanan()` dipanggil saat status pembayaran menjadi `berhasil`.
6. **Validasi tiket hanya berlaku hari ini** — Admin tidak bisa memvalidasi tiket untuk tanggal lain kecuali hari ini.
7. **Pint tidak boleh dilewati** — Setelah mengubah file PHP, jalankan `vendor/bin/pint --dirty --format agent`.
8. **Frontend cache** — Jika perubahan CSS/Tailwind tidak tampak, jalankan `npm run build` atau `php artisan view:clear`.

---

## 19. Alur Kode dari URL hingga Tampilan (Contoh: Landing Page)

1. User mengakses `/`
2. `routes/public.php`: `Route::get('/', LandingController::class)->name('landing')`
3. `LandingController::__invoke()` ambil data dari `TiketPaket`, `Diskon`, dan `config('destinasi')`
4. View: `resources/views/landing.blade.php` extends `layouts/public.blade.php`
5. Layout render header, content (section-section landing), dan footer
6. Tailwind CSS di-compile oleh Vite dari `resources/css/app.css`

---

Dokumentasi ini akan diperbarui seiring dengan perubahan sistem. Untuk detail implementasi tertentu, lihat langsung file sumber di direktori yang disebutkan.