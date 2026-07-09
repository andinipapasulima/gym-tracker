<?php
include 'koneksi.php';
include 'includes/functions.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = mysqli_prepare($koneksi, "DELETE FROM body_log WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    setFlash('Catatan berat badan dihapus.', 'info');
}

header("Location: bodylog.php");
exit;
