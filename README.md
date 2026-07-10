# Rumaindo - Portal Properti Terpercaya

Rumaindo adalah platform *marketplace* properti inovatif yang dirancang untuk mempertemukan pencari properti dengan pemilik, agen, dan penyedia jasa/barang kebutuhan properti di seluruh Indonesia.

## Fitur Utama

- **Cari Properti (Jual/Sewa):** Temukan hunian idaman seperti rumah, apartemen, tanah, ruko, hingga indekos dengan filter pencarian yang akurat.
- **Kategori Barang & Jasa:** Tidak hanya properti, pengguna juga dapat mengiklankan dan mencari barang atau jasa yang berkaitan dengan kebutuhan rumah tangga dan konstruksi.
- **Simulasi Nilai Properti (KPR):** Kalkulator bawaan untuk membantu pengguna mengestimasi cicilan KPR atau nilai investasi properti mereka.
- **Sistem *Co-Broke* (Agen):** Hub eksklusif bagi agen properti untuk berkolaborasi dan berbagi komisi (*co-broking*) antar sesama agen secara transparan.
- **Manajemen Iklan & Kuota Top-Up:** Pengguna dapat mendaftar, membeli kuota iklan (*top-up* poin), dan memantau performa iklan secara mandiri melalui *Dashboard* khusus pengguna.

## Teknologi yang Digunakan

Aplikasi ini dibangun menggunakan tumpukan teknologi modern untuk menjamin kecepatan, keamanan, dan pengalaman pengguna yang luar biasa:
- **Backend:** Laravel (PHP 8.x)
- **Frontend:** Blade Templating Engine, Tailwind CSS (Utility-first framework), dan Alpine.js (Lightweight Javascript behavior)
- **Database:** MySQL
- **Assets Pipeline:** Laravel Mix (NPM/Webpack)

## Instalasi Lokal (Development)

Untuk menjalankan proyek ini secara lokal, ikuti langkah-langkah berikut:

1. **Klon Repositori:**
   ```bash
   git clone https://github.com/gil1959/wisma-indo.git
   cd wisma-indo
   ```

2. **Install Dependencies:**
   ```bash
   composer install
   npm install && npm run prod
   ```

3. **Pengaturan Lingkungan (Environment):**
   Salin `.env.example` menjadi `.env` lalu sesuaikan kredensial database Anda.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrasi Database & Seeder:**
   Jalankan migrasi untuk membuat tabel beserta data otentikasi awal (Spatie Roles).
   ```bash
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Server Lokal:**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui browser di `http://127.0.0.1:8000`.

---
*Dikembangkan secara otomatis sebagai proyek konversi dari sistem Travel menuju Marketplace Properti.*
