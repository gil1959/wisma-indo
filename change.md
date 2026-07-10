# PROMPT: Clone & Ubah Fitur bintangwisataholiday.com → Web Iklan Properti/Barang/Jasa (ala rumaindo.com)

## KONTEKS PROYEK

Clone struktur, layout, dan design system dari **bintangwisataholiday.com** (web travel agency).
**Design/layout/komponen visual TETAP DIPERTAHANKAN** — yang diubah HANYA fitur dan isi konten,
diarahkan menjadi platform marketplace iklan **Properti, Barang, dan Jasa** dengan referensi fitur
dari **rumaindo.com**.

Jangan buat desain baru dari nol. Reuse struktur card, section, spacing, dan komponen yang sudah
ada di source, cuma reskin data model & isi kontennya.

**Fitur yang DIKECUALIKAN (tidak perlu dibuat):** PPOB.

---

## 1. NAVIGASI (NAVBAR)

### 1.1 Navbar default (belum login / state normal)

```
Logo | Dijual | Disewakan | Properti Terbaru | Cari Barang & Jasa | Simulasi Nilai Properti     [Pasang Iklan] [✉] [👤]
```

- `Dijual` → `/dijual`
- `Disewakan` → `/disewakan`
- `Properti Terbaru` → `/properti`
- `Cari Barang & Jasa` → `/barang-dan-jasa`
- `Simulasi Nilai Properti` → `/simulasi`
- Tombol **"Pasang Iklan"** (pill button, icon megaphone) — selalu terlihat menonjol di kanan
- Icon amplop (✉) — notifikasi pesan
- Icon profil (👤) — trigger dropdown menu (lihat 1.3)

### 1.2 Navbar berubah setelah masuk ke dashboard akun (klik "Akun Saya")

```
Logo | Properti Terbaru | Iklan Favorit | Iklan Saya | Top Up | Transaksi     [Pasang Iklan] [✉] [👤]
```

- `Properti Terbaru` → `/properti`
- `Iklan Favorit` → `/iklan-favorit`
- `Iklan Saya` → `/iklan-saya`
- `Top Up` → `/top-up`
- `Transaksi` → `/transaksi`

> Catatan: menu utama (poin 1.1) otomatis "digantikan" oleh menu dashboard ini ketika user
> berada di dalam area akun (`/akun`, `/iklan-saya`, `/top-up`, `/transaksi`, dll). Di luar area
> itu, navbar tetap pakai struktur 1.1.

### 1.3 Dropdown saat klik icon profil (👤)

**Kondisi belum login / generic:**
```
Akun Saya
Notifikasi
Iklan Saya
~~PPOB~~   ← DIHAPUS (fitur dikecualikan)
─────────
Co-Broke Hub
Artikel Inspirasi
─────────
KELUAR (warna merah)
```

**Kondisi setelah login (state final, sesuai referensi gambar):**
Dropdown menampilkan struktur yang sama seperti di atas — pastikan semua item tetap tampil
konsisten baik sebelum maupun sesudah login, kecuali label "KELUAR" hanya muncul saat sudah login
(ganti jadi "MASUK / DAFTAR" kalau belum login).

### 1.4 Tombol "Pasang Iklan" (klik dari navbar)

Trigger modal/dropdown pilihan kategori sebelum masuk form:

```
Mau pasang iklan apa?
┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│  Properti   │ │   Barang    │ │    Jasa     │
│ Iklan Hunian│ │ Perlengkapan│ │  Layanan    │
│  & Tanah    │ │             │ │ Profesional │
└─────────────┘ └─────────────┘ └─────────────┘
      [Lanjutkan →]
```

Field/copy per kategori:
- **Properti** — "Iklan Hunian & Tanah" — "Jual atau sewa rumah, apartemen, tanah, dll."
- **Barang** — "Perlengkapan" — "Jual barang elektronik, otomotif, perabotan."
- **Jasa** — "Layanan Profesional" — "Tawarkan jasa profesional atau keahlian Anda."

Pilih satu kartu (radio-select style, border biru saat aktif) → klik "Lanjutkan" → masuk form
pasang iklan sesuai kategori yang dipilih.

---

## 2. HALAMAN HOME

