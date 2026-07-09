<?php
// Panggil koneksi database
include 'koneksi.php';

// Ambil ID dari URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk menghapus data berdasarkan ID
    $query_hapus = "DELETE FROM catatan_latihan WHERE id = '$id'";
    mysqli_query($koneksi, $query_hapus);
}

// Kembalikan pengguna ke halaman utama
header("Location: index.php");
exit;
?>