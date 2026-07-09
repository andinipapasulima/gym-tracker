<?php
include 'koneksi.php';
include 'includes/functions.php';

$halaman_aktif = 'dashboard';
$judul_halaman = 'Dashboard';

$ringkasan = ringkasanDashboard($koneksi);
$streak = hitungStreak($koneksi);
$prList = ambilPR($koneksi);
$prTop = array_slice($prList, 0, 6);

// Data 5 sesi terakhir untuk tabel ringkas
$sesiTerakhir = mysqli_query($koneksi, "SELECT * FROM catatan_latihan ORDER BY tanggal DESC, id DESC LIMIT 6");

// Data untuk chart: total volume per hari, 14 hari terakhir
$volQuery = "SELECT tanggal, SUM(beban * sets * GREATEST(reps,1)) AS volume
             FROM catatan_latihan
             WHERE tanggal >= DATE_SUB(CURDATE(), INTERVAL 14 DAY)
             GROUP BY tanggal ORDER BY tanggal ASC";
$volResult = mysqli_query($koneksi, $volQuery);
$chartLabels = [];
$chartData = [];
while ($row = mysqli_fetch_assoc($volResult)) {
    $chartLabels[] = date('d M', strtotime($row['tanggal']));
    $chartData[] = round((float)$row['volume'], 1);
}

include 'includes/header.php';
?>

<div class="stat-grid">
    <div class="stat-card">
        <div class="label">Sesi Bulan Ini</div>
        <div class="value accent"><?= $ringkasan['sesi_bulan_ini'] ?></div>
        <div class="sub">total <?= $ringkasan['total_sesi'] ?> sesi sepanjang waktu</div>
    </div>
    <div class="stat-card">
        <div class="label">Streak Latihan</div>
        <div class="value good"><?= $streak ?> <span style="font-size:16px;">hari</span></div>
        <div class="sub"><?= $streak > 0 ? 'terus pertahankan 🔥' : 'yuk mulai lagi hari ini' ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Volume Bulan Ini</div>
        <div class="value"><?= number_format($ringkasan['total_volume_bulan_ini'], 0, ',', '.') ?></div>
        <div class="sub">kg (beban × set × repetisi)</div>
    </div>
    <div class="stat-card">
        <div class="label">Latihan Terakhir</div>
        <div class="value">
            <?= $ringkasan['hari_sejak_terakhir'] === null ? '—' : ($ringkasan['hari_sejak_terakhir'] == 0 ? 'Hari ini' : $ringkasan['hari_sejak_terakhir'] . 'h') ?>
        </div>
        <div class="sub">lalu</div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Volume Latihan — 14 Hari Terakhir</h2>
            <div class="desc">Total beban × set × repetisi per hari</div>
        </div>
    </div>
    <?php if (empty($chartData)): ?>
        <div class="empty-state">
            <div class="icon">📊</div>
            Belum ada data di 14 hari terakhir. <a href="catat.php">Catat latihan pertamamu</a>.
        </div>
    <?php else: ?>
        <canvas id="volumeChart" height="90"></canvas>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Personal Records</h2>
            <div class="desc">Beban tertinggi yang pernah kamu angkat, per gerakan</div>
        </div>
        <a href="progress.php" class="btn btn-outline btn-sm">Lihat Semua →</a>
    </div>
    <?php if (empty($prTop)): ?>
        <div class="empty-state">
            <div class="icon">🏆</div>
            Belum ada PR tercatat. Mulai catat latihan untuk melihat rekor pribadimu di sini.
        </div>
    <?php else: ?>
        <div class="pr-grid">
            <?php foreach ($prTop as $pr): ?>
                <div class="pr-item">
                    <div class="name"><?= e($pr['gerakan']) ?></div>
                    <div class="kat"><?= e($pr['kategori']) ?></div>
                    <div class="weight"><?= number_format($pr['pr_beban'], 1) ?> kg</div>
                    <div class="barbell">
                        <div class="sleeve"></div>
                        <?php foreach (pecahPlat($pr['pr_beban']) as $plat): ?>
                            <div class="plate p<?= $plat['berat'] ?>" style="background: <?= $plat['warna'] ?>"></div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Aktivitas Terbaru</h2>
        </div>
        <a href="riwayat.php" class="btn btn-outline btn-sm">Lihat Semua →</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Tanggal</th><th>Gerakan</th><th>Kategori</th><th>Beban</th><th>Set × Rep</th></tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($sesiTerakhir) === 0): ?>
                    <tr><td colspan="5" class="empty-state">Belum ada catatan.</td></tr>
                <?php else: while ($row = mysqli_fetch_assoc($sesiTerakhir)):
                    [$s, $r] = parseSetsReps($row);
                    $isPR = isset($prList[$row['gerakan']]) && (float)$prList[$row['gerakan']]['pr_beban'] === (float)$row['beban'];
                ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= e($row['gerakan']) ?> <?php if ($isPR): ?><span class="pr-badge">★ PR</span><?php endif; ?></td>
                        <td><span class="tag"><?= e($row['kategori'] ?? 'Lainnya') ?></span></td>
                        <td class="mono"><?= number_format($row['beban'], 1) ?> kg</td>
                        <td class="mono"><?= $s ?>×<?= $r ?></td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($chartData)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('volumeChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            label: 'Volume (kg)',
            data: <?= json_encode($chartData) ?>,
            backgroundColor: '#ff5a1f',
            borderRadius: 4,
            maxBarThickness: 28
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#8d919c' } },
            y: { grid: { color: '#303440' }, ticks: { color: '#8d919c' } }
        }
    }
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
