<?php
// Tentukan detail server
$host = "localhost";
$user = "root";
$pass = "";
$db   = "gym_tracker";

// Membuka koneksi ke database
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $koneksi = mysqli_connect($host, $user, $pass, $db);
    mysqli_set_charset($koneksi, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Koneksi database gagal. Pastikan XAMPP (MySQL) aktif dan database 'gym_tracker' sudah dibuat. Detail: " . $e->getMessage());
}
