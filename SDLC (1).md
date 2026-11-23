# SDLC - Sistem Pemesanan Tiket & Paket Wisata The Nice Park

## 1. Model yang Dipakai

**Agile - Scrum** (iteratif per sprint). Dipilih karena proyek sudah memakai Product Backlog dan Sprint Backlog, kebutuhan klien bisa berubah (misalnya aturan diskon atau batas rombongan), dan hasil bisa dilihat bertahap.

Tujuh tahap SDLC dijalankan di dalam sprint: tahap 1-3 dilakukan di awal (sprint 0), tahap 4-5 berulang di tiap sprint, tahap 6-7 di akhir.

## 2. Tahap SDLC dan Hasilnya

| # | Tahap | Kegiatan | Hasil (deliverable) | Status |
|---|---|---|---|---|
| 1 | **Perencanaan (Planning)** | Menemukan klien, menentukan ruang lingkup, tim, jadwal, tools | Profil klien, rencana sprint | Selesai |
| 2 | **Analisis Kebutuhan** | Wawancara klien, merumuskan masalah, kebutuhan, dan tujuan; menyusun fitur per role; aturan tiket umum dan rombongan | Dokumen Riset & Analisis Kebutuhan, Fitur Utama, Product Backlog | Selesai |
| 3 | **Desain** | User flow, ERD, desain UI (wireframe/mockup Figma), rancangan struktur aplikasi | User flow, ERD, wireframe, `PRD.md` | Sebagian (UI belum) |
| 4 | **Implementasi (Coding)** | Membuat migration, model, controller, view, integrasi payment gateway per sprint | Kode aplikasi di repository Git | Berikutnya |
| 5 | **Pengujian (Testing)** | Unit/feature test, uji fungsional per role, UAT dengan klien | Laporan pengujian, daftar bug | Per sprint |
| 6 | **Penerapan (Deployment)** | Hosting, konfigurasi domain, environment produksi, ganti payment gateway ke mode live | Aplikasi live | Akhir |
| 7 | **Pemeliharaan (Maintenance)** | Perbaikan bug, penyesuaian aturan/diskon, pencadangan data | Catatan perbaikan | Setelah rilis |

## 3. Peran Tim (Scrum)

Sesuaikan nama dengan anggota kelompok.

| Peran | Tanggung jawab |
|---|---|
| Product Owner | Mewakili klien (The Nice Park); menentukan prioritas backlog |
| Scrum Master | Memastikan sprint berjalan, menghapus hambatan |
| Developer | Membuat backend, frontend, dan database |
| Tester / UI | Menguji fitur, menyiapkan desain UI |

> Kolom PIC di Sprint Backlog diisi dengan nama anggota sesuai peran ini.

## 4. Jadwal Sprint

Estimasi dari Sprint Backlog (satuan hari kerja, bisa disesuaikan dengan durasi sprint).

| Sprint | Fokus | Story (No. backlog) | Estimasi |
|---|---|---|---|
| **Sprint 0** | Persiapan: setup proyek Laravel, database, Git, desain UI dasar | - | menyesuaikan |
| **Sprint 1** | Fondasi, katalog, diskon | 1, 2, 3, 4, 5 | 12 hari |
| **Sprint 2** | Tiket umum, pembayaran, e-ticket, riwayat, monitoring pesanan | 6, 7, 8, 9, 16 | 14 hari |
| **Sprint 3** | Paket rombongan | 10, 11, 13, 14 | 10 hari |
| **Sprint 4** | Pelengkap | 12, 15, 17, 18 | 10 hari |
| **Rilis** | Uji menyeluruh, UAT klien, deploy | - | menyesuaikan |
| **Backlog lanjutan** | Laporan, ganti nama peserta, notifikasi | 19, 20, 21 | 5 hari |

### Rincian per sprint

- **Sprint 1:** Registrasi/login pengunjung, login admin, CRUD tiket & paket (kategori umum/rombongan), kelola diskon, landing page + katalog + ketentuan pemilihan tiket.
- **Sprint 2:** Pesan tiket umum (maks 10, cek kuota, diskon umum), integrasi payment gateway (webhook), e-ticket (kode/QR + PDF), riwayat pesanan, admin memantau pesanan dan pembayaran.
- **Sprint 3:** Pengajuan rombongan (>10), biodata peserta, admin tinjau/setujui/tolak, status pengajuan dan pembayaran rombongan.
- **Sprint 4:** Import Excel/CSV, e-ticket rombongan dua tampilan, chart dashboard, validasi e-ticket di pintu masuk.