Struktur section **tetap ikuti layout asli** (hero → promo strip → listing sections → artikel →
kategori grid → why choose us → steps → trust logos → CTA → footer). Yang diubah adalah isi teks,
CTA, dan data listing.

### 2.1 Hero Section

Pertahankan desain hero asli (background, tipografi besar), ganti CTA grid jadi 3 pasang CTA
berikut (title + subtitle, masing-masing clickable):

| CTA Title | Subtitle | Link |
|---|---|---|
| Cari Properti | Beli & Sewa | `/iklan` |
| Pasang Iklan | Jual & Sewa | `/pasang-iklan` |
| Simulasi & Nilai Properti | — | `/simulasi` |
| Kebutuhan Barang & Jasa | — | `/barang-dan-jasa` |
| Baca Al Quran | Kumpulan surah & terjemahan digital | `/quran` |
| Co-Broke System | Kerjasama & bagi komisi antar agen resmi | `/co-broke` |

### 2.2 Section: Tipe Properti Terpopuler

- Judul: **"Tipe Properti Terpopuler"**
- Deskripsi: *"Cari properti impian Anda mulai dari rumah minimalis, apartemen modern, ruko
  strategis, hingga tanah kavling siap bangun."*
- Grid kategori: Rumah, Apartemen, Ruko, Tanah Kavling (reuse layout grid kategori destinasi
  yang ada di source, ganti icon & label)

### 2.3 Section: Kebutuhan Barang & Jasa

- Judul: **"Kebutuhan Barang & Jasa"**
- Deskripsi: *"Pusat penyedia jasa renovasi rumah, perawatan properti, dan perlengkapan rumah
  tangga terpercaya terlengkap di sekitar Anda."*

### 2.4 Section: Lokasi Unggulan & Strategis

- Judul: **"Lokasi Unggulan & Strategis"**
- Deskripsi: *"Daftar kawasan favorit dengan akses transportasi mudah, fasilitas publik lengkap,
  dan nilai investasi properti tinggi."*
- Reuse layout grid destinasi asli → ganti isi jadi nama kawasan/kota

### 2.5 Section: Rekomendasi Jual Beli & Sewa Properti

- Judul: **"Rekomendasi Jual Beli & Sewa Properti"**
- Deskripsi: *"Temukan iklan rumah dijual, sewa apartemen murah, dan ruko komersial dari agen
  terverifikasi."*
- Reuse card listing (lihat struktur card di poin 4)

### 2.6 Section: Daftar Barang & Jasa Terdekat Terpilih

- Judul: **"Daftar Barang & Jasa Terdekat Terpilih"**
- Deskripsi: *"Rekomendasi penyedia barang kebutuhan rumah tangga serta layanan profesional
  murah dan terdekat dari lokasi Anda."*
- Reuse card listing, versi barang/jasa (lihat poin 4.2)

### 2.7 Section lain yang tetap dipertahankan dari source (isi disesuaikan)

- Artikel/Blog → ganti isi jadi artikel properti (tips KPR, legalitas, renovasi, dll)
- "Why choose us" (4 value prop) → sesuaikan copy ke value proposition marketplace properti
- Trust logos → ganti jadi logo partner bank KPR / asosiasi REI (opsional, bisa placeholder)
- Footer → lihat poin 7

---

## 3. HALAMAN LISTING (Dijual / Disewakan / Properti Terbaru / Cari Barang & Jasa)

Keempat halaman ini **memakai layout & komponen yang sama persis** (reuse satu template listing
page), yang beda cuma sumber data & default filter-nya.

### 3.1 Struktur umum tiap halaman listing

```
Breadcrumb: Rumaindo > [Nama Halaman]
Judul halaman
─────────────────────────────
Tab pilihan kategori: [Properti] [Barang] [Jasa]
─────────────────────────────
[Sidebar Filter]              [Grid hasil listing]
```

### 3.2 Sidebar Filter (field lengkap, reuse dari referensi `/iklan-saya`)

