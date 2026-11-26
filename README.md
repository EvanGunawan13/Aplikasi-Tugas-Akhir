# Mandiri POS - CV Mandiri Diesel

Sistem Informasi Penjualan (POS) dan Pengelolaan Persediaan Barang Berdasarkan Lokasi Rak Gudang untuk **CV Mandiri Diesel** (perdagangan suku cadang alat berat: oli, baut, filter, seal, gasket, hose hidrolik, bearing, sparepart engine).

---

## 1. Persyaratan Sistem & Tech Stack
- **PHP**: ^8.2 (dengan ekstensi `pdo_mysql`, `gd`, `zip`, `mbstring`, `fileinfo`)
- **Web Framework**: Laravel 11
- **Database**: MySQL 8 / MariaDB 10.4+ (`utf8mb4_unicode_ci`)
- **Frontend**: Blade + Tailwind CSS + Alpine.js (Vite)
- **PDF Engine**: `barryvdh/laravel-dompdf`
- **Charts & Icons**: Chart.js & Lucide / Heroicons

---

## 2. Langkah Instalasi & Menjalankan Aplikasi

1. **Clone & Masuk ke Direktori Proyek**:
   ```bash
   cd c:\Tugas Akhir\Aplikasi-Tugas-Akhir
   ```

2. **Instalasi Dependensi PHP & Node**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Lingkungan (`.env`)**:
   Salin `.env.example` ke `.env` dan pastikan konfigurasi database sesuai:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mandiri_pos
   DB_USERNAME=root
   DB_PASSWORD=
   DB_COLLATION=utf8mb4_unicode_ci
   ```

4. **Migrasi Database & Seeding Data Realistis**:
   ```bash
   php artisan migrate --seed
   ```

5. **Generate Storage Symlink (Foto Produk)**:
   ```bash
   php artisan storage:link
   ```

6. **Kompilasi Aset Frontend (Vite)**:
   ```bash
   npm run build
   ```

7. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Buka browser di: [http://localhost:8000](http://localhost:8000)

8. **Menjalankan Pengujian (Automated Feature Tests)**:
   ```bash
   php artisan test
   ```

---

## 3. Akun Demo Berdasarkan Peran (Role)

Semua akun demo menggunakan kata sandi: `password`

| Role | Username | Email | Hak Akses Utama |
|---|---|---|---|
| **Pemilik** | `pemilik` | `pemilik@mandiridiesel.com` | Dashboard omset/stok, Kelola Akun Pengguna (CRUD user & role), Rekapitulasi Transaksi 3 Arus + Cetak PDF Resmi A4 Landscape, Cari Barang & Galeri Foto. |
| **Admin** | `admin` | `admin@mandiridiesel.com` | Master Pemasok & Rekanan, Input Pembelian (validasi fisik), Input Barang Keluar Rekanan B2B (sinkronisasi Accurate), Penjualan POS, Void Transaksi, Rekapitulasi Transaksi + Cetak PDF. |
| **Petugas Gudang** | `gudang` | `gudang@mandiridiesel.com` | Master Rak Gudang (CRUD + Zona), Master Suku Cadang (multi-foto & katalog), Penempatan Barang ke Rak & Tampilan visual Isi Rak, Cek Stok Real-Time (Aman/Menipis/Habis), Koreksi Stok Fisik (wajib alasan + riwayat). |
| **Kasir** | `kasir` | `kasir@mandiridiesel.com` | Kasir POS Cepat 2 Kolom, Shortcut Keyboard (`F2` Cari, `Enter` Masuk Keranjang, `F9` Bayar, `Esc` Batal), Keranjang Dinamis, Biaya Pengantaran Opsional, Cetak Nota Thermal 80mm & PDF, Riwayat Penjualan. |

---

## 4. Daftar Skema Database (19 Tabel) & Relasinya

1. **`users`**: Tabel kredensial pengguna (kolom `username` unik, `role`: pemilik, admin, petugas_gudang, kasir, `is_active`).
2. **`categories`**: Kategori suku cadang alat berat (8 kategori: Pelumas & Oli, Baut & Fastener, Filter, Seal Kit, Gasket, Hose, Bearing, Engine).
3. **`units`**: Satuan barang (Pcs, Liter, Dus, Set, Meter, Pail).
4. **`products`**: Master barang suku cadang (kode unik, part number, nama, harga beli, harga jual, stok sumber kebenaran, min stock alert, soft deletes). Relasi `belongsTo` Category dan Unit.
5. **`product_photos`**: Foto katalog beresolusi tinggi (path gambar, status foto utama, relasi `belongsTo` Product).
6. **`racks`**: Lokasi rak fisik gudang (kode unik misal A-01-1, nama, zona area, deskripsi).
7. **`product_racks`**: Pemetaan many-to-many produk ke rak penyimpanan (dengan penanda `is_primary` dan catatan letak).
8. **`stock_movements`**: Kartu stok mutasi audit trail (tipe: `sale`, `purchase`, `partner_out`, `adjustment`, `void`, perubahan qty, stok sebelum, stok sesudah, polymorphic reference).
9. **`stock_adjustments`**: Riwayat koreksi stok fisik opname (stok awal, stok akhir, selisih, alasan wajib, user pengoreksi).
10. **`suppliers`**: Master vendor/pemasok suku cadang resmi (kontak person, telepon, email, alamat).
11. **`partner_companies`**: Master perusahaan rekanan tambang/konstruksi B2B.
12. **`purchases`**: Faktur pembelian masuk dari pemasok (nomor PO unik, relasi supplier, surat jalan vendor, validasi fisik petugas gudang).
13. **`purchase_items`**: Rincian item barang masuk pembelian beserta kuantitas dan harga beli.
14. **`sales`**: Transaksi penjualan kasir POS eceran (format invoice `INV-YYYYMMDD-0001`, subtotal, biaya pengantaran, grand total, metode pembayaran, status completed/void, idempotency key).
15. **`sale_items`**: Snapshot item barang terjual (kode snapshot, nama snapshot, qty, harga, subtotal).
16. **`partner_outputs`**: Pengeluaran fisik barang ke rekanan B2B tanpa nilai akuntansi (untuk sinkronisasi ke Accurate) beserta biaya antar opsional.
17. **`partner_output_items`**: Kuantitas fisik barang yang diserahkan ke rekanan.
18. **`activity_logs`**: Log audit trail aksi pengguna (user_id, aksi, modul, deskripsi, alamat IP).
19. **`settings`**: Konfigurasi identitas toko CV Mandiri Diesel, alamat, telepon, footer nota struk thermal, dan header laporan.

---

## 5. Ringkasan Fitur Unggulan

- **Keandalan Mutasi Stok**: Seluruh pembaruan stok dilindungi `DB::transaction()` dan `lockForUpdate()` pada row produk sehingga kebal dari race condition saat digunakan kasir dan admin secara bersamaan.
- **Pencegahan Double-Submit POS**: Menggunakan `idempotency_key` unik per sesi checkout form dan proteksi otomatis tombol submit Alpine.js.
- **Dukungan Pencocokan Accurate**: Modul B2B Rekanan dirancang mencatat pergerakan fisik barang tanpa nilai akuntansi (nilai ada di Accurate), menyediakan referensi nomor Surat Jalan yang akurat.
- **Cetak Nota & Laporan PDF**: 
  - Nota POS mendukung mode Struk Thermal 80 mm (`@media print`) dan ekspor PDF langsung.
  - Rekapitulasi transaksi 3 arus menyediakan ekspor Dokumen Resmi PDF A4 Landscape (`barryvdh/laravel-dompdf`) lengkap dengan kop surat dan kolom tanda tangan.