## 5. Seremoni Scrum

| Kegiatan | Kapan | Tujuan |
|---|---|---|
| Sprint Planning | Awal sprint | Memilih story dari Product Backlog dan membaginya ke anggota |
| Daily Stand-up | Harian (singkat) | Apa yang dikerjakan, rencana hari ini, hambatan |
| Sprint Review | Akhir sprint | Demo hasil ke klien/guru, kumpulkan masukan |
| Sprint Retrospective | Setelah review | Apa yang berjalan baik dan apa yang diperbaiki |

## 6. Strategi Pengujian

| Jenis | Cakupan | Contoh |
|---|---|---|
| Unit / Feature test (PHPUnit/Pest) | Aturan bisnis di server | Pesanan umum 11 tiket ditolak; rombongan 10 tiket ditolak; diskon rombongan tidak berlaku untuk pesanan umum |
| Uji fungsional | Alur per role | Pengunjung dari login sampai e-ticket; admin menyetujui pengajuan |
| Uji integrasi | Payment gateway sandbox | Webhook sukses mengubah pesanan menjadi `lunas`; webhook ganda tidak membuat tiket ganda |
| Uji keamanan dasar | Hak akses | Pengunjung tidak bisa membuka `/admin`; tidak bisa melihat pesanan orang lain |
| Uji kuota | Konkurensi | Dua pesanan bersamaan tidak melebihi kuota harian |
| UAT | Penerimaan klien | Klien mencoba alur nyata di akhir tiap sprint atau saat rilis |

### Contoh kasus uji kritis

1. Umum: jumlah 10 -> berhasil; jumlah 11 -> diarahkan ke rombongan.
2. Rombongan: jumlah 10 -> ditolak; jumlah 11 -> berhasil.
3. Rombongan belum disetujui -> tombol/endpoint bayar tidak bisa diakses.
4. Pengajuan ditolak -> alasan terlihat oleh guru.
5. Pembayaran gagal/kedaluwarsa -> e-ticket tidak terbit.
6. Tiket yang sudah dipakai discan lagi -> ditolak.
7. Jumlah baris biodata tidak sama dengan jumlah tiket -> ditolak.

## 7. Definisi Selesai (Definition of Done)

Sebuah story dianggap selesai jika:
- Sesuai kriteria penerimaan di `PRD.md`.
- Aturan bisnis divalidasi di server.
- Lolos pengujian (minimal satu feature test untuk aturan bisnisnya).
- Tampilan berbahasa Indonesia dan responsif.
- Sudah di-merge ke branch utama lewat review anggota lain.
- Sudah didemokan di Sprint Review.

## 8. Manajemen Kode (Git)

- Branch: `main` (stabil), `develop` (gabungan sprint), `feature/<nama-fitur>` per story.
- Commit kecil dan jelas, mis. `feat: validasi maksimal 10 tiket umum`.
- Pull request ditinjau minimal satu anggota lain sebelum merge.
- Jangan commit file `.env` atau kunci API payment gateway.

## 9. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Integrasi payment gateway lebih lama dari estimasi | Sprint 2 molor | Mulai dengan sandbox sejak awal; pakai library resmi |
| Aturan klien berubah (batas rombongan, diskon) | Perubahan di banyak tempat | Simpan aturan di satu service/config, jangan di-hardcode di banyak view |
| Pesanan bersamaan melebihi kuota | Data tidak konsisten | Transaksi database dan locking saat cek kuota |
| Anggota tim berhalangan | Backlog tertunda | Pembagian PIC fleksibel, prioritaskan P1 |
| Waktu tugas terbatas | Fitur tidak selesai | Kerjakan P1 dulu; P2 dan P3 bisa menyusul |

## 10. Kriteria Rilis (MVP)

Minimal siap rilis jika semua story **Prioritas Tinggi** selesai: registrasi/login, katalog dan ketentuan, kelola tiket/paket/diskon, pesan tiket umum, pembayaran, e-ticket, riwayat, pengajuan dan persetujuan rombongan, serta pemantauan pesanan admin.
