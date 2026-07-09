<?php
// $halaman_aktif harus di-set di file pemanggil sebelum include ini
if (!isset($halaman_aktif)) $halaman_aktif = '';
$flash = ambilFlash();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($judul_halaman) ? e($judul_halaman) . ' — ' : '' ?>WZone Gym Tracker</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<div class="topbar">
    <div class="topbar-inner">
        <div class="brand">
            <div class="mark">W<span>ZONE</span></div>
            <div class="tag">Gym Log</div>
        </div>
        <nav class="tabs">
            <a href="index.php" class="<?= $halaman_aktif === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
            <a href="catat.php" class="<?= $halaman_aktif === 'catat' ? 'active' : '' ?>">Catat Latihan</a>
            <a href="riwayat.php" class="<?= $halaman_aktif === 'riwayat' ? 'active' : '' ?>">Riwayat</a>
            <a href="progress.php" class="<?= $halaman_aktif === 'progress' ? 'active' : '' ?>">Progres & PR</a>
            <a href="bodylog.php" class="<?= $halaman_aktif === 'bodylog' ? 'active' : '' ?>">Berat Badan</a>
        </nav>
    </div>
</div>

<main class="wrap">
    <?php if ($flash): ?>
        <div class="flash <?= e($flash['tipe']) ?>"><?= e($flash['pesan']) ?></div>
    <?php endif; ?>