- Kata Kunci (search box: "Cari judul iklan...")
- Status Iklan (dropdown: Semua Status / Tersedia / Terjual / dll)
- Urutkan (dropdown: Relevansi / Terbaru / Harga Terendah / Harga Tertinggi)
- Jenis Transaksi (toggle: Semua / Jual / Sewa)
- Lokasi / Daerah (dropdown)
- Rentang Harga (input Min – Max)
- **Tambahan khusus kategori Properti** (tidak ada di sumber, harus ditambah):
  - Tipe Properti (Rumah / Apartemen / Ruko / Tanah)
  - Jumlah Kamar Tidur
  - Luas Tanah / Bangunan (m²)

### 3.3 Perbedaan isi per halaman

| Halaman | Default filter | Isi konten |
|---|---|---|
| `/dijual` | Jenis Transaksi = Jual, semua kategori (Properti/Barang/Jasa) | Semua listing dengan status jual |
| `/disewakan` | Jenis Transaksi = Sewa | Semua listing dengan status sewa |
| `/properti` | Kategori = Properti | Semua listing properti (jual + sewa) |
| `/barang-dan-jasa` | Kategori = Barang & Jasa | Listing barang dan jasa saja |

### 3.4 Empty state

Kalau hasil kosong, tampilkan (reuse dari referensi):
```
[icon folder-x]
Data tidak ditemukan
Coba ubah atau hapus beberapa filter pencarian Anda.
```

---

## 4. KOMPONEN LISTING CARD

### 4.1 Card Properti

```
[Foto] [Badge: Baru / Turun Harga]  [Status: Dijual/Disewa]
Judul Properti
Rp [harga] (atau "Rp [harga]/bulan" jika sewa)
📍 [Lokasi singkat]
🛏 [jml KT]  🛁 [jml KM]  📐 [LT]m²/[LB]m²
[Lihat Detail]
```

### 4.2 Card Barang / Jasa

```
[Foto] [Badge kategori]
Judul Barang/Jasa
Rp [harga]
📍 [Lokasi singkat]
[Lihat Detail]
```

Reuse struktur card asli (image + badge + title + price + meta info + CTA button) — tinggal ganti
field data sesuai kategori.

---

## 5. HALAMAN DETAIL ITEM (Properti / Barang / Jasa)

Reuse struktur halaman detail package asli, dengan penyesuaian:

| Section asli | Diganti jadi |
|---|---|
| Gallery foto (3 foto) | Gallery foto item (6–10 foto: tampak depan, dalam, dll — khusus properti) |
| "About This Package" | "Deskripsi Properti/Barang/Jasa" |
| "Trip Itinerary" | **Properti:** Spesifikasi (LT, LB, jumlah lantai, KT, KM, carport, hadap, sertifikat, tahun bangun). **Barang:** Spesifikasi barang (kondisi, merk, tahun). **Jasa:** Cakupan layanan |
| "Included/Not Included" | Fasilitas / Catatan tambahan |
| Package Reservation + Booking Form | **Hapus form beli tiket**, ganti jadi form "Hubungi Penjual": Nama, Email, No. WhatsApp, Pesan → tombol "Kirim ke Penjual" / "Chat via WhatsApp" |
| Customer Reviews | Pertahankan sebagai review terhadap penjual/agen |
| — | **Tambah:** Peta lokasi (embed Google Maps) khusus Properti |

---

## 6. HALAMAN SIMULASI NILAI PROPERTI (`/simulasi`)

Kalkulator Simulasi KPR, field & alur sesuai rumaindo.com:

### 6.1 Input Kalkulator

- Harga Properti
- Uang Muka (DP)
- Pokok Pinjaman (auto-calculate dari Harga Properti − DP)
- Bunga Tahunan (%)
- Bunga Bulanan (%) — auto-calculate dari bunga tahunan
- Tenor Pinjaman (Tahun)
- Tombol: **"Hitung Simulasi"**

### 6.2 Output

- **Estimasi Angsuran Bulanan** — tampil besar: "Rp [nominal]"
- Info tenor: "Tenor: [x] Tahun ([y] Bulan)"
- **Tabel Amortisasi Cicilan** dengan kolom:
  `Bulan | Pokok Pinjaman | Bunga Bulanan | Pokok Bulanan | Cicilan Bulanan | Sisa Pinjaman`
- Catatan disclaimer di bawah tabel: *"Hasil simulasi ini merupakan estimasi awal/perkiraan
  kasar. Nilai suku bunga dan ketentuan cicilan real dapat berbeda-beda tergantung pada kebijakan
  bank penyedia KPR pilihan Anda serta riwayat kredit individu Anda."*

