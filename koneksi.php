<?php
// Tentukan detail server
$host = "localhost";
$user = "root";
$pass = "";
$db   = "gym_tracker";

// Membuka koneksi ke database
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Periksa apakah koneksi gagal
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>