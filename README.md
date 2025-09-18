# Aplikasi Web Tombol Panik (Panic Button)

Aplikasi web lengkap dengan fitur tombol panik dan sistem manajemen data, yang dikembangkan murni menggunakan PHP native, HTML, CSS, dan Bootstrap.

## Fitur Utama

### Untuk Siswa
- **Halaman Login Tunggal**: Satu halaman login untuk Siswa dan Guru.
- **Dashboard Siswa**: Antarmuka sederhana setelah login.
- **Tombol Panik**: Tombol besar yang saat diklik langsung mengirim laporan darurat ke database.
- **Riwayat Laporan**: Siswa dapat melihat daftar laporan yang telah mereka buat beserta statusnya.

### Untuk Guru (Admin)
- **Dashboard Guru**: Pusat kendali untuk admin.
- **Daftar Laporan**: Menampilkan semua laporan panik dari siswa dalam tabel yang terorganisir.
- **Update Status Laporan**: Admin dapat mengubah status laporan (Belum Diproses, Dalam Penanganan, Selesai) secara *real-time* tanpa me-refresh halaman.
- **Manajemen Data Siswa**: CRUD (Create, Read, Update, Delete) penuh untuk data siswa.
- **Manajemen Data Guru**: CRUD penuh untuk data guru.
- **Manajemen User**: Halaman terpadu yang menampilkan semua pengguna aplikasi.

## Teknologi yang Digunakan

- **Backend**: PHP 8+ (Native, tanpa framework)
- **Database**: MySQL atau MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (Native)
- **UI Framework**: Bootstrap 5.3

## Panduan Instalasi dan Konfigurasi

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di lingkungan server lokal seperti XAMPP atau WAMP.

### 1. Dapatkan Kode Sumber
- Unduh atau clone repositori ini ke direktori `htdocs` (untuk XAMPP) atau `www` (untuk WAMP) di server lokal Anda.
  ```bash
  git clone https://github.com/username/panic-button-app.git
  ```
- Pindahkan ke dalam folder `htdocs`, misalnya `C:\xampp\htdocs\panic-button-app`.

### 2. Buat dan Isi Database
- Buka `phpMyAdmin` dari panel kontrol XAMPP Anda (`http://localhost/phpmyadmin`).
- Buat database baru. Beri nama `panic_button_db` (atau nama lain pilihan Anda).
- Pilih database yang baru dibuat, lalu klik tab **Import**.
- Unggah file `database.sql` yang ada di root proyek ini dan jalankan proses impor. Ini akan membuat semua tabel yang diperlukan.

### 3. Konfigurasi Koneksi Database
- Buka file `config/config.php` di editor kode Anda.
- Sesuaikan nilai-nilai konstanta berikut agar sesuai dengan pengaturan database Anda.
  ```php
  // Ganti dengan pengaturan server database Anda
  define('DB_SERVER', 'localhost'); // atau '127.0.0.1'
  define('DB_USERNAME', 'root'); // username default xampp
  define('DB_PASSWORD', ''); // password default xampp kosong
  define('DB_NAME', 'panic_button_db'); // nama database yang Anda buat
  ```

### 4. Buat User Awal (Admin dan Siswa)
- Buka terminal atau command prompt.
- Arahkan ke direktori root proyek Anda.
  ```bash
  cd C:\xampp\htdocs\panic-button-app
  ```
- Jalankan skrip PHP berikut untuk membuat pengguna default. Pastikan `php` ada di PATH environment variable Anda.
  ```bash
  php util/generate_users.php
  ```
- Skrip ini akan membuat dua pengguna:
  - **Admin**: `username: admin`, `password: password`
  - **Siswa**: `username: siswa`, `password: password`
- **PENTING**: Segera ganti password ini setelah Anda berhasil login untuk pertama kali.

### 5. (Opsional) Tambahkan Suara Notifikasi
- Untuk mengaktifkan notifikasi suara di dasbor admin, letakkan file suara (`.mp3`, `.wav`, `.ogg`) di dalam direktori `assets/`.
- Ubah nama file tersebut menjadi `notification.mp3`.
- Jika Anda tidak menyediakan file ini, fitur notifikasi akan tetap berjalan tanpa suara.

### 6. Jalankan Aplikasi
- Pastikan server Apache dan MySQL Anda berjalan dari panel kontrol XAMPP.
- Buka browser Anda dan akses URL berikut:
  ```
  http://localhost/panic-button-app/
  ```
- Anda akan melihat halaman login. Gunakan kredensial yang dibuat di langkah sebelumnya untuk masuk.

## Panduan Deployment ke Hosting

Untuk mendeploy aplikasi ini ke hosting web standar (seperti cPanel):
1. **Unggah File**: Unggah semua file dan folder proyek ke direktori `public_html` (atau direktori root web lainnya) di hosting Anda menggunakan FTP client (seperti FileZilla) atau Manajer File cPanel.
2. **Database**: Gunakan fitur seperti "MySQL Database Wizard" di cPanel untuk membuat database dan pengguna database baru. Catat nama database, username, dan password.
3. **Impor SQL**: Impor file `database.sql` ke database yang baru Anda buat menggunakan `phpMyAdmin` di cPanel.
4. **Konfigurasi**: Perbarui file `config/config.php` di server dengan kredensial database yang Anda buat di langkah 2.
5. **Jalankan Skrip User (Jika Diperlukan)**: Jika hosting Anda menyediakan akses SSH atau terminal, Anda dapat menjalankan skrip `util/generate_users.php`. Jika tidak, Anda mungkin perlu menambahkan pengguna secara manual melalui `phpMyAdmin` (pastikan untuk menggunakan `PASSWORD_DEFAULT` hash untuk password).
6. **Selesai**: Aplikasi Anda seharusnya sudah bisa diakses melalui domain Anda.
