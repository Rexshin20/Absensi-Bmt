# Absensi BMT Cinde Barokah

Sistem absensi karyawan BMT Cinde Barokah. Proyek ini menyediakan panel admin berbasis Laravel untuk mengelola data karyawan dan memantau absensi, serta REST API yang dapat digunakan aplikasi mobile.

## Fitur

- Dashboard ringkasan kehadiran harian dan grafik absensi mingguan.
- Pencatatan dan rekap absensi berdasarkan tanggal, termasuk status hadir/terlambat, lokasi, jarak, dan sumber absensi.
- Detail absensi dengan foto selfie dan koordinat GPS.
- Pengelolaan karyawan, akun, divisi, jabatan, dan lokasi kantor.
- Pengelolaan pengajuan izin, jenis izin, hari kerja, dan hari libur.
- REST API dengan autentikasi token Laravel Sanctum untuk aplikasi mobile.
- Ekspor tabel absensi.

## Teknologi

- PHP 8.1 atau lebih baru
- Laravel 10
- MySQL atau MariaDB
- Composer
- Node.js dan npm (untuk pekerjaan pada aset Vite)

## Menjalankan di komputer lokal

1. Clone repository dan masuk ke direktori proyek:

   ```bash
   git clone https://github.com/Rexshin20/Absensi-Bmt.git
   cd Absensi-Bmt
   ```

2. Pasang dependency PHP dan siapkan file konfigurasi:

   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   ```

   Di Windows Command Prompt, salin `.env.example` sebagai `.env` dengan:

   ```bat
   copy .env.example .env
   ```

3. Buat database MySQL/MariaDB, lalu sesuaikan pengaturan berikut di `.env`:

   ```dotenv
   APP_NAME="Absensi BMT"
   APP_URL=http://127.0.0.1:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=absensi_bmt
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. Jalankan migrasi, data awal admin, dan tautan penyimpanan foto:

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

5. Jalankan aplikasi:

   ```bash
   php artisan serve
   ```

   Buka alamat yang ditampilkan Artisan, biasanya `http://127.0.0.1:8000`.

### Akun admin lokal

Seeder menyediakan akun untuk pengembangan lokal:

- Email: `admin@absensibmt.test`
- Username data akun: `johndoe`
- Password: `password`

Masuk ke panel admin menggunakan email dan password tersebut. Ganti password atau hapus akun contoh sebelum aplikasi digunakan di lingkungan bersama atau produksi. Jangan memakai kredensial bawaan ini untuk deployment.

## Aplikasi mobile dan API

API menggunakan prefiks `/api`. Endpoint yang tersedia:

| Method | Endpoint | Keterangan |
| --- | --- | --- |
| `POST` | `/api/login` | Login dan memperoleh token |
| `GET` | `/api/me` | Informasi akun yang sedang login |
| `POST` | `/api/logout` | Menghapus token aktif |
| `POST` | `/api/attendance` | Mencatat absensi dengan lokasi dan foto |
| `GET` | `/api/attendance?month=YYYY-MM` | Riwayat dan ringkasan absensi bulanan |
| `GET` | `/api/leave-types` | Daftar jenis izin aktif |
| `GET` | `/api/leaves` | Daftar pengajuan izin |
| `POST` | `/api/leaves` | Mengirim pengajuan izin |

Selain login, endpoint API memerlukan token Sanctum melalui header:

```http
Authorization: Bearer {token}
Accept: application/json
```

Untuk memakai API dari perangkat atau jaringan lain, atur `APP_URL` ke alamat server yang dapat dijangkau perangkat. Akses lokasi pada panel web memerlukan browser yang mendukung geolokasi dan, selain `localhost`, umumnya harus disajikan melalui HTTPS.

Foto absensi disimpan pada disk `public` di `storage/app/public`. Pastikan `php artisan storage:link` sudah dijalankan dan direktori penyimpanan dapat ditulis oleh proses aplikasi.

## Aset frontend

Panel admin menggunakan stylesheet dan JavaScript template yang tersedia di `public/assets`. Dependency Vite disediakan untuk pengembangan aset di `resources`:

```bash
npm install
npm run dev
```

Untuk membuat bundle Vite:

```bash
npm run build
```

## Pengujian

Jalankan pengujian Laravel dengan:

```bash
php artisan test
```

Konfigurasikan database pengujian sesuai lingkungan. Jika menggunakan SQLite in-memory, pastikan ekstensi PDO SQLite tersedia pada instalasi PHP.
