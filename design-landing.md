# Spesifikasi Desain & Interaksi: Foxino Travel Landing Page

Dokumen ini berisi rincian struktur antarmuka (UI), konteks konten, sistem warna, tipografi, serta spesifikasi animasi dan mekanisme *carousel* (geser) yang diadaptasi dari referensi visual landing page **Foxino** (destinasi unggulan: **Kerala**).

---

## 1. Konteks & Konsep Utama
* **Domain / Tema:** Pariwisata, Eksplorasi Destinasi & Travel Booking (`Foxino`).
* **Tujuan Halaman:** Memberikan pengalaman visual yang imersif (*immersive full-screen hero*) untuk memikat pengunjung mengeksplorasi destinasi wisata populer, dimulai dari **Kerala, India** (perkebunan teh Munnar yang berkabut), sekaligus menyediakan akses cepat ke destinasi lain melalui kartu *carousel* interaktif.
* **Gaya Desain:** *Modern Editorial Travel*, *Full-Bleed Photography*, *Glassmorphism* ringan pada elemen kontrol, dengan kontras tinggi antara teks putih bersih dan latar belakang alam yang kaya warna.

---

## 2. Struktur & Elemen di Dalam Halaman (Layout Breakdown)

### A. Navigation Bar (Header Transparan)
Terletak di bagian paling atas dengan posisi `absolute` atau `fixed` di atas gambar latar belakang (`z-index: 50`):
1. **Brand Logo (Kiri):** Ikon geometris oranye/koral disertai teks **Foxino** (warna putih, *sans-serif* semi-bold).
2. **Menu Navigasi (Tengah-Kanan):** Tautan minimalis berwarna putih dengan opasitas lembut saat normal dan `100%` saat *hover* (contoh: *Home*, *Destinations*, *Blog*, *About*).
3. **User Greeting / Profile (Kanan Pojok):** Teks sapaan personal (contoh: *"Hello, Alex!"* atau tombol profil/login minimalis).

### B. Full-Screen Hero Background (Latar Belakang Dinamis)
* **Visual Utama:** Foto lanskap resolusi tinggi (*100vw x 100vh*) yang menampilkan hamparan perbukitan kebun teh hijau di Kerala dengan kabut tipis di bagian atas.
* **Overlay Gradient:** Lapisan gradasi gelap halus (*linear-gradient* dari hitam transparan `rgba(0,0,0,0.45)` di sisi kiri dan atas menuju transparan di tengah) untuk memastikan keterbacaan teks putih tetap optimal.
* **Dynamic Background Sync:** Latar belakang utama ini terhubung langsung dengan kartu *carousel* yang sedang aktif.

### C. Hero Content & Call-to-Action (Sisi Kiri)
Berada di area tengah-kiri layar (`flex-col`, rata kiri):
1. **Judul Destinasi Utama (H1):** Teks **KERALA** berukuran sangat besar (*display typography*), kapital semua (*uppercase*), tebal (*bold/extrabold*), warna putih solid (`#FFFFFF`).
2. **Tombol CTA Utama:** Tombol berbentuk pil (*pill-shaped*, `border-radius: 9999px`) berlatar biru cerah (`#2563EB` / `#3B82F6`) dengan teks **Explore** dan ikon panah kanan (`→`) atau tanda tambah (`+`) di sisi kanannya.

### D. Interactive Destination Cards Carousel (Sisi Kanan)
Deretan kartu pratinjau vertikal (*portrait cards*) yang meluap hingga tepi kanan layar (*horizontal overflow*):
* **Struktur Tiap Kartu:**
  * **Header Mini di Atas Kartu:** Nama sub-destinasi/atraksi (berwarna putih kecil) + indikator rating 5 bintang (`★★★★★`) tepat di atas bingkai kartu.
  * **Bingkai Kartu:** Rasio aspek vertikal (`3:4` atau `2:3`), sudut membulat (`border-radius: 16px`), dilengkapi bayangan lembut (*drop-shadow*).
  * **Tombol Bookmark / Wishlist:** Ikon lingkaran putih kecil di pojok kanan atas di dalam setiap kartu untuk menyimpan destinasi.
  * **Isi Kartu (Contoh 3 Kartu Terlihat):**
    1. *Kartu 1 (Aktif/Fokus):* Pemandangan gunung dan danau berkabut bernuansa biru gelap (*Munnar / Western Ghats*).
    2. *Kartu 2:* Arsitektur istana/kuil megah dengan refleksi air (*Mysore / Kerala Heritage*).
    3. *Kartu 3 (Terpotong di tepi kanan sebagai petunjuk geser):* Tebing pantai dan deburan ombak (*Varkala Cliff*).

### E. Kontrol Navigasi & Indikator Slide (Bagian Bawah)
* **Pagination Dots / Progress Indicator:** Titik-titik indikator kecil di bagian bawah-tengah untuk menunjukkan posisi *slide* aktif.
* **Slide Controls (Opsional / Interaktif):** Mendukung gestur geser (*drag/swipe*), gulir horizontal (*trackpad*), maupun tombol panah kiri/kanan.

