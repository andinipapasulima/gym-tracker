<?php
include 'koneksi.php';
include 'includes/functions.php';

$halaman_aktif = 'progress';
$judul_halaman = 'Progres & PR';

$prList = ambilPR($koneksi);

// Daftar gerakan yang pernah dicatat, untuk dropdown pemilih chart
$gerakanQuery = mysqli_query($koneksi, "SELECT DISTINCT gerakan FROM catatan_latihan ORDER BY gerakan ASC");
$daftarGerakan = [];
while ($row = mysqli_fetch_assoc($gerakanQuery)) $daftarGerakan[] = $row['gerakan'];

$gerakanDipilih = $_GET['gerakan'] ?? ($daftarGerakan[0] ?? '');

$chartLabels = [];
$chartBeban = [];
$chart1RM = [];
if ($gerakanDipilih) {
    $stmt = mysqli_prepare($koneksi, "SELECT tanggal, beban, sets, reps, repetisi FROM catatan_latihan WHERE gerakan = ? ORDER BY tanggal ASC");
    mysqli_stmt_bind_param($stmt, "s", $gerakanDipilih);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($res)) {
        [$s, $r] = parseSetsReps($row);
        $chartLabels[] = date('d M', strtotime($row['tanggal']));
        $chartBeban[] = (float)$row['beban'];
        $chart1RM[] = estimasi1RM($row['beban'], $r);
    }
}

include 'includes/header.php';
?>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Grafik Progres</h2>
            <div class="desc">Pantau kenaikan beban dari waktu ke waktu, per gerakan</div>
        </div>
        <form method="GET">
            <select name="gerakan" onchange="this.form.submit()">
                <?php foreach ($daftarGerakan as $g): ?>
                    <option value="<?= e($g) ?>" <?= $gerakanDipilih === $g ? 'selected' : '' ?>><?= e($g) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (empty($chartLabels)): ?>
        <div class="empty-state">
            <div class="icon">📈</div>
            Belum ada data untuk gerakan ini. <a href="catat.php">Catat latihan</a> untuk mulai melihat progres.
        </div>
    <?php else: ?>
        <canvas id="progressChart" height="90"></canvas>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Semua Personal Records</h2>
            <div class="desc">Diurutkan dari beban tertinggi</div>
        </div>
    </div>
    <?php if (empty($prList)): ?>
        <div class="empty-state"><div class="icon">🏆</div>Belum ada PR tercatat.</div>
    <?php else: ?>
        <div class="pr-grid">
            <?php foreach ($prList as $pr): ?>
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

<?php if (!empty($chartLabels)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('progressChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [
            {
                label: 'Beban Aktual (kg)',
                data: <?= json_encode($chartBeban) ?>,
                borderColor: '#ff5a1f',
                backgroundColor: 'rgba(255,90,31,0.12)',
                tension: 0.3,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: '#ff5a1f'
            },
            {
                label: 'Estimasi 1RM (kg)',
                data: <?= json_encode($chart1RM) ?>,
                borderColor: '#3ddc84',
                borderDash: [5,4],
                tension: 0.3,
                pointRadius: 3,
                pointBackgroundColor: '#3ddc84'
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { labels: { color: '#8d919c' } } },
        scales: {
            x: { grid: { display: false }, ticks: { color: '#8d919c' } },
            y: { grid: { color: '#303440' }, ticks: { color: '#8d919c' } }
        }
    }
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