---

## 7. SISTEM AKUN & SALDO (Kuota Listing + Poin)

Ini fitur inti baru yang tidak ada di web sumber — harus dibangun dari nol.

### 7.1 Halaman "Akun Saya" (`/akun`)

Layout 2 kolom:

**Kolom kiri — kartu profil:**
- Foto profil (avatar)
- Nama lengkap + username (`@[nama]-[id]`)
- Email
- Nomor WhatsApp
- Lokasi
- Alamat
- Bergabung sejak [tanggal]
- Bio
- Menu Dashboard: `List Iklan Saya`, `Edit Profil`, `Keluar`

**Kolom kanan — dua panel kuota, masing-masing punya sub-panel Kuota Iklan & Kuota Poin:**

```
KUOTA IKLAN PROPERTI                              [Histori]
┌─────────────────────┐  ┌─────────────────────┐
│ 🖼 KUOTA IKLAN       │  │ ⭐ KUOTA POIN        │
│ Total: 0             │  │ Total: 0             │
│ Terpakai: 0           │  │ Terpakai: 0          │
│ Sisa: 0               │  │ Sisa: 0              │
└─────────────────────┘  └─────────────────────┘

KUOTA IKLAN BARANG & JASA                         [Histori]
┌─────────────────────┐  ┌─────────────────────┐
│ 🖼 KUOTA IKLAN       │  │ ⭐ KUOTA POIN        │
│ (sama struktur di atas)                        │
└─────────────────────┘  └─────────────────────┘
```

> Poin penting: kuota Properti dan kuota Barang & Jasa **terpisah** (dua pool berbeda), masing-
> masing punya 2 jenis saldo: **Kuota Iklan** (jumlah slot listing yang bisa dipasang) dan **Kuota
> Poin** (dipakai untuk fitur promosi/boost listing).

### 7.2 Aturan Bisnis: Pasang Iklan Butuh Saldo

- User **wajib punya sisa Kuota Iklan > 0** (sesuai kategori: Properti atau Barang & Jasa) untuk
  bisa submit form Pasang Iklan.
- Kalau saldo 0 / habis → redirect / munculkan prompt ke halaman **Top Up** sebelum lanjut posting.
- Setiap 1 listing yang berhasil dipasang = mengurangi 1 dari "Kuota Iklan" kategori terkait.
- Kuota Poin dipakai terpisah untuk fitur tambahan (misalnya boost/highlight listing) — bukan
  syarat wajib untuk posting biasa.

### 7.3 Halaman "Iklan Saya" (`/iklan-saya`)

- Tab kategori: **Properti | Barang | Jasa**
- Filter sidebar sama seperti listing page publik (Kata Kunci, Status Iklan, Urutkan, Jenis
  Transaksi, Lokasi/Daerah, Rentang Harga)
- Tombol "Pasang Iklan" di pojok kanan atas (shortcut ke flow poin 1.4)
- List menampilkan iklan milik user sendiri beserta status (aktif/nonaktif/kadaluarsa) dan opsi
  edit/hapus/boost
- Empty state sama seperti poin 3.4

### 7.4 Halaman "Top Up" (`/top-up`)

**Step 1 — Pilih jenis layanan iklan:**
```
┌───────────────────┐  ┌───────────────────┐
│ 🏠 Properti        │  │ 📦 Barang & Jasa   │
│ Iklan Hunian &     │  │ Iklan Perlengkapan │
│ Tanah              │  │ & Jasa             │
│ "Top up kuota      │  │ "Top up kuota      │
│  untuk mempromo-   │  │  untuk mempromo-   │
│  sikan iklan rumah,│  │  sikan material    │
│  apartemen, ruko,  │  │  bangunan, dekorasi│
│  atau properti     │  │  interior, atau    │
│  tanah Anda."       │  │  jasa konstruksi." │
└───────────────────┘  └───────────────────┘
        [Lanjutkan →]
```

**Step 2 — Pilih tipe kuota (tab: Iklan | Poin), lalu pilih paket:**

Tab **Iklan** — paket berdasar jumlah listing (contoh referensi harga rumaindo, boleh disesuaikan):

