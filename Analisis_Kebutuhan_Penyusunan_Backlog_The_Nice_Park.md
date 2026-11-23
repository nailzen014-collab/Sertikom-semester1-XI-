Kelompok:

1.Temukan Client

Nama Client: The Nice Park-Rumpin

2.Riset & Analisis Masalah

Ringkasan masalah :

Masalah/Hambatan:

Proses pemesanan yang masih dilakukan secara manual.

pengunjung pun masih bingung dan masih bertanya-tanya tentang pemilihan
paket, harga, bahkan cara pemesanannya.

dan untuk pemesanan tiket secara rombongan, petugas masih harus
mencatatnya dengan manual (data sekolah, penanggung jawab, tanggal
kunjungan, dan juga jumlah pesertanya.

disaat banyak pemesanan yang masuk dengan bersamaan, kami(petugas) masih
kesulitan untuk memastikan pesanan tercatat dengan benar dan pembayaran
tepat waktu.

pengunjung pun masih harus kembali ke petugas untuk menanyakan status
pesanannya.

Kebutuhan / Harapan Client:

aplikasi yang berbasis web, yang bisa memungkinkan untuk pengunjung
memilih pesanan tiket atau paket secara online, menentukan tanggal
kunjungan, melakukan pemesanan, dan juga pastinya membayar secara online
(payment gateway).

untuk paket khusus sekolah/rombongan, kami (petugas) ingin bisa
melakukan peninjauan dan memberikan persetujuan terhadap pelanggan
sebelum mereka melakukan pembayaran.

jika pembayaran sudah terverifikasi, pelanggan akan mendapatkan sebuah
e-ticket dan dapat melihat sebuah riwayat pesanan.

dari sisi pengelola (The nice park), kami ingin halaman admin untuk
mengelola tiket dan juga paket wisatanya, memproses pengajuan sekolah,
serta dapat memantau pesanan dan juga status pembayaran.

Tujuan dari Client:

bisa mempermudah pengunjung untuk membeli tiket umum, dan juga memesan
paket untuk kunjungan sekolah dengan cara online.

dapat menyediakan sebuah informasi terkait tiket, paket wisata, harga,
beserta ketentuan kunjungan secara jelas.

membuatkan pusat pencatatan agar bisa lebih rapih, dan bisa
meminimalisasikan kesalahan-kesalahan.

memudahkan admin-admin untuk meninjau, menyetujui, dan menolak pengajuan
dari sekolah untuk kunjungan.

dapat menyediakan sebuah e-ticket saat setelah melakukan pembayaran dan
diverfikasi.

3.Riset & Analisis Kebutuhan

Ide solusi / Nama sistem:

Aplikasi berbasis website dengan fitur yang dapat disesuaikan dengan
kebutuhannya.

FItur Utama (Secara Umum):

Admin:

dashboard pengelolaan data tiket dan paket, dibagi dua kategori: umum
dan rombongan (tambah, ubah, hapus, aktif/nonaktif, atur harga dan kuota
harian).

pengelolaan diskon: admin mengatur diskon rombongan (untuk pemesanan
lebih dari 10 tiket) dan diskon umum (untuk pemesanan maksimal 10 tiket)
secara terpisah.

halaman khusus untuk monitoring pengajuan kunjungan dari sekolah,
pesanan, dan juga status pembayaran.

meninjau detail pengajuan rombongan (data sekolah, penanggung jawab, dan
daftar peserta), lalu menyetujui atau menolak dengan catatan alasan.

kelola semua pesanan (tiket umum dan rombongan) dengan filter tanggal,
status, dan jenis tiket.

payment gateway (konteks: untuk mengelola pembayaran yang dilakukan oleh
pengunjung, status pembayaran diperbarui otomatis dan bisa diverifikasi
manual jika bermasalah).

validasi e-ticket di pintu masuk (input atau scan kode, tiket yang sudah
dipakai ditandai dan tidak bisa dipakai lagi).

chart untuk admin melihat statistik pembayaran atau pemesanan sesuai
tanggal.

laporan pesanan dan pendapatan yang bisa diekspor ke PDF atau Excel.

pengunjung:

halaman landing page untuk pengenalan wisata dan juga berbagai ketentuan
sebagai pengunjung, termasuk ketentuan pemilihan tiket (tiket umum
maksimal 10 tiket per pesanan, tiket rombongan lebih dari 10 tiket,
beserta informasi diskon masing-masing).

registrasi, login, dan edit profil.

katalog tiket dan paket wisata yang dibagi kategori umum dan rombongan,
beserta harga, diskon, fasilitas, dan ketentuan kunjungan.

pembelian tiket umum: pilih tanggal kunjungan (cek kuota) dan jumlah
tiket maksimal 10; jika lebih dari 10 pengunjung diarahkan ke paket
rombongan; diskon umum diterapkan otomatis.

pemesanan paket rombongan (minimal lebih dari 10 anggota): pilih paket,
isi jumlah anggota (contoh 25 murid), diskon rombongan diterapkan, lalu
isi biodata peserta secara manual atau import dari Excel/CSV.

status pengajuan rombongan (diajukan, disetujui, atau ditolak beserta
alasan); pembayaran terbuka setelah pengajuan disetujui.

pembayaran online melalui payment gateway (satu kali bayar untuk seluruh
rombongan).

e-ticket (nama dan kode unik/QR) yang terbit setelah pembayaran
terverifikasi, bisa diunduh sebagai PDF.

dua opsi tampilan e-ticket rombongan: per siswa (sesuai nama) dan per
rombongan (nama rombongan beserta data siswa).

riwayat dan status pesanan, serta ganti nama peserta sebelum hari
kunjungan.

4.  product backlog

No \| User Story \| Prioritas \| Estimasi Waktu \| Catatan \|

— \| — \| — \| — \| — \|

1 \| Sebagai pengunjung, saya ingin registrasi dan login agar riwayat
pesanan saya tersimpan. \| Tinggi \| 2 hari \| Registrasi, login, edit
profil \|

2 \| Sebagai admin, saya ingin login ke halaman admin agar hanya
pengelola yang dapat mengelola data. \| Tinggi \| 1 hari \| Akses
dibatasi role admin \|

3 \| Sebagai admin, saya ingin menambah, mengubah, dan menghapus tiket
dan paket wisata dalam dua kategori (umum dan rombongan) beserta harga,
kuota harian, dan status aktif agar informasi selalu terbaru. \| Tinggi
\| 3 hari \| CRUD tiket dan paket \|

4 \| Sebagai admin, saya ingin mengatur diskon rombongan dan diskon umum
secara terpisah agar harga promo bisa dikelola. \| Tinggi \| 3 hari \|
Diskon umum berbeda dari diskon rombongan \|

5 \| Sebagai pengunjung, saya ingin melihat landing page dan katalog
tiket/paket beserta harga, diskon, dan ketentuan pemilihan tiket agar
tidak bingung memilih. \| Tinggi \| 3 hari \| Ketentuan: umum maks 10
tiket, rombongan lebih dari 10 tiket \|

6 \| Sebagai pengunjung, saya ingin memilih tanggal kunjungan dan jumlah
tiket (maksimal 10) lalu memesan online agar tidak perlu memesan manual.
\| Tinggi \| 3 hari \| Cek kuota; jika lebih dari 10 diarahkan ke
rombongan; diskon umum otomatis \|

7 \| Sebagai pengunjung, saya ingin membayar pesanan lewat payment
gateway agar pembayaran cepat dan tercatat otomatis. \| Tinggi \| 4 hari
\| Pakai sandbox Midtrans/Xendit, status via webhook \|

8 \| Sebagai pengunjung, saya ingin menerima e-ticket (kode unik/QR)
setelah pembayaran terverifikasi agar bisa langsung dipakai. \| Tinggi
\| 3 hari \| Bisa diunduh sebagai PDF \|

9 \| Sebagai pengunjung, saya ingin melihat riwayat dan status pesanan
agar tidak perlu bertanya ke petugas. \| Tinggi \| 2 hari \| Status:
menunggu bayar, lunas, kedaluwarsa \|

10 \| Sebagai guru, saya ingin mengajukan paket rombongan (lebih dari 10
anggota, tanggal kunjungan, data sekolah) agar rombongan bisa dipesan
online dengan diskon rombongan. \| Tinggi \| 3 hari \| Contoh: 25 murid;
validasi minimal lebih dari 10 \|

11 \| Sebagai guru, saya ingin mengisi biodata peserta rombongan agar
setiap tiket memuat nama siswa. \| Tinggi \| 2 hari \| Jumlah baris
sesuai jumlah anggota \|

12 \| Sebagai guru, saya ingin mengimpor nama siswa dari Excel/CSV agar
tidak mengetik satu per satu. \| Sedang \| 3 hari \| Sediakan template
unduhan dan validasi \|

13 \| Sebagai admin, saya ingin meninjau lalu menyetujui atau menolak
pengajuan rombongan beserta alasannya agar kunjungan terkontrol. \|
Tinggi \| 3 hari \| Pembayaran terbuka setelah disetujui \|

14 \| Sebagai guru, saya ingin melihat status pengajuan dan membayar
setelah disetujui agar proses jelas. \| Tinggi \| 2 hari \| Status:
diajukan, disetujui, ditolak \|

15 \| Sebagai guru, saya ingin e-ticket rombongan tampil per siswa dan
per rombongan agar fleksibel dipakai saat masuk. \| Sedang \| 3 hari \|
Satu data, dua tampilan \|

16 \| Sebagai admin, saya ingin memantau semua pesanan dan status
pembayaran agar tidak ada pesanan yang terlewat. \| Tinggi \| 2 hari \|
Filter tanggal, status, jenis \|

17 \| Sebagai admin, saya ingin melihat chart statistik pemesanan dan
pembayaran per tanggal agar mudah memantau kunjungan. \| Sedang \| 2
hari \| Dashboard \|

18 \| Sebagai admin, saya ingin memvalidasi e-ticket di pintu masuk agar
tiket tidak dipakai dua kali. \| Sedang \| 2 hari \| Input atau scan
kode \|

19 \| Sebagai admin, saya ingin mengekspor laporan pesanan dan
pendapatan agar mudah direkap. \| Rendah \| 2 hari \| PDF/Excel \|

20 \| Sebagai guru, saya ingin mengganti nama peserta sebelum hari
kunjungan agar data tetap sesuai. \| Rendah \| 1 hari \| Jumlah peserta
tetap \|

21 \| Sebagai pengunjung, saya ingin menerima notifikasi status pesanan
lewat email agar tidak perlu mengecek manual. \| Rendah \| 2 hari \|
Opsional, bisa ditambah WhatsApp \|

5.  sprint backlog

No \| User story \| Estimasi waktu \| PIC (Anggota) \|

— \| — \| — \| — \|

Sprint 1 - Fondasi, katalog, dan diskon (total 12 hari) \| Sprint 1 -
Fondasi, katalog, dan diskon (total 12 hari) \| Sprint 1 - Fondasi,
katalog, dan diskon (total 12 hari) \| Sprint 1 - Fondasi, katalog, dan
diskon (total 12 hari) \|

1 \| Registrasi dan login pengunjung \| 2 hari \| \|

2 \| Login admin \| 1 hari \| \|

3 \| CRUD tiket dan paket (kategori umum dan rombongan) \| 3 hari \| \|

4 \| Kelola diskon umum dan rombongan \| 3 hari \| \|

5 \| Landing page, katalog, dan ketentuan pemilihan tiket \| 3 hari \|
\|

Sprint 2 - Pemesanan tiket umum, pembayaran, e-ticket (total 14 hari) \|
Sprint 2 - Pemesanan tiket umum, pembayaran, e-ticket (total 14 hari) \|
Sprint 2 - Pemesanan tiket umum, pembayaran, e-ticket (total 14 hari) \|
Sprint 2 - Pemesanan tiket umum, pembayaran, e-ticket (total 14 hari) \|

6 \| Pilih tanggal dan pesan tiket umum (maks 10) \| 3 hari \| \|

7 \| Pembayaran lewat payment gateway \| 4 hari \| \|

8 \| E-ticket (kode/QR) dan unduh PDF \| 3 hari \| \|

9 \| Riwayat dan status pesanan \| 2 hari \| \|

16 \| Admin pantau pesanan dan status pembayaran \| 2 hari \| \|

Sprint 3 - Paket rombongan (total 10 hari) \| Sprint 3 - Paket rombongan
(total 10 hari) \| Sprint 3 - Paket rombongan (total 10 hari) \| Sprint
3 - Paket rombongan (total 10 hari) \|

10 \| Pengajuan paket rombongan (lebih dari 10 anggota) \| 3 hari \| \|

11 \| Isi biodata peserta rombongan \| 2 hari \| \|

13 \| Admin tinjau, setujui, tolak pengajuan \| 3 hari \| \|

14 \| Status pengajuan dan pembayaran rombongan \| 2 hari \| \|

Sprint 4 - Pelengkap (total 10 hari) \| Sprint 4 - Pelengkap (total 10
hari) \| Sprint 4 - Pelengkap (total 10 hari) \| Sprint 4 - Pelengkap
(total 10 hari) \|

12 \| Import nama siswa dari Excel/CSV \| 3 hari \| \|

15 \| E-ticket rombongan (per siswa dan per rombongan) \| 3 hari \| \|

17 \| Chart statistik di dashboard \| 2 hari \| \|

18 \| Validasi e-ticket di pintu masuk \| 2 hari \| \|

5.  User flow

6.  ERD (Entity Relationship Diagram)


## 5. User Flow

Dokumen sumber memuat diagram alur pengguna pada halaman 7. Alurnya dimulai dari pengunjung membuka landing page dan membaca ketentuan tiket, registrasi/login, lalu memilih kategori tiket:

- **Rombongan:** memilih paket, tanggal, data sekolah, dan jumlah lebih dari 10 tiket; sistem memeriksa kuota dan menerapkan diskon rombongan; peserta diisi manual atau melalui Excel/CSV; admin meninjau pengajuan. Jika ditolak, guru melihat alasan penolakan. Jika disetujui, proses berlanjut ke pembayaran.
- **Umum:** memilih tiket, tanggal, dan jumlah maksimal 10 tiket; sistem memeriksa kuota dan menerapkan diskon umum.
- Kedua jalur berlanjut ke pemilihan metode pembayaran melalui payment gateway. Jika pembayaran berhasil, e-ticket diterbitkan. Jika gagal/tidak selesai, pesanan kedaluwarsa.
- Admin memvalidasi e-ticket di pintu masuk dengan memeriksa kode, tanggal, dan status pemakaian.

## 6. ERD (Entity Relationship Diagram)

Dokumen sumber memuat diagram ERD pada halaman 8. Diagram tersebut menampilkan entitas utama seperti `users`, `wisata_tiket`, `diskon`, `pesanan`, `pengajuan_rombongan`, `pembayaran`, `peserta_rombongan`, dan `tiket`, beserta relasi antarentitas. Detail atribut pada gambar sumber dipertahankan sebagai referensi visual di dokumen DOCX asli.
