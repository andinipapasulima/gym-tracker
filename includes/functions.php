<?php
/**
 * WZONE GYM TRACKER — Fungsi bantuan
 */

// Daftar kategori otot
function daftarKategori() {
    return ['Dada', 'Punggung', 'Kaki', 'Bahu', 'Lengan', 'Core', 'Cardio', 'Lainnya'];
}

// Saran gerakan umum per kategori (untuk datalist di form)
function daftarGerakanUmum() {
    return [
        'Dada'     => ['Push-up', 'Bench Press', 'Chest Press Machine', 'Dumbbell Fly'],
        'Punggung' => ['Lat Pulldown', 'Dumbbell Row', 'Seated Cable Row', 'Pull-up'],
        'Kaki'     => ['Goblet Squat', 'Lunges', 'Leg Press', 'Romanian Deadlift', 'Calf Raise'],
        'Bahu'     => ['Overhead Press', 'Lateral Raise', 'Front Raise'],
        'Lengan'   => ['Bicep Curl', 'Tricep Pushdown', 'Hammer Curl'],
        'Core'     => ['Plank', 'Sit-up', 'Russian Twist', 'Hanging Leg Raise'],
        'Cardio'   => ['Treadmill', 'Sepeda Statis', 'Jump Rope'],
        'Lainnya'  => [],
    ];
}

// Ambil sets & reps dari beberapa kemungkinan sumber (kolom baru atau teks lama "3x12")
function parseSetsReps($row) {
    $sets = isset($row['sets']) ? (int)$row['sets'] : 0;
    $reps = isset($row['reps']) ? (int)$row['reps'] : 0;
    if ($sets > 0 && $reps > 0) {
        return [$sets, $reps];
    }
    // fallback: parse format lama "3x12"
    if (!empty($row['repetisi']) && stripos($row['repetisi'], 'x') !== false) {
        $parts = explode('x', strtolower($row['repetisi']));
        if (count($parts) === 2 && is_numeric(trim($parts[0])) && is_numeric(trim($parts[1]))) {
            return [(int)trim($parts[0]), (int)trim($parts[1])];
        }
    }
    return [$sets ?: 1, $reps ?: 0];
}

// Estimasi 1RM pakai formula Epley — dipakai untuk grafik progres kekuatan
function estimasi1RM($beban, $reps) {
    if ($reps <= 0) return (float)$beban;
    return round($beban * (1 + $reps / 30), 1);
}

// Pecah beban jadi kombinasi plat gym standar (warna IWF), untuk visual "barbell"
// Ini representasi visual, bukan hitungan literal alat di gym-nya.
function pecahPlat($totalKg) {
    $platTersedia = [
        25   => '#E63946', // merah
        20   => '#1D3557', // biru
        15   => '#F4B400', // kuning
        10   => '#2A9D8F', // hijau
        5    => '#EDEEF2', // putih
        2.5  => '#3A3D45', // hitam/gelap
    ];
    $sisi = $totalKg / 2; // beban dibagi 2 sisi barbel
    $hasil = [];
    foreach ($platTersedia as $berat => $warna) {
        $jumlah = 0;
        while ($sisi >= $berat && count($hasil) < 6) {
            $sisi -= $berat;
            $hasil[] = ['berat' => $berat, 'warna' => $warna];
            $jumlah++;
        }
    }
    return $hasil;
}

// Ambil Personal Record (beban tertinggi) untuk setiap gerakan
function ambilPR($koneksi) {
    $query = "SELECT gerakan, kategori, MAX(beban) AS pr_beban
              FROM catatan_latihan
              GROUP BY gerakan
              ORDER BY pr_beban DESC";
    $hasil = mysqli_query($koneksi, $query);
    $pr = [];
    while ($row = mysqli_fetch_assoc($hasil)) {
        $pr[$row['gerakan']] = $row;
    }
    return $pr;
}

// Hitung streak: berapa hari berturut-turut (termasuk hari ini/kemarin) ada catatan latihan
function hitungStreak($koneksi) {
    $query = "SELECT DISTINCT tanggal FROM catatan_latihan ORDER BY tanggal DESC";
    $hasil = mysqli_query($koneksi, $query);
    $tanggalList = [];
    while ($row = mysqli_fetch_assoc($hasil)) {
        $tanggalList[] = $row['tanggal'];
    }
    if (empty($tanggalList)) return 0;

    $streak = 0;
    $cursor = new DateTime();
    // Jika belum latihan hari ini, mulai hitung dari kemarin
    if ($tanggalList[0] !== $cursor->format('Y-m-d')) {
        $cursor->modify('-1 day');
    }
    foreach ($tanggalList as $tgl) {
        if ($tgl === $cursor->format('Y-m-d')) {
            $streak++;
            $cursor->modify('-1 day');
        } else {
            break;
        }
    }
    return $streak;
}

// Ringkasan statistik dashboard
function ringkasanDashboard($koneksi) {
    $data = [
        'sesi_bulan_ini' => 0,
        'total_volume_bulan_ini' => 0,
        'total_sesi' => 0,
        'hari_sejak_terakhir' => null,
    ];

    $bulanIni = date('Y-m');
    $q1 = "SELECT COUNT(DISTINCT tanggal) AS jml FROM catatan_latihan WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulanIni'";
    $r1 = mysqli_query($koneksi, $q1);
    $data['sesi_bulan_ini'] = (int)mysqli_fetch_assoc($r1)['jml'];

    $q2 = "SELECT COUNT(DISTINCT tanggal) AS jml FROM catatan_latihan";
    $r2 = mysqli_query($koneksi, $q2);
    $data['total_sesi'] = (int)mysqli_fetch_assoc($r2)['jml'];

    $q3 = "SELECT SUM(beban * sets * GREATEST(reps,1)) AS vol
           FROM catatan_latihan WHERE DATE_FORMAT(tanggal, '%Y-%m') = '$bulanIni'";
    $r3 = mysqli_query($koneksi, $q3);
    $data['total_volume_bulan_ini'] = (float)(mysqli_fetch_assoc($r3)['vol'] ?? 0);

    $q4 = "SELECT MAX(tanggal) AS terakhir FROM catatan_latihan";
    $r4 = mysqli_query($koneksi, $q4);
    $terakhir = mysqli_fetch_assoc($r4)['terakhir'];
    if ($terakhir) {
        $data['hari_sejak_terakhir'] = (new DateTime())->diff(new DateTime($terakhir))->days;
    }

    return $data;
}

// Helper flash message via session
function setFlash($pesan, $tipe = 'sukses') {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['flash'] = ['pesan' => $pesan, 'tipe' => $tipe];
}

function ambilFlash() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function e($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}