| Listing | Harga | Harga/listing | Diskon | Bonus Poin |
|---|---|---|---|---|
| 1 | Rp59.000 | Rp59.000 | — | — |
| 5 | Rp89.000 | Rp17.800 | -41% | — |
| 20 | Rp159.000 | Rp7.950 | -74% | 20 poin |
| 30 | Rp199.000 | Rp6.633 | -78% | 30 poin |
| 50 | Rp299.000 | Rp5.980 | -80% | 50 poin |
| 125 | Rp399.000 | Rp3.192 | -88% | 125 poin |
| 200 | Rp599.000 | Rp2.995 | -90% | 200 poin |
| 350 | Rp999.000 | Rp2.854 | -90% | 350 poin |
| 700 | Rp1.899.000 | — | -91% | — |
| 1000 | Rp2.799.000 | — | -91% | — |

Tab **Poin** — paket berdasar jumlah poin:

| Poin | Harga | Harga/poin | Diskon |
|---|---|---|---|
| 200 | Rp100.000 | Rp500 | -75% |
| 500 | Rp200.000 | Rp400 | -80% |
| 1000 | Rp400.000 | Rp400 | -80% |
| 2000 | Rp700.000 | Rp350 | -83% |
| 5000 | Rp1.800.000 | Rp360 | -82% |
| 10000 | Rp3.500.000 | Rp350 | -83% |

Card style: badge diskon pojok kanan atas (merah), harga besar bold, harga per unit di bawahnya,
badge bonus poin (biru, icon gift) kalau ada. Tombol CTA di bawah grid: **"Beli Kuota Sekarang"**.

### 7.5 Halaman "Transaksi" (`/transaksi`)

- Riwayat semua top up & penggunaan kuota (tabel: tanggal, jenis transaksi, jumlah, status)

### 7.6 Halaman "Iklan Favorit" (`/iklan-favorit`)

- List listing yang di-bookmark/favorit-kan user, layout grid card sama seperti listing biasa

---

## 8. FOOTER

Reuse struktur footer asli, isi 4 kolom link diganti jadi:

**Cari Properti**
- Properti Dijual → `/dijual`
- Properti Disewakan → `/disewakan`
- Properti Terbaru → `/properti`
- Pasang Iklan Gratis → `/pasang-iklan`
- Simulasi Nilai Properti → `/simulasi`

**Layanan & Fitur**
- Cari Barang & Jasa → `/barang-dan-jasa`
- Kebutuhan Barang → `/barang`
- Layanan Jasa → `/jasa`
- Co-Broke Hub → `/tentang-co-broke`
- Al-Qur'an Digital → `/quran`

**Dukungan & Legalitas**
- Tentang Kami
- Artikel Inspirasi
- Syarat & Ketentuan
- Kebijakan Privasi
- Hubungi Kami (WA)

**Unduh Aplikasi** (opsional — badge Play Store & App Store, bisa dikosongkan kalau belum ada app)

Social links: Instagram, Facebook, WhatsApp (reuse posisi dari source)

Copyright: `© [tahun] — [tahun] [Nama Web] (PT [Nama PT]). All Rights Reserved.`

---

## 9. RINGKASAN PRIORITAS BUILD

Urutan pengerjaan yang disarankan (dari yang paling beda dari source ke yang paling reusable):

1. **Sistem akun & saldo** (Akun Saya, Top Up, kuota iklan/poin) — fitur paling baru, jadi
   fondasi sebelum fitur lain jalan
2. **Flow Pasang Iklan** (pilih kategori → cek saldo → form sesuai kategori)
3. **Halaman listing publik** (Dijual/Disewakan/Properti Terbaru/Barang & Jasa) + filter
4. **Card component** (Properti vs Barang/Jasa)
5. **Halaman detail item** (rombak booking form jadi "Hubungi Penjual")
6. **Simulasi KPR**
7. **Home page** (reuse paling banyak, tinggal ganti copy & CTA)
8. **Footer & halaman statis** (Tentang Kami, Syarat & Ketentuan, dll)
9. Fitur **Co-Broke Hub**, **Al-Qur'an Digital**, **Artikel Inspirasi** — nice-to-have, bisa
   dikerjakan belakangan
