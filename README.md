# WZone Gym Tracker v2

Versi upgrade dari gym-tracker kamu: desain baru + fitur dashboard, progress chart, PR otomatis, body weight tracker, filter riwayat, dan export CSV. Semua query database sudah pakai **prepared statements** jadi aman dari SQL injection.

## Cara pasang (XAMPP)

1. **Backup dulu** folder project lama kamu (copy ke tempat lain), just in case.
2. Copy semua file di sini ke `htdocs/gym-tracker` (timpa file lama).
3. Buka **phpMyAdmin** → pilih database `gym_tracker` → tab **SQL** → paste isi `db_migration.sql` → jalankan (Go).
   - Script ini aman dijalankan di database yang sudah ada isinya — kolom lama tidak dihapus, cuma nambah kolom baru (`kategori`, `sets`, `reps`, `catatan`) dan tabel baru `body_log`.
4. Buka `http://localhost/gym-tracker/` di browser.

## Yang baru dibanding versi lama

- **Dashboard** (`index.php`) — statistik sesi bulan ini, streak latihan, total volume, grafik 14 hari terakhir, dan preview PR.
- **Catat Latihan** (`catat.php`) — form terpisah dengan kategori otot, jumlah set & repetisi terpisah (bukan teks bebas lagi), catatan opsional, dan notifikasi otomatis kalau kamu baru saja bikin PR.
- **Riwayat** (`riwayat.php`) — filter by kategori, cari gerakan, rentang tanggal, dan tombol export ke CSV.
- **Progres & PR** (`progress.php`) — grafik garis progres beban per gerakan (plus estimasi 1RM pakai formula Epley) dan daftar semua Personal Record dengan visual "barbell" plat gym.
- **Berat Badan** (`bodylog.php`) — catat & pantau tren berat badan terpisah dari catatan latihan.
- **Keamanan** — semua query INSERT/UPDATE/DELETE/SELECT yang melibatkan input pengguna sekarang pakai prepared statements (`mysqli_prepare` + `bind_param`), jadi tidak lagi rentan SQL injection seperti versi sebelumnya.

## Struktur file

```
wzone/
├── koneksi.php          → koneksi database
├── db_migration.sql     → jalankan sekali di phpMyAdmin
├── index.php            → dashboard
├── catat.php            → form tambah latihan
├── riwayat.php          → tabel riwayat + filter + export CSV
├── edit.php / hapus.php → edit & hapus catatan latihan
├── progress.php         → grafik progres + daftar PR
├── bodylog.php          → tracker berat badan
├── hapus_berat.php      → hapus catatan berat badan
├── includes/
│   ├── functions.php    → helper (kategori, PR, streak, plate visual)
│   ├── header.php       → nav & layout atas
│   └── footer.php       → layout bawah
└── assets/
    └── style.css        → semua styling (tema "Steel & Molten Iron")
```

## Ide pengembangan selanjutnya (kalau mau lanjut)

- Login sederhana kalau nanti mau dipakai lebih dari satu orang.
- Reminder/notifikasi kalau sudah beberapa hari belum latihan.
- Target mingguan (misal "latihan 4x minggu ini") dengan progress bar.
