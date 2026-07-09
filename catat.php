<?php
include 'koneksi.php';
include 'includes/functions.php';

$halaman_aktif = 'catat';
$judul_halaman = 'Catat Latihan';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simpan'])) {
    $tanggal  = $_POST['tanggal'];
    $gerakan  = trim($_POST['gerakan']);
    $kategori = $_POST['kategori'];
    $beban    = (float)$_POST['beban'];
    $sets     = max(1, (int)$_POST['sets']);
    $reps     = max(0, (int)$_POST['reps']);
    $catatan  = trim($_POST['catatan'] ?? '');
    $repetisi = $sets . 'x' . $reps;

    if ($gerakan === '') {
        setFlash('Nama gerakan tidak boleh kosong.', 'error');
        header("Location: catat.php");
        exit;
    }

    // Cek apakah ini PR baru sebelum insert (untuk pesan sukses)
    $stmtCheck = mysqli_prepare($koneksi, "SELECT MAX(beban) AS pr FROM catatan_latihan WHERE gerakan = ?");
    mysqli_stmt_bind_param($stmtCheck, "s", $gerakan);
    mysqli_stmt_execute($stmtCheck);
    $prLama = mysqli_fetch_assoc(mysqli_stmt_get_result($stmtCheck))['pr'];
    $isPRBaru = $prLama === null || $beban > (float)$prLama;

    $stmt = mysqli_prepare($koneksi, "INSERT INTO catatan_latihan (tanggal, gerakan, kategori, beban, sets, reps, repetisi, catatan)
                                       VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sssdiiss", $tanggal, $gerakan, $kategori, $beban, $sets, $reps, $repetisi, $catatan);
    mysqli_stmt_execute($stmt);

    setFlash($isPRBaru && $prLama !== null ? "Mantap, PR baru untuk $gerakan! 🏆" : "Latihan berhasil dicatat.", 'sukses');
    header("Location: catat.php");
    exit;
}

$kategoriList = daftarKategori();
$gerakanUmum = daftarGerakanUmum();
$semuaGerakan = array_merge(...array_values($gerakanUmum));

include 'includes/header.php';
?>

<div class="card" style="max-width:640px; margin:0 auto;">
    <div class="card-head">
        <div>
            <h2>Catat Latihan Baru</h2>
            <div class="desc">Isi detail set kamu barusan</div>
        </div>
    </div>

    <form action="catat.php" method="POST">
        <div class="form-grid">
            <div class="field">
                <label>Tanggal</label>
                <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" required>
            </div>
            <div class="field">
                <label>Kategori</label>
                <select name="kategori" required>
                    <?php foreach ($kategoriList as $k): ?>
                        <option value="<?= e($k) ?>"><?= e($k) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="field" style="margin-top:14px;">
            <label>Gerakan</label>
            <input type="text" name="gerakan" list="gerakan-list" placeholder="Contoh: Goblet Squat" required>
            <datalist id="gerakan-list">
                <?php foreach ($semuaGerakan as $g): ?>
                    <option value="<?= e($g) ?>">
                <?php endforeach; ?>
            </datalist>
        </div>

        <div class="form-grid" style="margin-top:14px;">
            <div class="field">
                <label>Beban (kg)</label>
                <input type="number" step="0.5" min="0" name="beban" placeholder="0 jika bodyweight" required>
            </div>
            <div class="field">
                <label>Jumlah Set</label>
                <input type="number" min="1" name="sets" value="3" required>
            </div>
            <div class="field">
                <label>Repetisi per Set</label>
                <input type="number" min="0" name="reps" placeholder="12" required>
            </div>
        </div>

        <div class="field" style="margin-top:14px;">
            <label>Catatan (opsional)</label>
            <input type="text" name="catatan" placeholder="Contoh: RPE 8, form fokus tempo turun">
        </div>

        <button type="submit" name="simpan" class="btn btn-primary btn-block" style="margin-top:20px;">Simpan Latihan</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
