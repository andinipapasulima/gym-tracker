# 🏋️ WZone Gym Tracker

Aplikasi web sederhana untuk mencatat sesi latihan gym, memantau progres beban & Personal Record (PR), serta melacak berat badan — dibangun dengan PHP native + MySQL, tanpa framework, dan dirancang untuk berjalan di XAMPP secara lokal.

## ✨ Fitur

- **Dashboard** (`index.php`) — statistik sesi bulan ini, streak latihan berturut-turut, total volume angkatan, grafik volume 14 hari terakhir, dan preview PR terbaru.
- **Catat Latihan** (`catat.php`) — form input dengan kategori otot, jumlah set & repetisi terpisah, catatan opsional, dan notifikasi otomatis saat kamu mencetak PR baru.
- **Riwayat** (`riwayat.php`) — daftar semua catatan latihan dengan filter kategori, pencarian gerakan, rentang tanggal, serta tombol export ke CSV.
- **Progres & PR** (`progress.php`) — grafik garis progres beban per gerakan, estimasi 1RM (formula Epley), dan daftar seluruh Personal Record dengan visual "barbell plate" ala gym.
- **Berat Badan** (`bodylog.php`) — catat dan pantau tren berat badan, terpisah dari catatan latihan.
- **Edit & Hapus** (`edit.php`, `hapus.php`, `hapus_berat.php`) — kelola ulang catatan latihan maupun berat badan yang sudah tersimpan.
- **Keamanan** — seluruh query `INSERT`/`UPDATE`/`DELETE`/`SELECT` yang melibatkan input pengguna menggunakan **prepared statements** (`mysqli_prepare` + `bind_param`), sehingga aman dari SQL injection.

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | PHP (native, `mysqli`) |
| Database | MySQL / MariaDB |
| Frontend | HTML, CSS (`assets/style.css` — tema "Steel & Molten Iron") |
| Grafik | [Chart.js 4](https://www.chartjs.org/) via CDN |
| Local server | XAMPP |

## 📋 Prasyarat

- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL + PHP)
- Browser modern untuk mengakses aplikasi

## 🚀 Instalasi

1. **Clone repo ini** ke folder `htdocs` XAMPP kamu:
   ```bash
   cd C:/xampp/htdocs
   git clone https://github.com/andinipapasulima/gym-tracker.git
   ```
2. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.
3. Buka **phpMyAdmin** (`http://localhost/phpmyadmin`), buat database baru bernama `gym_tracker`.
4. Masuk ke database `gym_tracker` → tab **SQL** → paste isi file `db_migration.sql` → jalankan (**Go**).
   > Script ini aman dijalankan di database yang sudah ada isinya — kolom lama tidak dihapus, hanya menambah kolom baru (`kategori`, `sets`, `reps`, `catatan`) dan tabel baru `body_log`.
5. Cek konfigurasi koneksi database di `koneksi.php` (default: host `localhost`, user `root`, tanpa password) dan sesuaikan bila perlu.
6. Buka `http://localhost/gym-tracker/` di browser.

## 📁 Struktur Proyek

```
gym-tracker/
├── koneksi.php          → koneksi database
├── db_migration.sql     → skema & migrasi database (jalankan sekali di phpMyAdmin)
├── index.php            → dashboard
├── catat.php            → form tambah catatan latihan
├── riwayat.php          → tabel riwayat + filter + export CSV
├── edit.php             → edit catatan latihan
├── hapus.php            → hapus catatan latihan
├── progress.php         → grafik progres beban + daftar PR
├── bodylog.php          → tracker berat badan
├── hapus_berat.php      → hapus catatan berat badan
├── includes/
│   ├── functions.php    → helper (kategori, hitung PR, streak, visual plate)
│   ├── header.php       → navigasi & layout atas
│   └── footer.php       → layout bawah
└── assets/
    └── style.css        → seluruh styling
```

## 🗺️ Rencana Pengembangan Selanjutnya

- [ ] Login sederhana untuk mendukung lebih dari satu pengguna.
- [ ] Reminder/notifikasi jika sudah beberapa hari belum latihan.
- [ ] Target mingguan (misal "latihan 4x minggu ini") dengan progress bar.

## 👤 Author

Dibuat oleh [Andini](https://github.com/andinipapasulima).
