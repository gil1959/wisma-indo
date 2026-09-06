# Wisma Indo API Documentation

**Base URL:** `http://localhost:8000/api/v1` (atau sesuaikan dengan URL server Anda)

Semua endpoint yang memerlukan autentikasi harus menyertakan header berikut:
```http
Authorization: Bearer {token_anda}
Accept: application/json
```

---

## 1. Authentication

### Register
- **Endpoint:** `POST /register`
- **Body:** `name`, `email`, `password`, `password_confirmation`, `phone`
- **Response:** User data + `access_token`

### Login
- **Endpoint:** `POST /login`
- **Body:** `email`, `password`
- **Response:** User data + `access_token`

### Logout (Auth Required)
- **Endpoint:** `POST /logout`
- **Response:** Success message

### Get Current User (Auth Required)
- **Endpoint:** `GET /me`
- **Response:** User details

---

## 2. Public API (No Auth Required)

### Home
- **Endpoint:** `GET /home`
- **Response:**
  - `banners`: Daftar banner iklan (image URL bersifat absolut).
  - `locations`: Daftar lokasi unggulan (image URL bersifat absolut).
  - `testimonials`: Daftar testimoni full (avatar URL bersifat absolut).
  - `bank_partners`: Daftar partner bank/pembayaran (logo URL bersifat absolut).
  - `categories`: Daftar kategori penuh (photo URL bersifat absolut), dikelompokkan menjadi:
    - `property`: Kategori properti
    - `goods`: Kategori barang
    - `services`: Kategori jasa
  - `settings`: Objek berisi info kontak Admin/CS dari panel admin (termasuk `footer_whatsapp`, `footer_phone`, `footer_email`, `brand_name`).

### Listings (Iklan)
- **Endpoint:** `GET /listings`
  - *Query Params:* 
    - `search`: Filter pencarian berdasarkan kata kunci (judul iklan)
    - `transaction_type`: `dijual` / `disewakan`
    - `category_slug`: Filter berdasarkan Tipe Properti (slug kategori)
    - `min_price`: Filter harga minimum (angka)
    - `max_price`: Filter harga maksimum (angka)
    - `type`: `property` / `goods` / `services` / `barang_jasa` (gabungan)
    - **Geolocation Search:**
      - `latitude`: Koordinat lintang pengguna (contoh: -6.200000)
      - `longitude`: Koordinat bujur pengguna (contoh: 106.816666)
      - `radius`: Radius pencarian dalam satuan Kilometer (contoh: 10)
  - *Response JSON (Object `data` berupa array dari Listing Object)*: 
    - **Info Dasar:** `id`, `title`, `slug`, `type`, `transaction_type` (dijual/disewa), `price`, `price_formatted` (string misal: Rp 30.000.000), `negotiable` (boolean - untuk badge "Bisa Nego"), `description`.
    - **Gambar:** `cover_image` (string url) dan array `images` (berisi ID, url gambar lengkap, dan status is_primary).
    - **Spesifikasi Properti:** `land_area` (Luas Tanah), `building_area` (Luas Bangunan), `bedrooms` (K. Tidur), `bathrooms` (K. Mandi), `floors` (Jml Lantai), `build_year` (Thn Dibangun), `certificate` (Sertifikat), `imb` (boolean), `pbb` (boolean), `furnished_status` (Perabotan), `carport`, `garage`.
    - **Fasilitas & Area Sekitar:** `facilities` dan `surroundings` (berformat array json).
    - **Kontak & Multimedia:** `phone`, `whatsapp` (tombol kontak), `youtube_url` (Video), `latitude`, `longitude`, `maps_url` (Lokasi Peta).
    - **Barang/Jasa:** `brand`, `service_area`, `condition`.
    - **Statistik & Status:** `views` (jumlah dilihat), `status` (tersedia/terjual/dll), `is_premium`, `is_sundul`.
    - **Kategori:** Object `category` berisi relasi dari tabel kategori (nama, ikon, dll).
    - **Profil Penjual (User):** Object `user` berisi detail pemasang iklan komplit termasuk `avatar` (foto profil), `name`, dan `created_at` (untuk tanggal "Bergabung sejak").

- **Endpoint:** `GET /listings/{slug}`
  - *Response JSON:* 
    - Object `data`: Berisi 1 (satu) Listing Object tunggal yang field-nya persis sama detailnya dengan yang dijelaskan di atas.
    - Array `similar_listings`: Berisi maksimal 4 Listing Object lain (berkategori sama) yang digunakan untuk section **"Mungkin Anda juga tertarik / Iklan Lainnya"**.
    - Contoh response root: `{ "success": true, "data": { ... }, "similar_listings": [ ... ] }`

- **Endpoint:** `GET /listing-categories`

