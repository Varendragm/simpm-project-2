# SIMPM — Project 2 (Sistem Informasi Monitoring Performa Mesin)
Laravel + Supabase (PostgreSQL) · PG Rendeng

Paket ini berisi kode aplikasi (Model, Controller, Migration, Seeder, View,
CSS, JS) untuk ditimpakan ke instalasi Laravel. Sandbox pembuat kode ini
tidak punya akses ke Packagist, jadi `composer create-project` tetap harus
kamu jalankan sendiri sekali di komputer kamu.

## 1. Buat project Laravel baru

```bash
composer create-project laravel/laravel simpm-project2
cd simpm-project2
```

## 2. Timpa dengan isi paket ini

Ekstrak zip ini, lalu salin & **timpa (overwrite)** semua folder berikut ke
project Laravel yang baru dibuat — struktur foldernya sudah sama persis
supaya tinggal drag-drop / copy-paste:

```
app/            → timpa app/
config/         → timpa config/  (hanya database.php yang berubah)
database/       → timpa database/
public/css/     → timpa public/css/
public/js/      → timpa public/js/
resources/      → timpa resources/
routes/web.php  → timpa routes/web.php
.env.example    → timpa .env.example
```

## 3. Aktifkan ekstensi PHP untuk PostgreSQL

Supabase pakai PostgreSQL, bukan MySQL — pastikan ekstensi PDO Postgres
aktif.

**Kalau pakai XAMPP:** buka `php.ini` (Config → PHP di XAMPP Control
Panel), hilangkan tanda `;` di depan baris ini lalu restart Apache:
```ini
extension=pdo_pgsql
extension=pgsql
```

## 4. Daftarkan middleware `role`

Buka `bootstrap/app.php`, cari `->withMiddleware()` dan tambahkan alias:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

## 5. Buat project Supabase & ambil kredensial database

1. Buka [supabase.com](https://supabase.com) → **New Project**.
2. Setelah project dibuat, buka **Project Settings → Database**.
3. Salin kredensial di bagian **Connection parameters**:
   - Host (pakai **Session pooler** kalau tersedia, lebih stabil — lihat
     catatan di `.env.example`)
   - Port: `5432`
   - Database: `postgres`
   - User: `postgres` (atau `postgres.<project-ref>` kalau pakai pooler)
   - Password: password yang kamu set saat membuat project

## 6. Konfigurasi `.env`

Salin `.env.example` (dari paket ini) jadi `.env` di root project, lalu
isi `DB_HOST`, `DB_PASSWORD`, dst sesuai kredensial Supabase kamu.

```bash
php artisan key:generate
```

## 7. Jalankan migration & seeder

```bash
php artisan migrate
php artisan db:seed
```

Kalau berhasil, buka **Supabase Dashboard → Table Editor** — tabel
`users`, `mesin`, `damage_reports`, `maintenance_schedules`,
`maintenance_checklists`, `kpi_trends`, dan `sessions` akan muncul di sana.

> **Penting soal `damage_reports`:** tabel ini disediakan supaya SIMPM bisa
> jalan mandiri untuk demo. Begitu Project 1 (SIPPM) benar-benar digabung
> ke Supabase yang sama, migration `damage_reports` ini **di-skip** di sisi
> SIMPM — cukup pakai Model `DamageReport` untuk membaca tabel yang sudah
> dibuat Project 1 (read-only, jangan pernah insert/update dari SIMPM).

## 8. Jalankan server

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000` → splash screen → halaman login.

## 9. Akun demo (dari seeder)

Semua password: `password`

| Role       | Username         |
|------------|------------------|
| Supervisor | sri.supervisor   |
| Teknisi    | budi.teknisi     |
| Teknisi    | rudi.teknisi     |
| Manajer    | wahyu.manajer    |

## 10. Yang sudah diimplementasikan

- **Login berbasis username + role** (Supervisor / Teknisi / Manajer),
  splash screen, redirect otomatis ke dashboard masing-masing role.
- **CSS & JS terpisah** — `public/css/app.css` (semua styling + animasi)
  dan `public/js/app.js` (semua interaktivitas), tidak ada `<style>` atau
  `<script>` inline yang berantakan di Blade.
- **Grafik interaktif (Chart.js)** — tren availability, downtime per
  mesin, perbandingan OEE antar mesin, jumlah perbaikan per kategori;
  data asli dari database (Model → JSON → Chart.js), bukan SVG statis.
- **Animasi count-up** pada semua angka statistik saat halaman dimuat.
- **Progress bar & gauge** animasi (availability, OEE, checklist).
- **Checklist interaktif** — Teknisi centang item pemeriksaan, tersimpan
  otomatis via AJAX (`fetch`) tanpa reload halaman, progress bar checklist
  ikut update real-time.
- **Filter & pencarian tabel live** (client-side, tanpa reload) di
  halaman Performa Mesin, Riwayat Maintenance, dan Jadwal.
- **Sort tabel dengan klik header** (klik kolom untuk urutkan naik/turun).
- **Toast notification** untuk pesan sukses/gagal (mis. setelah simpan
  jadwal, update profil).
- **Tab mesin interaktif** di halaman Performa Manajer.
- **Role Manajer bersifat read-only** — tidak ada form input, sesuai
  mockup ("Tampilan Manajer bersifat ringkas dan hanya-baca").
- **Data fiktif lengkap** di seeder: 4 mesin Gilingan 01–04 dengan KPI
  (OEE/Availability/MTTR/MTBF), 6 laporan riwayat SIPPM, 4 jadwal PM,
  checklist, dan seri data untuk semua grafik (6 minggu/6 bulan).

## 11. Yang belum ada (lanjutan pengembangan)

- Export PDF/Excel di halaman Laporan (tombolnya sudah ada, aksinya belum).
- Validasi form lebih ketat (tanggal, format nomor HP, dsb).
- Notifikasi in-app/email saat jadwal jatuh tempo.
- Halaman lupa password.
