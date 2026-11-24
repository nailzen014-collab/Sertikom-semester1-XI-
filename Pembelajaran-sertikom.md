# Pembelajaran Sidang — The Nice Park (Laravel + Midtrans)

Catatan jawaban untuk tiap topik, merujuk file & baris nyata di proyek.

## PRIORITAS 1

### 1. Middleware dan Otorisasi

**Autentikasi vs Otorisasi**
- Autentikasi = "siapa kamu" → guard `auth` bawaan Laravel. Dipasang via middleware `'auth'` (routes/admin.php:12, routes/user.php:11).
- Otorisasi = "boleh apa" → middleware custom (`admin`, `pengunjung`) + Policy (`PesananPolicy`).

**Middleware role: file & pendaftaran**
- `app/Http/Middleware/EnsureUserIsAdmin.php:14-19` → `abort_unless($request->user()?->isAdmin(), 403);`
- `app/Http/Middleware/EnsureUserIsPengunjung.php:12-16` → `abort_unless($request->user()?->role === User::ROLE_PENGUNJUNG, 403);`
- `isAdmin()` di `app/Models/User.php:45-48`; default role pengunjung di User.php:28-30.
- Alias didaftarkan di `bootstrap/app.php:17-20` (`admin`, `pengunjung`).

**Dipasang di route mana**
- Admin: routes/admin.php:12 → `Route::middleware(['auth','admin'])->prefix('admin')`.
- User: routes/user.php:11 → `Route::middleware(['auth','pengunjung'])`.

**Kalau pelanggan buka /admin**
- Sudah login tapi bukan admin → 403 Forbidden (abort_unless). Belum login → 'auth' redirect ke /login.

**Kenapa harus di server**
- Menyembunyikan tombol hanya kosmetik; request tetap bisa dikirim manual. Pengecekan di middleware + Policy menolak di server.

**Cegah B buka pesanan A (IDOR)**
- Pakai Policy: `app/Policies/PesananPolicy.php:10-13` (`$pesanan->user_id === $user->id`).
- Didaftarkan di AuthServiceProvider.php:16-18.
- Dipanggil via `$this->authorize('view', $pesanan)` di PembayaranController.php:66-69 dan RiwayatController.php:37.
- Alternatif inline (Gate manual) di RombonganController.php:211.

### 2. Perhitungan Total di Server

**Kenapa tak boleh dari form**: input bisa diubah via DevTools/request langsung.

**Alur**: `app/Services/HitungHargaService.php:14-26` — ambil harga dari DB (`$tiketPaket->harga`), kali jumlah, kurangi diskon.
Dipakai di PesananController.php:92-102 (umum) dan RombonganController.php:167-177 (rombongan).

**Bukti**: StorePesananRequest.php:19-22 hanya izinkan `tanggal_kunjungan` & `jumlah_tiket`. `total_bayar`/`harga` tidak ada di validated() → diabaikan.

**Snapshot harga**: kolom harga_satuan, potongan_diskon, total_bayar di create_pesanan_table.php:20-22. Pesanan lama tak berubah saat harga produk diubah.

### 3. Dari Mana Status "Lunas"

**Hanya dari server**: satu-satunya tempat set lunas = PembayaranService.php:109-111 (`proses()`).

**Dua jalur**:
1. Webhook PaymentWebhookController.php:12-34 (POST /webhook/midtrans, routes/webhook.php:6). Dikecualikan CSRF (bootstrap/app.php:22-24), dilindungi signature.
2. Cek aktif API: sinkronkanDariGateway() (PembayaranService.php:64-82), dipanggil di PembayaranController.php:46.

**Verifikasi signature**: PaymentWebhookController.php:40-56 — sha512(order_id + status_code + gross_amount + serverKey) dengan hash_equals. order_id cari record (baris 22). gross_amount ditandatangani → 403 kalau diubah.
Catatan: gross_amount belum dibandingkan eksplisit dgn pesanan.total_bayar (tersirat via signature).

**Idempotent**: proses() dibungkus DB::transaction + lockForUpdate (baris 90), early-return jika sudahBerhasil (baris 92-94). TiketGenerator.php:33-35 early-return jika tiket sudah ada.

**Alur Midtrans Sandbox**: Snap::createTransaction (PembayaranService.php:37-59) → redirect (PembayaranController.php:39) → bayar → webhook → proses() → update + generate tiket.

**Server Key di .env**: config('services.midtrans.server_key') (PembayaranService.php:120-126). Bocor → signature bisa dipalsukan. .env di .gitignore baris 3.

### 4. Eager Loading dan N+1

**N+1**: 1 query daftar + N query relasi per baris. 20 pesanan + user + produk = ~41 query.