### Articles (Artikel)
- **Endpoint:** `GET /articles`
  - *Query Params:* `category_slug`, `search`
  - *Response JSON (Object `data` berupa array dari Article Object)*:
    - **Info Dasar:** `id`, `title`, `slug`, `content`, `excerpt` (ringkasan, nullable), `views` (jumlah pembaca).
    - **Gambar:** `image` (berupa String URL gambar absolut siap pakai, misal: http://.../storage/gambar.jpg).
    - **Kategori:** Object `category` berisi relasi `id`, `name`, dan `slug` dari kategori artikel tersebut.
    - **Timestamps:** `created_at`, `updated_at`.
- **Endpoint:** `GET /articles/{slug}`
  - *Response JSON:*
    - Object `data`: Berisi 1 (satu) Article Object tunggal yang field-nya persis sama detailnya dengan yang dijelaskan di atas.
- **Endpoint:** `GET /article-categories`

### Pages (Halaman Statis)
- **Endpoint:** `GET /pages/{slug}`

### Simulator
- **Endpoint:** `GET /simulator/kpr`
  - *Query Params:* `property_price`, `down_payment`, `interest_rate`, `tenor_years`
- **Endpoint:** `GET /simulator/kemampuan`
  - *Query Params:* `monthly_income`, `other_installments`, `interest_rate`, `tenor_years`

---

## 3. User API (Auth Required)

### Profile (Profil)
- **Endpoint:** `GET /user/profile`
  - *Response JSON (Object `data`):* Berisi info user (`id`, `name`, `email`, `phone`, `avatar`, `bio`, `address`, `full_address`, `sub_district`, `whatsapp_template`, `created_at` (Bergabung sejak)).
  - **Baru:** Terdapat object `quota` di dalam `data` yang dihitung secara **real-time** setiap kali endpoint dipanggil dengan field:
    - `total_bought`: Total kuota yang dibeli (didapat otomatis dari hasil penjumlahan semua riwayat topup user yang berstatus `success`).
    - `used`: Kuota terpakai (jumlah iklan user yang aktif/ada saat ini).
    - `remaining`: Sisa Kuota murni yang saat ini bisa digunakan.
- **Endpoint:** `PUT /user/profile`
  - *Body:* `name`, `email`, `phone`, `password`, `password_confirmation`, `address`, `full_address`, `sub_district`, `bio`, `whatsapp_template`
- **Endpoint:** `POST /user/profile/avatar`
  - *Body (Multipart):* `avatar` (image file)

### Favorites (Iklan Favorit)
- **Endpoint:** `GET /user/favorites`
- **Endpoint:** `POST /user/favorites/{listing_id}`
- **Endpoint:** `DELETE /user/favorites/{listing_id}`

### User Listings (Manajemen Iklan Saya)
- **Endpoint:** `GET /user/listings`
  - *Response JSON:* Sama dengan Public Listings, mengembalikan daftar iklan (mendukung paginasi).
- **Endpoint:** `POST /user/listings`
  - *Body:* Field yang harus dikirim bergantung pada Tipe Kategori (`type`):
    - **Global (Semua Tipe):** `listing_category_id`, `title`, `description`, `price`, `cover_image` (Multipart).
    - **Geolokasi (Opsional, berlaku untuk semua):** `latitude`, `longitude`, `maps_url` (otomatis akan memunculkan map).
    - **Tambahan Properti (`property`):** `transaction_type` (dijual/disewa), `property_type`, `bedrooms`, `bathrooms`, `land_area`, `building_area`, `floors`, `certificate`, `imb`, `pbb`, `electricity`, `car_access`, `water_source`, `facing_direction`, `build_year`, `carport`, `garage`, `furnished_status`, `facilities` (array), `surroundings` (array), `co_broke`, `negotiable`.
    - **Tambahan Barang (`goods`):** `brand`, `condition`.
    - **Tambahan Jasa (`services`):** `service_area`.
- **Endpoint:** `POST /user/generate-ai` (AI Deskripsi Generator)
  - *Body JSON:* `type` (misal: "listing"), `title`, `category`, `price`, `transaction_type`, `location`, ditambah field opsional properti (seperti `bedrooms`, `facilities` dll).
  - *Response:* Mengembalikan konten deskripsi super lengkap berformat HTML yang digenerate otomatis oleh Google Gemini. Cocok dipakai untuk mengisi field `description` secara instan.
- **Endpoint:** `GET /user/listings/{id}`
- **Endpoint:** `PUT /user/listings/{id}`
  - *Body:* Sama seperti POST, untuk update iklan.
- **Endpoint:** `DELETE /user/listings/{id}`

### Listing Promotions (Promosi Iklan / Sundul / Premium)

#### 3.1 Get Promotion Packages
- **Endpoint:** `GET /user/listings/{id}/promotions/packages`
- **Method**: `GET`
- **Auth Required**: Yes (Sanctum)
- **Response**:
  ```json
  {
      "success": true,
      "data": {
          "listing": { /* listing object */ },
          "packages": [
              {
                  "id": 1,
                  "name": "Sundul Sehari",
                  "type": "sundul",
                  "price": "10000.00",
                  "duration_days": 1
              }
          ],
          "offline_payment_methods": [
              { "id": 1, "bank_name": "BCA", "account_number": "123" }
          ],
          "pg_channels": [
              {
                  "code": "BRIVA",
                  "name": "BRI Virtual Account",
                  "type": "Bank Transfer",
                  "provider": "tripay"
              }
          ]
      }
  }
  ```

#### 3.2 Checkout Promotion Package
- **Endpoint:** `POST /user/listings/{id}/promotions/checkout/{package_id}`
- **Method**: `POST`
- **Auth Required**: Yes
- **Body**:
  ```json
  {
      "payment_method": "pg|BRIVA" // Format: "offline|{id_metode}" atau "pg|{kode_channel}"
  }
  ```
- **Response (Payment Gateway)**:
  ```json
  {
      "success": true,
      "message": "Checkout online initialized.",
      "data": {
          "id": 12,
          "payment_reference": "PROMO-12-16982736",
          "payment_url": "https://tripay.co.id/checkout/...",
          "status": "pending"
      }
  }
  ```

#### 3.3 Upload Payment Proof (For Offline Methods)
- **Endpoint:** `POST /user/listing-promotions/{transaction_id}/upload-proof`
- **Method**: `POST` (Multipart/form-data)
- **Auth Required**: Yes
- **Body**:
  - `payment_proof`: File image (JPG, PNG) max 10MB
- **Response**:
  ```json
  {
      "success": true,
      "message": "Payment proof uploaded successfully. Waiting for admin confirmation."
  }
  ```

#### 3.4 Riwayat Transaksi Promosi Iklan
- **Endpoint:** `GET /user/listing-promotions/transactions`
- **Method**: `GET`
- **Auth Required**: Yes
- **Response**: Mengembalikan daftar semua transaksi promosi iklan (Sundul/Premium) user, lengkap beserta detail objek iklannya (`listing`) dan paketnya (`listingPackage`). Data mendukung paginasi.
  ```json
  {
      "success": true,
      "data": {
          "current_page": 1,
          "data": [
              {
                  "id": 1,
                  "amount": "10000.00",
                  "payment_method": "offline",
                  "status": "pending",
                  "created_at": "2024-01-01T10:00:00.000000Z",
                  "listing": {
                      "id": 5,
                      "title": "Rumah Murah Bintaro",
                      "slug": "rumah-murah-bintaro",
                      "cover_image": "images/rumah1.jpg"
                  },
                  "listing_package": {
                      "id": 1,
                      "name": "Sundul Sehari",
                      "type": "sundul"
                  }
              }
          ]
      }
  }
  ```

#### 3.5 Panduan Alur Pembayaran (Workflow)

**A. Alur Pembayaran Offline (Transfer Bank Manual)**
1. **User Memilih Paket & Bank**: User memanggil API `GET /packages` dan memilih paket beserta metode `offline` (misalnya bank BCA).
2. **Checkout**: Aplikasi Android memanggil API `POST /checkout` dengan `payment_method: "offline|1"`.
3. **Response Checkout**: Server mereturn data transaksi dengan `status: "pending"`. Aplikasi Android lalu menampilkan **Instruksi Transfer** ke rekening admin.
4. **Upload Bukti**: Setelah user transfer uang, user mengambil foto struk dan memanggil API `POST /upload-proof`. Status transaksi masih `pending`.
5. **Moderasi Admin**: Admin mengecek bukti bayar di Dashboard Web. 
   - Jika admin menekan **Setuju (Approve)**, status transaksi menjadi `success`, dan fitur Promosi/Premium langsung aktif.
   - Jika admin menekan **Tolak (Reject)**, status transaksi menjadi `failed`.
6. **Pengecekan Status oleh User**: Aplikasi Android dapat mengecek status persetujuan ini melalui API Riwayat Transaksi atau melihat langsung perubahan tampilan iklannya.

**B. Alur Pembayaran Online (Payment Gateway Tripay / Xendit)**
1. **User Memilih PG**: Aplikasi memanggil `GET /packages`. User memilih paket dan metode PG (contoh: Alfamart atau Virtual Account).
2. **Checkout**: Aplikasi Android memanggil `POST /checkout` dengan `payment_method: "pg|BRIVA"`.
3. **Mendapatkan Link Bayar**: Server membuatkan tagihan *live* ke Tripay/Xendit dan mereturn response berisi `payment_url`. Status `pending`.
4. **User Membayar**: Aplikasi Android membuka `payment_url` di WebView. User membayar sesuai tagihan di halaman tersebut (tidak perlu upload bukti transfer).
5. **Konfirmasi Otomatis (Webhook)**: Sesaat setelah user membayar, Tripay/Xendit langsung menembak API Webhook server kita secara otomatis di belakang layar.
6. **Sukses Otomatis**: Server otomatis mengupdate transaksi menjadi `success` dan mengaktifkan fitur promosi/premium tanpa campur tangan Admin.

#### 3.6 Efek Promosi pada Iklan
Setelah pembayaran dinyatakan sukses (baik oleh Admin di mode Offline, maupun otomatis oleh Webhook di mode PG), ini yang terjadi pada data Iklan Anda di *database*:

- **Jika Membeli Paket Premium**:
  - Properti `is_premium` pada iklan tersebut akan berubah menjadi `true`.
  - Iklan Premium biasanya akan mendapatkan penanda (badge) khusus di aplikasi, diletakkan di *section* paling atas atau disorot dengan desain kartu (card) yang lebih eksklusif dibandingkan iklan gratisan.
- **Jika Membeli Paket Sundul**:
  - Kolom `bump_count` akan bertambah (misalnya jika beli paket sundul yang berisi kuota sundul lebih dari 1).
  - Kolom `bumped_at` (waktu sundul) akan di-*update* ke waktu saat ini (`now()`).
  - Karena algoritma API pencarian mengurutkan iklan berdasarkan waktu ter-update/tersundul (`bumped_at`), iklan ini akan langsung "meloncat" kembali ke halaman paling depan dan urutan paling atas di hasil pencarian reguler.

### Topup Saldo & Kuota
- **Endpoint:** `GET /user/topup-packages`
  - *Response JSON (Object `data`):*
    - `packages`: Daftar paket kuota reguler (`is_voucher` = false).
    - `voucher_packages`: Daftar paket spesial/voucher (`is_voucher` = true).
    - *(Detail atribut per objek paket dari kedua daftar di atas)*:
      - `id`: ID paket
      - `amount`: Jumlah kuota listing yang didapat
      - `price`: Harga paket
      - `bonus`: Tambahan bonus iklan (opsional)
      - `discount_label`: Label diskon (contoh: "Hemat 10%")
      - `original_price`: Harga coret / harga asli (bisa null)
      - `button_text`: Teks pada tombol beli
      - `benefits`: Daftar keuntungan / benefit paket (berupa array string)
      - `valid_until`: Batas waktu voucher (bisa null). *Catatan: Paket/voucher yang tanggal berlakunya sudah lewat tidak akan dimunculkan di API ini secara otomatis.*
    - `offline_payment_methods`: Daftar metode bank transfer manual.
    - `pg_channels`: Daftar metode pembayaran online otomatis (Tripay / Xendit).
- **Endpoint:** `POST /user/topup/checkout/{package_id}`
  - *Body:* `payment_method` (Gunakan format: `"offline|{id_metode_offline}"` ATAU `"pg|{kode_channel_pg}"`).
  - *Response:* Object transaksi lengkap. 
    - **Jika `offline`:** Akan otomatis ada `unique_code` (kode unik acak 3 digit) dan `total_amount` (harga asli + kode unik). Tersedia juga `total_amount_formatted` (contoh: "Rp 150.123") yang langsung siap ditampilkan di layar instruksi transfer aplikasi.
    - **Jika `pg` (online):** Akan langsung mereturn `payment_url` (link tagihan Tripay/Xendit) untuk langsung Anda muat di dalam WebView.
- **Endpoint:** `POST /user/topup/upload-proof/{transaction_id}`
  - *Body (Multipart):* `payment_proof` (image file)
  - *Response:* Bukti transfer akan disimpan dan status transaksi akan dipertahankan sebagai `"pending"`. Admin kemudian akan melihat bukti tersebut di Panel Web dan meng-*approve* transaksi menjadi `"success"`.
- **Endpoint:** `GET /user/transactions` (Riwayat Transaksi Topup)
  - *Response JSON:* Daftar riwayat transaksi user. Mendukung paginasi.
  - *Detail Object `data`:*
    - `quota_amount`: Jumlah kuota yang dibeli (contoh: 10).
    - `amount` & `amount_formatted`: Harga dasar paket dalam Rupiah (contoh: 150000 / "Rp 150.000").
    - `total_amount` & `total_amount_formatted`: Harga total yang harus dibayar termasuk kode unik (jika offline).
    - `payment_method`, `status`, dan relasi `package` (nama paket yang dibeli).

### Notifications (Notifikasi)
- **Endpoint:** `GET /user/notifications`
- **Endpoint:** `POST /user/notifications/{id}/read`

---
*Catatan: Dokumentasi ini dihasilkan secara otomatis dan memuat rangkuman endpoint utama yang tersedia pada aplikasi Wisma Indo.*
