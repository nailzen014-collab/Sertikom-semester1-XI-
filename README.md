# The Nice Park — Tiket & Paket Wisata

Sistem manajemen tiket dan paket wisata untuk "The Nice Park Rumpin" (playground & mini zoo). Aplikasi ini dibangun dengan Laravel dan mencakup pemesanan tiket, pengajuan paket rombongan (sekolah), pembayaran melalui Midtrans, validasi e-tiket oleh admin, serta laporan pesanan.

## Teknologi

- **Backend**: Laravel (PHP 8.3+)
- **Database**: MySQL / SQLite
- **Pembayaran**: Midtrans (Snap API) dengan webhook verifikasi signature
- **Frontend**: Blade, Tailwind CSS, Vite, Alpine.js
- **PDF**: barryvdh/laravel-dompdf, simplesoftwareio/simple-qrcode
- **Testing**: Pest PHP

## Struktur Rute

### Publik (tanpa auth)
| Method | URI | Nama | Keterangan |
|--------|-----|------|------------|
| GET | `/` | `landing` | Halaman utama (hero, katalog, ketentuan, FAQ) |
| GET | `/katalog/{tiketPaket}` | `katalog.show` | Detail tiket/paket |
| GET | `/payment` | `demo-payment.index` | Demo pembayaran |
| POST | `/payment` | `demo-payment.pay` | Proses demo pembayaran |
| POST | `/payment/check` | `demo-payment.check` | Cek status demo |

### Autentikasi (guest / auth)
| Method | URI | Nama | Keterangan |
|--------|-----|------|------------|
| GET/POST | `/register` | `register` | Daftar akun |
| GET/POST | `/login` | `login` | Masuk |
| GET/POST | `/forgot-password` | `password.request`, `password.email` | Lupa kata sandi |
| GET/POST | `/reset-password/{token}` | `password.reset`, `password.store` | Reset kata sandi |
| GET | `/verify-email` | `verification.notice` | Notifikasi verifikasi email |
| GET | `/verify-email/{id}/{hash}` | `verification.verify` | Verifikasi email (signed) |
| POST | `/email/verification-notification` | `verification.send` | Kirim ulang verifikasi |
| GET/POST | `/confirm-password` | `password.confirm`, `password.store` | Konfirmasi kata sandi |
| PUT | `/password` | `password.update` | Ganti kata sandi |
| POST | `/logout` | `logout` | Keluar |
| GET | `/dashboard` | `dashboard` | Redirect ke admin atau user dashboard |

### Pengunjung (`auth`, `pengunjung`)
| Method | URI | Nama | Keterangan |
|--------|-----|------|------------|
| GET | `/akun` | `user.dashboard` | Dashboard pengunjung |
| GET | `/akun/pesan` | `user.booking` | Pesan tiket |
| GET | `/akun/pesanan` | `user.orders` | Riwayat pesanan |
| GET | `/pesan` | `pesan.kategori` | Pilih kategori pesanan |
| GET | `/pesan/rombongan` | `pesan.rombongan.create` | Ajukan paket rombongan |
| POST | `/pesan/rombongan/periksa` | `pesan.rombongan.periksa` | Periksa pengajuan rombongan |
| GET/POST | `/pesan/rombongan/peserta` | `pesan.rombongan.peserta`, `pesan.rombongan.simpan-peserta` | Data peserta |
| GET | `/pesan/rombongan/template` | `pesan.rombongan.template` | Template peserta |
| GET/POST | `/pesan/{tiketPaket}` | `pesan.create`, `pesan.store` | Buat pesanan |
| GET/POST | `/pembayaran/{pesanan}` | `pembayaran.show`, `pembayaran.process` | Halaman & proses pembayaran |
| GET | `/pembayaran/{pesanan}/selesai` | `pembayaran.selesai` | Selesai pembayaran |
| GET | `/riwayat` | `riwayat.index` | Alihkan ke pesanan saya |
| GET | `/riwayat/{pesanan}` | `riwayat.show` | Detail pesanan |
| POST | `/riwayat/{pesanan}/peserta/{peserta}` | `pesan.rombongan.update-peserta` | Ubah data peserta |
| GET | `/tiket/{tiket}` | `tiket.show` | Detail tiket |
| GET | `/tiket/{tiket}/pdf` | `tiket.pdf` | Unduh PDF tiket |
| GET | `/riwayat/{pesanan}/pdf-rombongan` | `pesanan.pdf-rombongan` | Unduh PDF tiket rombongan |

