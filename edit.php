<?php
include 'koneksi.php';
include 'includes/functions.php';

$halaman_aktif = 'riwayat';
$judul_halaman = 'Edit Catatan';

if (!isset($_GET['id'])) { header("Location: riwayat.php"); exit; }
$id = (int)$_GET['id'];

$stmt = mysqli_prepare($koneksi, "SELECT * FROM catatan_latihan WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$data = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$data) { header("Location: riwayat.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $tanggal  = $_POST['tanggal'];
    $gerakan  = trim($_POST['gerakan']);
    $kategori = $_POST['kategori'];
    $beban    = (float)$_POST['beban'];
    $sets     = max(1, (int)$_POST['sets']);
    $reps     = max(0, (int)$_POST['reps']);
    $catatan  = trim($_POST['catatan'] ?? '');
    $repetisi = $sets . 'x' . $reps;

    $upd = mysqli_prepare($koneksi, "UPDATE catatan_latihan
                                      SET tanggal=?, gerakan=?, kategori=?, beban=?, sets=?, reps=?, repetisi=?, catatan=?
                                      WHERE id=?");
    mysqli_stmt_bind_param($upd, "sssdiissi", $tanggal, $gerakan, $kategori, $beban, $sets, $reps, $repetisi, $catatan, $id);
    mysqli_stmt_execute($upd);

    setFlash('Perubahan berhasil disimpan.', 'sukses');
    header("Location: riwayat.php");
    exit;
}

[$curSets, $curReps] = parseSetsReps($data);
$kategoriList = daftarKategori();
$gerakanUmum = daftarGerakanUmum();
$semuaGerakan = array_merge(...array_values($gerakanUmum));

include 'includes/header.php';
?>

<div class="card" style="max-width:640px; margin:0 auto;">
    <div class="card-head">
        <div><h2>Edit Catatan Latihan</h2></div>
    </div>

    <form method="POST">
        <div class="form-grid">
            <div class="field">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="<?= e($data['tanggal']) ?>" required>
            </div>
            <div class="field">
                <label>Kategori</label>
                <select name="kategori" required>
                    <?php foreach ($kategoriList as $k): ?>
                        <option value="<?= e($k) ?>" <?= ($data['kategori'] ?? '') === $k ? 'selected' : '' ?>><?= e($k) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="field" style="margin-top:14px;">
            <label>Gerakan</label>
            <input type="text" name="gerakan" list="gerakan-list" value="<?= e($data['gerakan']) ?>" required>
            <datalist id="gerakan-list">
                <?php foreach ($semuaGerakan as $g): ?><option value="<?= e($g) ?>"><?php endforeach; ?>
            </datalist>
        </div>

        <div class="form-grid" style="margin-top:14px;">
            <div class="field">
                <label>Beban (kg)</label>
                <input type="number" step="0.5" min="0" name="beban" value="<?= e($data['beban']) ?>" required>
            </div>
            <div class="field">
                <label>Jumlah Set</label>
                <input type="number" min="1" name="sets" value="<?= $curSets ?>" required>
            </div>
            <div class="field">
                <label>Repetisi per Set</label>
                <input type="number" min="0" name="reps" value="<?= $curReps ?>" required>
            </div>
        </div>

        <div class="field" style="margin-top:14px;">
            <label>Catatan (opsional)</label>
            <input type="text" name="catatan" value="<?= e($data['catatan'] ?? '') ?>">
        </div>

        <div style="display:flex; gap:10px; margin-top:20px;">
            <button type="submit" name="update" class="btn btn-primary" style="flex:1;">Simpan Perubahan</button>
            <a href="riwayat.php" class="btn btn-outline">Batal</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
