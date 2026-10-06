# SIM-RSUD

Sistem berbasis CodeIgniter 4 untuk mengelola modul Pelatihan, Pendidikan,
Riset, dan administrasi Super Admin.

## Persyaratan

- PHP 8.2 atau lebih tinggi
- Composer
- MySQL
- XAMPP (Apache dan MySQL) untuk menjalankan secara lokal
- Ekstensi PHP: `intl`, `mbstring`, `mysqli`, `curl`, dan `gd`
- Ekstensi `sqlite3` untuk menjalankan pengujian dengan database sementara

## Menjalankan Project dari Awal

Contoh berikut menggunakan XAMPP pada Windows.

### 1. Aktifkan Apache dan MySQL

Aktifkan Apache dan MySQL melalui XAMPP Control Panel.

### 2. Siapkan project dan konfigurasi lokal

Jika belum memiliki project, clone repository lalu masuk ke foldernya:

```powershell
git clone https://github.com/JooshuaAnd/SIM-RSUD-.git
cd SIM-RSUD-
```

Salin contoh konfigurasi menjadi file lokal:

```powershell
Copy-Item .env.example .env
```

Jika `.env` sudah ada, jangan ditimpa; sesuaikan konfigurasi yang diperlukan.

### 3. Buat database

Buat database dengan nama `sim_diklat` melalui phpMyAdmin atau MySQL:

```sql
CREATE DATABASE sim_diklat;
```

Pastikan konfigurasi `.env` sesuai:

```ini
database.default.hostname = 127.0.0.1
database.default.database = sim_diklat
database.default.username = root
database.default.password = YOUR_DB_PASSWORD
database.default.DBDriver = MySQLi
database.default.port = 3306

app.baseURL = 'http://localhost:8080/'
app.indexPage = ''
```

### 4. Install dependency

Buka terminal pada folder project:

```powershell
composer install
```

### 5. Buat tabel database

Pada database baru yang masih kosong, jalankan migration:

```powershell
php spark migrate --all
```

### 6. Jalankan seeder pada database development baru

Gunakan seeder utama berikut:

```powershell
php spark db:seed "App\Database\Seeds\Pelatihan\DataAwalPelatihanSeeder"
```

Seeder ini memanggil seluruh seeder data Pelatihan, Pendidikan, dan Riset,
termasuk role serta akun demo. Jangan menjalankan semua file seeder satu per
satu karena beberapa seeder melakukan `truncate`. Seeder utama juga
mengosongkan sejumlah tabel: jangan jalankan pada database yang berisi data
operasional. Ganti password akun demo sebelum aplikasi digunakan bersama.

### 7. Jalankan aplikasi

```powershell
php spark serve
```

Buka [http://localhost:8080](http://localhost:8080).

> **Peringatan:** `php spark migrate:refresh` menghapus data lama. Gunakan
> hanya pada database development atau setelah membuat backup.

## Memperbarui Instalasi yang Sudah Berjalan

Backup database dan seluruh file upload terlebih dahulu. Setelah mengambil
perubahan kode, jalankan:

```powershell
composer install
php spark migrate --all
```

Jangan menjalankan `migrate:refresh` atau seeder utama untuk pembaruan biasa.
Migration `AddRevisiDikirimAtToInstitusiPendidikan` menambahkan kolom penanda
pengiriman revisi yang diperlukan oleh alur verifikasi institusi.

## Pengujian

Jalankan dari folder project dengan ekstensi `sqlite3` aktif:

```powershell
php vendor/bin/phpunit tests --no-coverage --no-logging --do-not-cache-result
```

Jika `sqlite3` tersedia di XAMPP tetapi belum aktif di konfigurasi PHP,
gunakan perintah berikut tanpa mengubah `php.ini`:

```powershell
php -d extension=sqlite3 vendor/bin/phpunit tests --no-coverage --no-logging --do-not-cache-result
```

Konfigurasi pengujian menggunakan SQLite `:memory:`. Tes Pendidikan
memeriksa bahwa database tersebut terisolasi sebelum melakukan perubahan
data. Tes mencakup pembayaran, unduh bukti bayar, rekap nilai PDF, validasi
logbook, penempatan stase, perlindungan riwayat akademik, dan CSRF admin.

## File yang Tidak Disimpan di Git

File `.env`, backup konfigurasi, cache, log, session, dependency lokal,
file upload pengguna, skrip diagnostik lokal, serta panduan lokal seperti
`modul_admin.md` dan `panduan_pendidikan.md` tidak disertakan dalam repository.
`.env.example`, kode aplikasi, migration, pengujian, dan file pengaman folder
tetap disimpan. File upload yang sudah ada harus dibackup atau dipindahkan
secara terpisah ketika memindahkan aplikasi.

## Struktur Project

```text
SIM-RSUD-/
├── app/
│   ├── Config/              # Konfigurasi aplikasi, database, route, dan filter
│   ├── Controllers/         # Logika request dan alur tiap modul
│   ├── Database/
│   │   ├── Migrations/      # Struktur tabel database
│   │   └── Seeds/           # Data awal Pelatihan, Pendidikan, dan Riset
│   ├── Filters/             # Pembatasan akses berdasarkan autentikasi/role
│   ├── Models/              # Model dan akses data database
│   └── Views/               # Tampilan halaman aplikasi
├── public/
│   ├── assets/              # CSS, JavaScript, gambar, dan aset publik
│   └── index.php            # Entry point aplikasi
├── tests/                   # Pengujian aplikasi
├── writable/                # Cache, log, session, dan file upload
├── .env                     # Konfigurasi environment lokal
├── composer.json            # Dependency dan konfigurasi Composer
├── docker-compose.yml       # Konfigurasi menjalankan aplikasi dengan Docker
├── Dockerfile               # Image Docker aplikasi
└── spark                    # CLI CodeIgniter 4
```

Modul utama berada di dalam folder `app/Controllers`, `app/Models`, dan
`app/Views`, yaitu Pelatihan, Pendidikan, Riset, dan Super Admin.