### Admin (`auth`, `admin`)
| Method | URI | Nama | Keterangan |
|--------|-----|------|------------|
| GET | `/admin` | `admin.dashboard` | Dashboard admin |
| GET | `/admin/pengajuan` | `admin.pengajuan.index` | Daftar pengajuan rombongan |
| POST | `/admin/pengajuan/{pesanan}/setujui` | `admin.pengajuan.approve` | Setujui pengajuan |
| POST | `/admin/pengajuan/{pesanan}/tolak` | `admin.pengajuan.reject` | Tolak pengajuan |
| GET | `/admin/validasi-tiket` | `admin.tiket.validasi` | Halaman validasi tiket |
| POST | `/admin/validasi-tiket` | `admin.tiket.validasi.store` | Proses validasi |
| Resource | `/admin/tiket-paket` | `admin.tiket-paket.*` | Kelola tiket & paket |
| Resource | `/admin/diskon` | `admin.diskon.*` | Kelola diskon |
| GET | `/admin/pesanan` | `admin.pesanan.index` | Daftar pesanan |
| GET | `/admin/pesanan/{pesanan}` | `admin.pesanan.show` | Detail pesanan |
| GET | `/admin/laporan/excel` | `admin.laporan.excel` | Ekspor Excel |
| GET | `/admin/laporan/pdf` | `admin.laporan.pdf` | Ekspor PDF |

## Instalasi

1. Clone repositori:
   ```bash
   git clone <repository-url>
   cd The-Nice-PARK
   ```

2. Install dependensi PHP dan JavaScript:
   ```bash
   composer install
   npm install --ignore-scripts
   ```

3. Salin dan konfigurasikan environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Isi minimal di `.env`:
   - `APP_URL` — URL aplikasi
   - `DB_*` — koneksi database
   - `KEY_MIDTRANS_SERVER_KEY` dan `KEY_MIDTRANS_IS_PRODUCTION` — Midtrans
   - `MAIL_MAILER` dan `MAIL_*` — pengiriman email verifikasi

4. Jalankan migrasi dan build aset:
   ```bash
   php artisan migrate --force
   npm run build
   ```

5. Jalankan server pengembangan:
   ```bash
   php artisan serve
   ```

   Atau jalankan semua proses sekaligus:
   ```bash
   composer run dev
   ```

## Midtrans (Payment Gateway)

Aplikasi terintegrasi dengan Midtrans Snap API. Konfigurasikan di `.env`:

```env
KEY_MIDTRANS_SERVER_KEY=<server-key-anda>
KEY_MIDTRANS_IS_PRODUCTION=false
```

- `PembayaranService` menangani pembuatan transaksi, sinkronisasi status, dan pembuatan e-tiket setelah pembayaran berhasil.
- `PaymentWebhookController` menerima webhook Midtrans dan memverifikasi signature dengan `hash_equals`.

## Pengujian

Jalankan seluruh suite tes dengan Pest:

```bash
vendor/bin/pest
```

Atau jalankan berkas tertentu:

```bash
vendor/bin/pest tests/Feature/UserAreaTest.php
```

## Format Kode

Proyek menggunakan Laravel Pint. Setelah mengubah berkas PHP, jalankan:

```bash
vendor/bin/pint --dirty --format agent
```

## Lisensi

MIT License.
