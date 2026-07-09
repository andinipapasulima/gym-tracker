<?php
include 'koneksi.php';
include 'includes/functions.php';

$halaman_aktif = 'riwayat';
$judul_halaman = 'Riwayat Latihan';

$kategoriList = daftarKategori();
$prList = ambilPR($koneksi);

// Filter
$fKategori = $_GET['kategori'] ?? '';
$fGerakan  = trim($_GET['gerakan'] ?? '');
$fDari     = $_GET['dari'] ?? '';
$fSampai   = $_GET['sampai'] ?? '';

$where = [];
$params = [];
$types = '';

if ($fKategori !== '') { $where[] = "kategori = ?"; $params[] = $fKategori; $types .= 's'; }
if ($fGerakan !== '')  { $where[] = "gerakan LIKE ?"; $params[] = "%$fGerakan%"; $types .= 's'; }
if ($fDari !== '')     { $where[] = "tanggal >= ?"; $params[] = $fDari; $types .= 's'; }
if ($fSampai !== '')   { $where[] = "tanggal <= ?"; $params[] = $fSampai; $types .= 's'; }

$sql = "SELECT * FROM catatan_latihan";
if ($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY tanggal DESC, id DESC";

$stmt = mysqli_prepare($koneksi, $sql);
if ($params) mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);

// Export CSV (pakai hasil query yang sama, sebelum output HTML apapun)
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=riwayat_latihan_' . date('Y-m-d') . '.csv');
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Tanggal', 'Gerakan', 'Kategori', 'Beban (kg)', 'Set', 'Repetisi', 'Catatan']);
    while ($row = mysqli_fetch_assoc($hasil)) {
        [$s, $r] = parseSetsReps($row);
        fputcsv($out, [$row['tanggal'], $row['gerakan'], $row['kategori'] ?? 'Lainnya', $row['beban'], $s, $r, $row['catatan'] ?? '']);
    }
    fclose($out);
    exit;
}

include 'includes/header.php';
?>

<div class="card">
    <div class="card-head">
        <div>
            <h2>Riwayat Latihan</h2>
            <div class="desc"><?= mysqli_num_rows($hasil) ?> catatan ditemukan</div>
        </div>
        <a href="riwayat.php?export=csv&<?= http_build_query($_GET) ?>" class="btn btn-outline btn-sm">Export CSV ⇩</a>
    </div>

    <form method="GET" class="filter-bar">
        <div class="field">
            <label>Kategori</label>
            <select name="kategori">
                <option value="">Semua</option>
                <?php foreach ($kategoriList as $k): ?>
                    <option value="<?= e($k) ?>" <?= $fKategori === $k ? 'selected' : '' ?>><?= e($k) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label>Cari Gerakan</label>
            <input type="text" name="gerakan" value="<?= e($fGerakan) ?>" placeholder="misal: squat">
        </div>
        <div class="field">
            <label>Dari</label>
            <input type="date" name="dari" value="<?= e($fDari) ?>">
        </div>
        <div class="field">
            <label>Sampai</label>
            <input type="date" name="sampai" value="<?= e($fSampai) ?>">
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <?php if ($fKategori || $fGerakan || $fDari || $fSampai): ?>
            <a href="riwayat.php" class="btn btn-outline">Reset</a>
        <?php endif; ?>
    </form>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Tanggal</th><th>Gerakan</th><th>Kategori</th><th>Beban</th><th>Set × Rep</th><th>Catatan</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($hasil) === 0): ?>
                    <tr><td colspan="7" class="empty-state">Tidak ada catatan yang cocok dengan filter ini.</td></tr>
                <?php else: while ($row = mysqli_fetch_assoc($hasil)):
                    [$s, $r] = parseSetsReps($row);
                    $isPR = isset($prList[$row['gerakan']]) && (float)$prList[$row['gerakan']]['pr_beban'] === (float)$row['beban'];
                ?>
                    <tr>
                        <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                        <td><?= e($row['gerakan']) ?> <?php if ($isPR): ?><span class="pr-badge">★ PR</span><?php endif; ?></td>
                        <td><span class="tag"><?= e($row['kategori'] ?? 'Lainnya') ?></span></td>
                        <td class="mono"><?= number_format($row['beban'], 1) ?> kg</td>
                        <td class="mono"><?= $s ?>×<?= $r ?></td>
                        <td style="color:var(--text-muted); font-size:13px;"><?= e($row['catatan'] ?? '') ?></td>
                        <td>
                            <a href="edit.php?id=<?= (int)$row['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                            <a href="hapus.php?id=<?= (int)$row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus catatan ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
