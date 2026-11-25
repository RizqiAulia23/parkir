# Sistem Informasi Parkir Perkantoran

Aplikasi web pengelolaan parkir perkantoran berbasis **Laravel 12**, **PostgreSQL**, dan **Tailwind CSS 4**. Mencakup login multi-role, pendataan kendaraan, transaksi masuk/keluar, cetak tiket & struk, log aktivitas, serta rekap pendapatan.

## Fitur

### Admin (superuser)
- CRUD pengguna (`tb_user`) dengan password di-hash menggunakan `Hash::make()`.
- CRUD tarif parkir (`tb_tarif`), area parkir (`tb_area_parkir`), dan kendaraan (`tb_kendaraan`).
- Melihat log aktivitas (`tb_log_aktivitas`) urut dari waktu terbaru.
- Akses penuh ke transaksi parkir dan laporan.

### Petugas
- Transaksi parkir masuk: pilih kendaraan terdaftar atau input kendaraan baru langsung.
- Pencegahan area penuh (`terisi >= kapasitas`) dan kendaraan yang masih parkir.
- Transaksi parkir keluar: durasi dibulatkan ke atas (minimal 1 jam), biaya dihitung otomatis.
- Cetak tiket masuk dan struk pembayaran (siap cetak via `window.print()`).
- Kolom `terisi` pada area bertambah/berkurang otomatis dalam transaksi database.

### Owner
- Rekap transaksi berdasarkan rentang tanggal.
- Pilihan basis tanggal: tanggal masuk atau tanggal keluar.
- Ringkasan total pendapatan, jumlah transaksi selesai, dan yang masih parkir.
- Cetak laporan beserta area tanda tangan.

### Umum
- Login dengan `username` + `password` dan validasi `status_aktif = 1`.
- Setiap aktivitas penting dicatat ke `tb_log_aktivitas`.
- Dashboard berbeda sesuai role.
- Tampilan responsif dengan Tailwind CSS 4.

## Stack

| Komponen | Versi |
|---|---|
| PHP | 8.3+ (diuji pada 8.5) |
| Laravel | 12.72 |
| Database | PostgreSQL |
| Frontend | Tailwind CSS 4 + Vite |

## Kebutuhan

- PHP 8.3 atau lebih baru beserta ekstensi `pdo_pgsql`
- Composer 2
- PostgreSQL
- Node.js 20+ dan npm

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Sesuaikan koneksi database pada `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=db_parkir
DB_USERNAME=postgres
DB_PASSWORD=...
APP_TIMEZONE=Asia/Jakarta
```

Pastikan database sudah memiliki tabel berikut:

- `tb_user`, `tb_tarif`, `tb_area_parkir`, `tb_kendaraan`, `tb_transaksi`, `tb_log_aktivitas`
- `sessions`, `cache`, `cache_locks`, `jobs`, `failed_jobs`, `job_batches`

> **Penting:** jangan menjalankan `php artisan migrate:fresh` atau mengubah struktur enam tabel utama. Aplikasi menggunakan skema yang sudah ada. Model diset `public $timestamps = false;` dengan `$table` dan `$primaryKey` eksplisit.

Build aset dan jalankan aplikasi:

```bash
npm install
npm run build
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Untuk pengembangan frontend, jalankan `npm run dev` di terminal terpisah.

## Akun

Tabel `tb_user` sudah berisi akun seed dengan role `admin`, `petugas`, dan `owner`. Password mengikuti data yang tersimpan di database dan tidak diubah oleh aplikasi. Akun baru hanya dapat dibuat oleh admin melalui menu **Pengguna**.

## Catatan teknis

- Middleware role didaftarkan sebagai alias `role` di `bootstrap/app.php` (tanpa `Kernel.php`).
- Login memakai guard `web` dengan kredensial `username`, `password`, dan `status_aktif = 1`.
- Penghapusan master data yang sudah dipakai transaksi akan ditolak dengan pesan yang jelas.
- Perhitungan biaya: `durasi_jam = max(1, ceil(selisih detik / 3600))`, lalu `biaya_total = durasi_jam × tarif_per_jam`.
- Transaksi masuk/keluar memakai `DB::transaction()` dan `lockForUpdate()` agar kapasitas area tetap konsisten.
- Zona waktu aplikasi: `Asia/Jakarta`.

## Struktur utama

```
app/Http/Controllers/
├── AuthController.php
├── DashboardController.php
├── Admin/            # CRUD user, tarif, area, kendaraan, log
├── Petugas/          # Transaksi masuk/keluar & struk
└── Owner/            # Laporan & cetak

app/Models/           # User, Tarif, AreaParkir, Kendaraan, Transaksi, LogAktivitas
app/Http/Middleware/  # RoleMiddleware
resources/views/      # Layout, dashboard, admin, petugas, owner, cetak
routes/web.php        # Seluruh route berdasarkan role
```
