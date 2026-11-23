---
name: web-design-discovery
description: Melakukan wawancara strategis dan mengumpulkan kebutuhan desain website (UI/UX, brand, konten, dan struktur) dari user sebelum membuat wireframe, mockup, atau kode website. Gunakan skill ini setiap kali user meminta dibuatkan desain atau tampilan website baru.
---

# Web Design Discovery & Requirement Gathering Skill

## Tujuan Utama
Jangan pernah langsung men-generate kode atau desain website saat user baru memberikan prompt awal yang umum (contoh: *"Buatkan website untuk bisnis kopi saya"*). Jalankan fase **Discovery** terlebih dahulu untuk memastikan desain sesuai dengan tujuan bisnis dan selera visual user.

## Aturan Interaksi (Wajib Dipatuhi)
1. **Bertahap (Maksimal 3 Pertanyaan):** Ajukan hanya 2 sampai 3 pertanyaan per giliran chat agar user tidak kewalahan.
2. **Berikan Opsi (Multiple Choice/Contoh):** Jika user terlihat bingung, berikan 2-3 opsi gaya atau contoh konkret agar user mudah memilih.
3. **Konfirmasi Sebelum Eksekusi:** Setelah semua fase selesai, rangkum hasilnya dalam bentuk **Design Brief** dan minta persetujuan user (`"Apakah brief ini sudah sesuai untuk mulai saya buatkan desain/kodenya?"`).

---

## Alur Pertanyaan (4 Fase Discovery)

### Fase 1: Tujuan & Target Audiens
Gali konteks dasar bisnis sebelum memikirkan estetika:
- Apa tujuan utama website ini? (Contoh: *Lead generation/isi form, penjualan langsung, portofolio, atau profil perusahaan*).
- Siapa target audiens utamanya dan apa masalah yang ingin mereka selesaikan?
- Apa satu hal paling unik (USP) dari produk/jasa ini?

### Fase 2: Gaya Visual & Brand Identity
Terjemahkan karakter brand ke dalam elemen UI:
- Bagaimana *vibe* atau kepribadian desain yang diinginkan? (Tawarkan opsi: *Minimalis & Bersih, Korporat & Profesional, Modern & Bold, atau Hangat & Estetik*).
- Apakah sudah ada warna brand (*brand palette*), logo, atau font khusus? Jika belum, tanyakan preferensi tema (*Light mode* atau *Dark mode*).
- Apakah ada 1–2 website referensi yang disukai tata letak atau nuansanya?

### Fase 3: Struktur Halaman & Konten
Tentukan arsitektur informasi:
- Bagian (*section*) atau halaman apa saja yang wajib ada? (Contoh: *Hero, Fitur/Layanan, Testimoni, Harga, FAQ, Kontak*).
- Apakah teks (*copywriting*) dan aset gambar sudah tersedia, atau agent perlu membuatkan draf *copywriting* serta menggunakan gambar *placeholder* terlebih dahulu?

### Fase 4: Fungsionalitas & Perangkat
Pastikan kesiapan UX teknis:
- Apakah ada elemen interaktif khusus yang dibutuhkan? (Contoh: *Tombol CTA mengarah ke WhatsApp, formulir pemesanan, kalkulator harga, atau animasi scroll*).
- Apakah fokus utama tampilan ditujukan untuk pengguna HP (*Mobile-First*) atau Desktop?

---

## Format Output Akhir (Design Brief)
Setelah Fase 1–4 terjawab, tampilkan ringkasan berikut sebelum masuk ke tahap pembuatan desain/kode:

```text
RINGKASAN DESIGN BRIEF
- Tujuan Utama: [Hasil Fase 1]
- Target Audiens: [Hasil Fase 1]
- Gaya Visual & Warna: [Hasil Fase 2]
- Struktur Halaman/Section: [Hasil Fase 3]
- Fitur & Fokus Perangkat: [Hasil Fase 4]
```

## Langkah Lanjutan (Setelah Disetujui)
Setelah user menyetujui Design Brief di atas:
1. Buat struktur wireframe teks (hierarki section dari atas ke bawah).
2. Lanjutkan pembuatan kode frontend yang bersih, responsif, dan sesuai dengan warna serta gaya visual yang disepakati.
