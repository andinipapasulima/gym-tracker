<?php
// Panggil koneksi database
include 'koneksi.php';

// 1. AMBIL DATA LAMA (Read)
// Cek apakah ada ID di URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}
$id = $_GET['id'];

// Ambil data dari database berdasarkan ID
$query = "SELECT * FROM catatan_latihan WHERE id = '$id'";
$hasil = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($hasil);

// Jika data tidak ditemukan, kembalikan ke index
if (!$data) {
    header("Location: index.php");
    exit;
}

// 2. PROSES UPDATE DATA (Update)
// Cek apakah tombol "update" ditekan
if (isset($_POST['update'])) {
    $tanggal = $_POST['tanggal'];
    $gerakan = $_POST['gerakan'];
    $beban = $_POST['beban'];
    $repetisi = $_POST['repetisi'];

    // Query untuk menimpa data lama dengan data baru
    $query_update = "UPDATE catatan_latihan 
                     SET tanggal = '$tanggal', 
                         gerakan = '$gerakan', 
                         beban = '$beban', 
                         repetisi = '$repetisi' 
                     WHERE id = '$id'";
    
    mysqli_query($koneksi, $query_update);
    
    // Kembalikan ke halaman utama setelah sukses
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Catatan - WZone Gym Tracker</title>
    <style>
        /* CSS Dasar & Statis */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 20px;
            color: #333333;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 { text-align: center; color: #2c3e50; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input, select { 
            width: 100%; padding: 10px; border: 1px solid #cccccc; box-sizing: border-box; font-size: 14px; 
        }
        .btn-update {
            width: 100%; background-color: #f39c12; color: #ffffff; 
            padding: 12px; border: none; border-radius: 4px; font-size: 16px; 
            font-weight: bold; cursor: pointer; margin-top: 10px;
        }
        .btn-update:hover { background-color: #d68910; }
        .btn-batal {
            display: block; text-align: center; margin-top: 15px; 
            color: #7f8c8d; text-decoration: none; font-size: 14px;
        }
        .btn-batal:hover { color: #34495e; text-decoration: underline; }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Catatan Latihan ✏️</h2>
    
    <form action="" method="POST">
        <div class="form-group">
            <label>Tanggal:</label>
            <input type="date" name="tanggal" value="<?php echo $data['tanggal']; ?>" required>
        </div>
        
        <div class="form-group">
            <label>Gerakan:</label>
            <select name="gerakan">
                <option value="Goblet Squat" <?php if($data['gerakan'] == 'Goblet Squat') echo 'selected'; ?>>Goblet Squat</option>
                <option value="Push-up" <?php if($data['gerakan'] == 'Push-up') echo 'selected'; ?>>Push-up</option>
                <option value="Plank" <?php if($data['gerakan'] == 'Plank') echo 'selected'; ?>>Plank</option>
                <option value="Lat Pulldown" <?php if($data['gerakan'] == 'Lat Pulldown') echo 'selected'; ?>>Lat Pulldown</option>
                <option value="Lunges" <?php if($data['gerakan'] == 'Lunges') echo 'selected'; ?>>Lunges</option>
                <option value="Overhead Press" <?php if($data['gerakan'] == 'Overhead Press') echo 'selected'; ?>>Overhead Press</option>
                <option value="Dumbbell Row" <?php if($data['gerakan'] == 'Dumbbell Row') echo 'selected'; ?>>Dumbbell Row</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Beban (Kg):</label>
            <input type="number" name="beban" value="<?php echo $data['beban']; ?>" required min="0">
        </div>
        
        <div class="form-group">
            <label>Repetisi/Set:</label>
            <input type="text" name="repetisi" value="<?php echo $data['repetisi']; ?>" required>
        </div>
        
        <button type="submit" name="update" class="btn-update">Simpan Perubahan</button>
        <a href="index.php" class="btn-batal">Batal & Kembali</a>
    </form>
</div>

</body>
</html>