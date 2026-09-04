<div align="center">

# 🏋️ WZone Gym Tracker

**Aplikasi web pribadi untuk mencatat latihan gym, memantau progres beban & Personal Record, serta melacak berat badan.**

Dibangun dengan PHP native + MySQL, tanpa framework, dirancang untuk dijalankan lokal lewat XAMPP.

</div>

---

## 📋 Daftar Isi

- [Fitur](#-fitur)
- [Preview Alur Aplikasi](#-preview-alur-aplikasi)
- [Tech Stack](#️-tech-stack)
- [Prasyarat](#-prasyarat)
- [Instalasi](#-instalasi)
- [Setup Akun Pertama (Login)](#-setup-akun-pertama-login)
- [Struktur Database](#️-struktur-database)
- [Struktur File Proyek](#-struktur-file-proyek)
- [Keamanan](#-keamanan)
- [Troubleshooting](#-troubleshooting)
- [Rencana Pengembangan](#️-rencana-pengembangan-selanjutnya)

---

## ✨ Fitur

| Fitur | Halaman | Deskripsi |
|---|---|---|
| 🔐 **Login** | `login.php` | Autentikasi wajib sebelum bisa akses aplikasi. Password disimpan ter-hash (bcrypt), bukan teks biasa. |
| 📊 **Dashboard** | `index.php` | Statistik sesi bulan ini, streak latihan berturut-turut, total volume angkatan, grafik volume 14 hari terakhir, dan preview Personal Record. |
| ✍️ **Catat Latihan** | `catat.php` | Form input dengan kategori otot, jumlah set & repetisi terpisah, catatan opsional, dan notifikasi otomatis saat kamu mencetak PR baru. |
| 📜 **Riwayat** | `riwayat.php` | Daftar semua catatan latihan dengan filter kategori, pencarian gerakan, rentang tanggal, dan tombol export ke CSV. |
| 📈 **Progres & PR** | `progress.php` | Grafik garis progres beban per gerakan, estimasi 1RM (formula Epley), dan daftar seluruh Personal Record dengan visual "barbell plate" ala gym. |
| ⚖️ **Berat Badan** | `bodylog.php` | Catat dan pantau tren berat badan, terpisah dari catatan latihan. |
| ✏️🗑️ **Edit & Hapus** | `edit.php`, `hapus.php`, `hapus_berat.php` | Kelola ulang catatan yang sudah tersimpan. Aksi hapus dikirim lewat `POST` (bukan link biasa) supaya tidak bisa terpicu tanpa sengaja. |

### Kenapa datanya aman?

- **Prepared statements di semua query** yang melibatkan input pengguna (`mysqli_prepare` + `bind_param`) → bebas dari celah SQL Injection.
- **Output di-escape** lewat fungsi `e()` (`htmlspecialchars`) → bebas dari celah XSS di halaman yang menampilkan data.
- **Login wajib** di semua halaman utama (`requireLogin()`), jadi data latihan kamu tidak bisa diakses/diubah sembarangan orang di jaringan yang sama.
- **Password di-hash** pakai `password_hash()` (bcrypt) dan diverifikasi pakai `password_verify()` — tidak pernah disimpan dalam bentuk teks biasa.

---

## 🧭 Preview Alur Aplikasi

```
Buka aplikasi
     │
     ▼
Ada akun terdaftar?
     │
   ┌─┴──┐
   │Belum│──▶ register.php (buat akun admin pertama) ──▶ otomatis login
   │  Ada │──▶ login.php (masukkan username & password)
   └─┬──┘
     ▼
  index.php (Dashboard)
     │
     ├── catat.php      → tambah catatan latihan
     ├── riwayat.php     → lihat, filter, edit, hapus, export CSV
     ├── progress.php    → grafik progres & daftar PR
     ├── bodylog.php     → catat & lihat tren berat badan
     └── logout.php      → keluar, kembali ke login.php
```

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | PHP (native, `mysqli`) |
| Database | MySQL / MariaDB |
| Frontend | HTML, CSS (`assets/style.css` — tema dark "Steel & Molten Iron") |
| Grafik | [Chart.js 4](https://www.chartjs.org/) via CDN |
| Autentikasi | Session PHP native + `password_hash()` / `password_verify()` |
| Local server | XAMPP (Apache + MySQL/MariaDB + PHP) |

---

## 📋 Prasyarat

- [XAMPP](https://www.apachefriends.org/) — sudah termasuk Apache, MySQL/MariaDB, dan PHP.
- PHP versi 7.2 ke atas (butuh dukungan `password_hash()`, sudah pasti tersedia di versi XAMPP modern).
- Browser modern untuk mengakses aplikasi.

---

## 🚀 Instalasi

1. **Clone atau download** repo ini ke folder `htdocs` XAMPP kamu:
   ```bash
   cd C:/xampp/htdocs
   git clone https://github.com/andinipapasulima/gym-tracker.git
   ```
2. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel (pastikan keduanya berstatus hijau/running).
3. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`), buat database baru bernama `gym_tracker`.
4. Masuk ke database `gym_tracker` → tab **SQL** → paste seluruh isi file `db_migration.sql` → klik **Go**.
   > ✅ Script ini aman dijalankan berkali-kali dan aman untuk database yang sudah berisi data — kolom/tabel lama tidak dihapus. Script ini akan membuat/melengkapi:
   > - Kolom tambahan di `catatan_latihan` (`kategori`, `sets`, `reps`, `catatan`, `created_at`)
   > - Tabel `body_log` (tracker berat badan)
   > - Index untuk mempercepat query dashboard & grafik
   > - Tabel `users` (untuk login)
5. Cek konfigurasi koneksi database di `koneksi.php` (default: host `localhost`, user `root`, tanpa password) dan sesuaikan kalau konfigurasi MySQL kamu berbeda.
6. Buka `http://localhost/gym-tracker/` di browser.

---

## 🔐 Setup Akun Pertama (Login)

Aplikasi ini **mewajibkan login** — tidak ada data yang bisa dilihat/diubah tanpa masuk akun terlebih dahulu.

1. Saat pertama kali membuka aplikasi dan **belum ada akun sama sekali** di tabel `users`, kamu akan otomatis diarahkan ke halaman **`register.php`** untuk membuat akun admin (username + password, minimal 6 karakter).
2. Setelah akun dibuat, kamu langsung masuk otomatis ke Dashboard.
3. Untuk sesi berikutnya, akses aplikasi akan diarahkan ke **`login.php`**.
4. Tombol **Keluar** tersedia di pojok kanan atas navbar (`logout.php`) untuk mengakhiri sesi.

> ⚠️ Halaman `register.php` hanya aktif selama tabel `users` masih kosong — begitu ada 1 akun terdaftar, halaman ini otomatis mengarahkan ke `login.php` dan tidak bisa dipakai untuk mendaftar akun baru lagi. Aplikasi ini didesain **single-user** (satu akun admin per instalasi), sesuai kebutuhan tracker pribadi.

---

## 🗄️ Struktur Database

```
users
├── id              INT, PK, AUTO_INCREMENT
├── username        VARCHAR(50), UNIQUE
├── password_hash   VARCHAR(255)
└── created_at      TIMESTAMP

catatan_latihan
├── id              INT, PK, AUTO_INCREMENT
├── tanggal         DATE
├── gerakan         VARCHAR
├── kategori        VARCHAR(50)
├── beban           DECIMAL
├── sets            INT
├── reps            INT
├── repetisi        VARCHAR   (format lama "3x12", tetap didukung untuk kompatibilitas)
├── catatan         VARCHAR(255), nullable
└── created_at      TIMESTAMP

body_log
├── id              INT, PK, AUTO_INCREMENT
├── tanggal         DATE
├── berat_badan     DECIMAL(5,2)
├── catatan         VARCHAR(255), nullable
└── created_at      TIMESTAMP
```

---

## 📁 Struktur File Proyek

```
gym-tracker/
├── koneksi.php          → koneksi database
├── db_migration.sql     → skema & migrasi database (jalankan sekali di phpMyAdmin)
│
├── register.php         → setup akun admin pertama
├── login.php            → form login
├── logout.php           → hapus sesi & kembali ke login
│
├── index.php            → dashboard
├── catat.php             → form tambah catatan latihan
├── riwayat.php           → tabel riwayat + filter + export CSV
├── edit.php              → edit catatan latihan
├── hapus.php             → hapus catatan latihan (via POST)
├── progress.php          → grafik progres beban + daftar PR
├── bodylog.php           → tracker berat badan
├── hapus_berat.php       → hapus catatan berat badan (via POST)
│
├── includes/
│   ├── auth.php          → helper login (isLoggedIn, requireLogin, jumlahUser)
│   ├── functions.php     → helper umum (kategori, hitung PR, streak, visual plate, dst.)
│   ├── header.php        → navigasi atas + info user yang login
│   └── footer.php        → layout bawah
│
└── assets/
    └── style.css         → seluruh styling aplikasi
```

---

## 🔒 Keamanan

| Aspek | Implementasi |
|---|---|
| SQL Injection | Semua query dengan input pengguna pakai **prepared statement** (`mysqli_prepare` + `bind_param`). |
| XSS | Semua output ke HTML di-escape lewat fungsi `e()` (`htmlspecialchars`). |
| Password | Di-hash pakai `password_hash()` (bcrypt), diverifikasi dengan `password_verify()` — tidak pernah disimpan sebagai teks biasa. |
| Session hijacking | `session_regenerate_id(true)` dipanggil setiap kali login berhasil. |
| Aksi hapus data | Dikirim via `POST` (bukan link `GET`), supaya tidak bisa terpicu oleh link/preload/crawler tanpa sengaja. |
| Akses halaman | Semua halaman utama memanggil `requireLogin()` di baris paling atas — otomatis redirect ke `login.php` kalau belum masuk akun. |

> Catatan: aplikasi ini didesain untuk **penggunaan lokal/pribadi** (localhost via XAMPP). Kalau berencana meng-host di server publik, tambahkan HTTPS, rate limiting untuk percobaan login, dan pertimbangkan menonaktifkan `register.php` setelah akun pertama dibuat (misalnya dengan menghapus filenya).

---

## 🧩 Troubleshooting

<details>
<summary><strong>Warning: include(includes/xxx.php): Failed to open stream</strong></summary>

File yang dimaksud belum ada di folder `includes/` project kamu. Pastikan strukturnya persis seperti di bagian [Struktur File Proyek](#-struktur-file-proyek) — semua file bantuan (`auth.php`, `functions.php`, `header.php`, `footer.php`) harus ada **di dalam** folder `includes/`, sejajar satu sama lain.
</details>

<details>
<summary><strong>Fatal error: Uncaught mysqli_sql_exception: MySQL server has gone away</strong></summary>

Ini bukan masalah di kode, tapi koneksi ke MySQL yang terputus di tengah proses. Coba:
1. Cek status MySQL di XAMPP Control Panel (masih hijau/running?).
2. Cek log error di `C:\xampp\mysql\data\mysql_error.log`.
3. Pastikan tidak ada aplikasi lain yang bentrok di port `3306`.
4. Restart MySQL (Stop lalu Start lagi) dari XAMPP Control Panel.
</details>

<details>
<summary><strong>Koneksi database gagal / "Database 'gym_tracker' sudah dibuat"</strong></summary>

Pastikan sudah membuat database bernama **persis** `gym_tracker` di phpMyAdmin, dan MySQL sedang berjalan. Cek juga kredensial di `koneksi.php` sesuai konfigurasi MySQL kamu.
</details>

---

## 🗺️ Rencana Pengembangan Selanjutnya

- [x] ~~Login sederhana untuk mendukung lebih dari satu pengguna.~~ *(sudah diimplementasikan sebagai login single-user)*
- [ ] Manajemen multi-user (kalau nanti dipakai lebih dari satu orang).
- [ ] Reminder/notifikasi jika sudah beberapa hari belum latihan.
- [ ] Target mingguan (misal "latihan 4x minggu ini") dengan progress bar.

---

<div align="center">

👤 Dibuat oleh [**Andini**](https://github.com/andinipapasulima)

</div>