**Contoh**: admin/pesanan/index.blade.php:73-76 akses `$item->user->nama` & `$item->tiketPaket->nama`.

**Perbaikan**: Admin/PesananController.php:15 `Pesanan::with(['user','tiketPaket'])`; RiwayatController.php:23-29 `with([...])`. Query jadi tetap (2-3) tak bertambah per baris.

**Cara hitung**: barryvdh/laravel-debugbar (composer.json) atau DB::enableQueryLog()/DB::getQueryLog()/DB::listen().

## PRIORITAS 2

### Alur MVC (pesan tiket)
1. Route: routes/user.php:24 POST /pesan/{tiketPaket} → PesananController@store.
2. Controller: PesananController::store (38-106) + StorePesananRequest.
3. Service: HitungHargaService, KuotaService, TiketGenerator.
4. Model: Pesanan::create (baris 92), relasi belongsTo(TiketPaket/User).
5. Blade: user/dashboard.blade.php via RiwayatController::orders.
Logika bisnis di Service, bukan Blade.

### Database & Eloquent
- Migration = skema; Seeder = data awal (database/seeders/DatabaseSeeder.php). `php artisan migrate --seed`.
- Relasi: User hasMany Pesanan (User.php:55-58); Pesanan belongsTo User/TiketPaket/Diskon, hasMany Pembayaran/Tiket/PesertaRombongan, hasOne PengajuanRombongan (Pesanan.php:66-99); Pembayaran & Tiket belongsTo Pesanan.
- PK = id. FK: pesanan.user_id→users.id, tiket_paket_id→tiket_paket.id (restrictOnDelete) (create_pesanan_table.php:14-15). Kardinalitas: User 1:N Pesanan; Pesanan 1:N Tiket; Pesanan 1:1 PengajuanRombongan.

### Validasi
- Form Request + $request->validate (RombonganController.php:218-221).
- Aturan: umum 1-10 (StorePesananRequest.php:21 min:1; >10 dialihkan PesananController.php:52-59); rombongan >10 (StorePengajuanRombonganRequest.php:24 min:11); tanggal tak lampau (after_or_equal:today, StorePesananRequest.php:20); produk nonaktif ditolak (PesananController.php:72-76, RombonganController.php:143-147, KatalogController.php:13); kuota (PesananController.php:84-88).

### Alur paket sekolah
- Status: menunggu_persetujuan → disetujui → menunggu_pembayaran (atau ditolak). Konstanta Pesanan.php:18-28.
- Approve: PengajuanRombonganController.php:56-58 set MENUNGGU_PEMBAYARAN.
- Belum disetujui tak bisa bayar walau request langsung: PembayaranController::periksaStatus (52-64) hanya izinkan status MENUNGGU_PEMBAYARAN.
- Penolakan: catatan_admin disimpan (baris 81), status DITOLAK (baris 86), ditampilkan ke pelanggan.

### CRUD produk — deactivate vs delete
- FK restrictOnDelete (create_pesanan_table.php:15) mencegah hapus produk yang sudah dipakai pesanan.
- TiketPaketController::destroy (50-55) hard-delete → ditolak DB bila terpakai.
- Praktik benar: nonaktifkan via status_aktif (TiketPaket.php:44-47, toggle TiketPaketController.php:65). Riwayat tetap utuh.

### Keamanan dasar
- CSRF: @csrf di form; bootstrap/app.php:22 kecualikan hanya webhook/midtrans (dilindungi signature).
- Password hash: cast 'hashed' (User.php:41).
- .env di .gitignore (baris 3); .env.example disertakan.

## PRIORITAS 3 (Dokumen & Proses)
- README.md: cara jalankan (baris 77-117), tabel rute, Midtrans & testing.
- Dokumen: Analisis_Kebutuhan_Backlog, PRD, SDLC, user_flow_jelas.png, plans/.
- User flow: user_flow_jelas.png. ERD: belum ada file khusus (gambar dari relasi model). Matriks hak akses: user.php vs admin.php.
- Dua kategori tiket: TiketPaket::KATEGORI umum vs rombongan (TiketPaket.php:14-21). Alasan: umum langsung bayar; rombongan >10 butuh persetujuan + daftar peserta.
- Riwayat commit: git log --oneline.
- Laporan pengujian: Pest (tests/Feature: Sprint1Test, Sprint2Test, UserAreaTest, BacklogFiturLanjutanTest, AdminDashboardTest, Auth). `vendor/bin/pest`.

## Dua catatan kejujuran teknis (antisipasi penguji)
1. Webhook memverifikasi signature yang mencakup gross_amount, tapi belum membandingkan eksplisit dengan pesanan.total_bayar.
2. destroy() produk masih hard-delete, dijaga FK restrictOnDelete; praktik nonaktif via status_aktif yang dianjurkan.