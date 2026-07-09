<?php
include 'koneksi.php';
include 'includes/functions.php';

$halaman_aktif = 'bodylog';
$judul_halaman = 'Berat Badan';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $berat = (float)$_POST['berat_badan'];
    $catatan = trim($_POST['catatan'] ?? '');

    $stmt = mysqli_prepare($koneksi, "INSERT INTO body_log (tanggal, berat_badan, catatan) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sds", $tanggal, $berat, $catatan);
    mysqli_stmt_execute($stmt);

    setFlash('Berat badan berhasil dicatat.', 'sukses');
    header("Location: bodylog.php");
    exit;
}

$log = mysqli_query($koneksi, "SELECT * FROM body_log ORDER BY tanggal DESC LIMIT 30");
$chartQuery = mysqli_query($koneksi, "SELECT * FROM body_log ORDER BY tanggal ASC");
$chartLabels = [];
$chartData = [];
while ($row = mysqli_fetch_assoc($chartQuery)) {
    $chartLabels[] = date('d M', strtotime($row['tanggal']));
    $chartData[] = (float)$row['berat_badan'];
}

$terbaru = end($chartData);
$sebelumnya = count($chartData) > 1 ? $chartData[count($chartData) - 2] : null;
$selisih = ($terbaru !== false && $sebelumnya !== null) ? round($terbaru - $sebelumnya, 1) : null;

include 'includes/header.php';
?>

<div class="stat-grid">
    <div class="stat-card">
        <div class="label">Berat Terakhir</div>
        <div class="value accent"><?= $terbaru !== false ? number_format($terbaru, 1) . ' kg' : '—' ?></div>
    </div>
    <div class="stat-card">
        <div class="label">Perubahan Terakhir</div>
        <div class="value <?= $selisih !== null && $selisih < 0 ? 'good' : '' ?>">
            <?= $selisih === null ? '—' : ($selisih > 0 ? '+' : '') . $selisih . ' kg' ?>
        </div>
        <div class="sub">dari catatan sebelumnya</div>
    </div>
    <div class="stat-card">
        <div class="label">Total Catatan</div>
        <div class="value"><?= count($chartData) ?></div>
    </div>
</div>

<div class="card" style="max-width:520px;">
    <div class="card-head"><h2>Catat Berat Badan</h2></div>
    <form method="POST">
        <div class="form-grid">
            <div class="field">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="field">
                <label>Berat (kg)</label>
                <input type="number" step="0.1" min="0" name="berat_badan" required>
            </div>
        </div>
        <div class="field" style="margin-top:14px;">
            <label>Catatan (opsional)</label>
            <input type="text" name="catatan" placeholder="Contoh: setelah bangun tidur">
        </div>
        <button type="submit" name="simpan" class="btn btn-primary btn-block" style="margin-top:18px;">Simpan</button>
    </form>
</div>

<div class="card">
    <div class="card-head"><h2>Tren Berat Badan</h2></div>
    <?php if (empty($chartData)): ?>
        <div class="empty-state"><div class="icon">⚖️</div>Belum ada data berat badan.</div>
    <?php else: ?>
        <canvas id="bodyChart" height="80"></canvas>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-head"><h2>Riwayat</h2></div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Tanggal</th><th>Berat</th><th>Catatan</th><th>Aksi</th></tr></thead>
            <tbody>
                <?php if (mysqli_num_rows($log) === 0): ?>
                    <tr><td colspan="4" class="empty-state">Belum ada catatan.</td></tr>
                <?php else: while ($row = mysqli_fetch_assoc($log)): ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                        <td class="mono"><?= number_format($row['berat_badan'], 1) ?> kg</td>
                        <td style="color:var(--text-muted); font-size:13px;"><?= e($row['catatan'] ?? '') ?></td>
                        <td><a href="hapus_berat.php?id=<?= (int)$row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus catatan berat badan ini?')">Hapus</a></td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (!empty($chartData)): ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('bodyChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [{
            label: 'Berat Badan (kg)',
            data: <?= json_encode($chartData) ?>,
            borderColor: '#ffc24b',
            backgroundColor: 'rgba(255,194,75,0.12)',
            tension: 0.35,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: '#ffc24b'
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
