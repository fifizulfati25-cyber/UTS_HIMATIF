# Sistem Informasi Publik & Portal Absensi Anggota HIMATIF UCIC

Aplikasi Laravel untuk menampilkan informasi HIMATIF Universitas Catur Insan Cendekia (UCIC) dan membantu anggota mengisi absensi rapat secara daring.

## Latar Belakang

Informasi profil, program kerja, dokumentasi, dan kontak organisasi perlu tersedia di satu tempat. Absensi rapat yang dicatat manual juga lebih sulit direkap. Project ini menyediakan halaman publik HIMATIF serta portal anggota untuk jadwal, absensi, dan riwayat kehadiran.

## Teknologi

- PHP 8.3+
- Laravel 13
- Blade untuk tampilan halaman
- Session Laravel dan penyimpanan JSON lokal untuk data aplikasi
- PHPUnit untuk pengujian

## Fitur

### Informasi publik

- Beranda, profil, visi-misi, struktur organisasi, program kerja, galeri, dan kontak.
- Daftar serta detail program kerja dapat dibuka tanpa login.
- Logo HIMATIF tersedia di `public/images/logo-himatif.png`.

### Portal anggota dan pengurus

- Anggota mendaftar dengan nama, NIM, email, dan password.
- Pendaftaran anggota menunggu verifikasi pengurus sebelum anggota dapat login.
- Anggota terverifikasi dapat melihat jadwal rapat, mengisi absensi untuk rapat yang dibuka, dan melihat riwayat kehadiran miliknya.
- Pengurus dapat memverifikasi anggota, mengelola jadwal rapat, membuka atau menutup absensi, melihat rekap, dan mengelola program kerja.
- Sistem menolak absensi saat rapat ditutup atau anggota sudah mengisi absensi pada rapat yang sama.

### CRUD Program Kerja

Pengurus dapat menambah, melihat, mengubah, dan menghapus program kerja. Pengunjung dapat melihat daftar dan detailnya. Validasi form mencakup kode, nama, kategori, tanggal, lokasi, status, dan deskripsi.

## Konsep OOP dan Laravel MVC

- `Pengguna` adalah abstract class dasar. `Anggota` dan `PengurusHimatif` mewarisinya; `Anggota` juga menerapkan interface `BisaAbsen`.
- `Rapat` memiliki aturan status buka/tutup. `Absensi` menyimpan kehadiran anggota pada rapat. `Kegiatan`, `Foto`, dan `KontakPerson` mewakili objek informasi organisasi.
- Alur Laravel: **Route → Controller → Blade**. Route di `routes/web.php` memilih Controller, Controller menyiapkan atau memvalidasi data, lalu Blade menampilkan halaman.

## Penyimpanan Data

- Data CRUD program kerja disimpan menggunakan Laravel session dengan key `daftarKegiatan` dan `idKegiatanBerikutnya`; tidak memakai database.
- Akun anggota, jadwal rapat, dan absensi disimpan sebagai file JSON di `storage/app/private/himatif/`, di luar folder publik. File tersebut tidak masuk repository.
- `SESSION_DRIVER=file` digunakan agar session tidak memerlukan tabel database.
- Penyimpanan file lokal ditujukan untuk latihan dan demonstrasi pada satu server. Ini bukan pengganti layanan autentikasi dan database produksi.

## Struktur Folder Utama

```text
app/
├── Console/Commands/       perintah untuk membuat pengurus awal
├── Domain/Himatif/         class dan interface OOP
├── Http/Controllers/       penyiapan data dan logika request
├── Http/Middleware/        pemulihan akun serta pembatasan akses
└── Services/               session dan file data privat
bootstrap/                  konfigurasi awal Laravel dan middleware
config/                     konfigurasi Laravel dan profil HIMATIF
routes/web.php              route publik, anggota, pengurus, dan CRUD
resources/views/
├── auth/                   halaman login dan registrasi
├── informasi/              profil, galeri, dan kontak
├── kegiatan/               daftar, detail, tambah, dan edit program kerja
├── layouts/                layout publik, portal, autentikasi, dan styling
└── portal/                 dashboard, jadwal rapat, absensi, dan pengurus
public/                     entry point, logo, favicon, dan aset publik
storage/app/private/himatif/ data lokal privat saat aplikasi berjalan
tests/                      pengujian fitur dan unit
```

## Instalasi Lokal

Pastikan PHP 8.3+, Composer, dan Laravel dependencies tersedia. Dari folder project, jalankan di PowerShell:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
```

Pastikan `.env` memiliki pengaturan berikut:

```dotenv
SESSION_DRIVER=file
```

Jangan mengunggah `.env`; file itu menyimpan konfigurasi lokal. `.env.example` adalah contoh konfigurasi tanpa APP_KEY atau kredensial pribadi.

## Menjalankan Aplikasi

Dari terminal yang sedang berada di folder project:

```powershell
php artisan serve
```

Buka `http://127.0.0.1:8000` di browser. Hentikan server dengan `Ctrl+C`.

Untuk membuat akun pengurus pertama secara interaktif, jalankan sekali:

```powershell
php artisan himatif:admin:create
```

Terminal meminta nama, NIM, email, dan password tanpa memasukkan password ke source code. Jika pengurus sudah ada, perintah tidak membuat akun kedua. Simpan kredensial secara pribadi. Pengurus perlu memverifikasi akun anggota sebelum anggota dapat login.

Aplikasi publik HIMATIF memakai Blade dan styling yang disertakan layout. Untuk menjalankan fitur utama, project tidak memerlukan `npm run build` atau migrasi database.

## Route CRUD Program Kerja

| HTTP | URL | Nama route | Akses |
|---|---|---|---|
| GET | `/kegiatan` | `kegiatan.index` | Publik |
| GET | `/kegiatan/create` | `kegiatan.create` | Pengurus |
| POST | `/kegiatan` | `kegiatan.store` | Pengurus |
| GET | `/kegiatan/{id}` | `kegiatan.show` | Publik |
| GET | `/kegiatan/{id}/edit` | `kegiatan.edit` | Pengurus |
| PUT | `/kegiatan/{id}` | `kegiatan.update` | Pengurus |
| DELETE | `/kegiatan/{id}` | `kegiatan.destroy` | Pengurus |

Route pengelolaan dilindungi middleware login dan role pengurus. Halaman detail kegiatan tetap menerima parameter ID seperti `/kegiatan/1`.

## Pengujian

Jalankan seluruh pengujian dengan:

```powershell
php artisan test
```

Untuk memeriksa route program kerja:

```powershell
php artisan route:list --path=kegiatan
```

Pengujian fitur mencakup halaman publik, login dan registrasi, verifikasi anggota, pembatasan hak akses, pengelolaan rapat, absensi, riwayat, serta CRUD program kerja.

## Keterbatasan Project Praktikum

- Foto dokumentasi kegiatan belum tersedia di project.
- Halaman kontak masih menggunakan teks penanda sampai kontak resmi dikonfirmasi pengurus.
- Pastikan data visi-misi dan struktur organisasi yang ditampilkan sudah disetujui pengurus.
- Session dan file JSON berada di komputer/server lokal; lakukan backup serta jaga akses folder penyimpanan.
- Project ini untuk tugas praktikum, bukan penerapan autentikasi produksi.

## Screenshot

Screenshot aplikasi akan ditambahkan setelah dokumentasi selesai.