---

## 3. Spesifikasi Animasi & Mekanisme Geser (Motion & Carousel Specs)

Agar halaman terasa hidup dan responsif seperti referensi video aslinya, terapkan perilaku animasi berikut:

### A. Mekanisme Geser (Horizontal Carousel)
1. **Gestur & Input:**
   * **Touch / Mouse Drag:** Pengguna dapat mengklik dan menyeret deretan kartu ke kiri atau kanan dengan efek *momentum* dan *snap-to-card* (`scroll-snap-type: x mandatory`).
   * **Klik Kartu:** Mengklik kartu kedua atau ketiga otomatis menggeser kartu tersebut ke posisi utama (paling kiri dalam deretan kartu).
   * **Keyboard Navigation:** Mendukung tombol `ArrowLeft` dan `ArrowRight`.
2. **Hierarki Skala Kartu (Active vs Inactive):**
   * Kartu yang berada di posisi pertama (aktif) memiliki skala penuh (`scale(1.0)`) dan opasitas `100%`.
   * Kartu berikutnya di sebelah kanan sedikit lebih kecil (`scale(0.92)`) dengan sedikit pergeseran vertikal atau opasitas `85%` untuk menciptakan kedalaman visual (*depth*).

### B. Sinkronisasi Latar Belakang & Teks (Hero Transition)
Saat pengguna menggeser *carousel* ke destinasi berikutnya (misalnya dari **Kerala** ke **Varkala** atau **Jaipur**):
1. **Crossfade & Ken Burns Effect pada Background:**
   * Gambar latar belakang layar penuh berganti menggunakan transisi *crossfade* halus (`duration: 700ms`, `ease-in-out`).
   * Gambar latar yang baru masuk disertai efek *subtle zoom-out* perlahan (dari `scale(1.08)` ke `scale(1.0)` selama 6–8 detik) agar latar belakang terasa sinematik dan tidak statis.
2. **Text Reveal Animation (Judul & CTA):**
   * Judul destinasi lama memudar ke atas (`opacity: 0`, `translateY(-20px)`).
   * Judul destinasi baru muncul dari bawah dengan efek *masked slide-up* (`opacity: 0 -> 1`, `translateY(24px) -> 0`, `duration: 500ms`, `cubic-bezier(0.16, 1, 0.3, 1)`).
   * Tombol **Explore** menyusul dengan *stagger delay* `100ms`.

### C. Micro-Interactions (Interaksi Mikro)
* **Hover Kartu:** Kartu terangkat sedikit (`translateY(-6px)`), gambar di dalam kartu melakukan *zoom-in* halus (`scale(1.05)`, `duration: 400ms`).
* **Hover Tombol Explore:** Warna biru sedikit lebih terang, ikon panah/plus bergeser ke kanan (`translateX(4px)`).
* **Klik Ikon Bookmark:** Animasi *spring bounce* (`scale(0.8) -> scale(1.2) -> scale(1.0)`) dan perubahan warna ikon menjadi oranye/merah.

---

## 4. Panduan Token Desain (Design Tokens)

| Kategori | Token / Nilai | Keterangan |
| :--- | :--- | :--- |
| **Primary Text** | `#FFFFFF` | Judul utama, navigasi, label kartu |
| **Secondary Text** | `rgba(255, 255, 255, 0.75)` | Sub-label, menu tidak aktif |
| **Accent Blue (CTA)** | `#2563EB` (Hover: `#1D4ED8`) | Tombol utama "Explore" |
| **Brand Accent** | `#F97316` | Ikon logo Foxino & rating bintang |
| **Card Overlay / Glass** | `rgba(255, 255, 255, 0.15)` + `backdrop-blur(8px)` | Kontrol navigasi / badge |
| **Hero Title Font** | `Plus Jakarta Sans` / `Montserrat`, `800 ExtraBold` | Ukuran `4.5rem` – `6.5rem`, *letter-spacing* `0.04em` |
| **Body & UI Font** | `Inter` / `Plus Jakarta Sans`, `400` – `600` | Ukuran `0.875rem` – `1rem` |
| **Card Border Radius** | `16px` (`1rem`) | Sudut kartu destinasi |
| **Button Border Radius** | `9999px` | Tombol pil penuh |

---

## 5. Rekomendasi Implementasi Teknis (Tech Stack)
Jika ingin langsung diimplementasikan ke dalam kode web:
* **HTML/CSS + Tailwind CSS:** Untuk *layout* responsif, gradasi *overlay*, dan tipografi.
* **Swiper.js atau Embla Carousel:** Untuk menangani fitur *drag/swipe* kartu yang mulus serta *event listener* `onSlideChange` guna memicu pergantian latar belakang dan judul secara sinkron.
* **GSAP (GreenSock) atau Framer Motion:** Untuk animasi transisi teks (*staggered slide-up*) dan efek *Ken Burns* pada gambar latar belakang.
