<?php
// Panggil koneksi database
include 'koneksi.php';

// Logika untuk MENYIMPAN DATA (Create)
if (isset($_POST['simpan'])) {
    $tanggal = $_POST['tanggal'];
    $gerakan = $_POST['gerakan'];
    $beban = $_POST['beban'];
    $repetisi = $_POST['repetisi'];

    $query_insert = "INSERT INTO catatan_latihan (tanggal, gerakan, beban, repetisi) 
                     VALUES ('$tanggal', '$gerakan', '$beban', '$repetisi')";
    
    mysqli_query($koneksi, $query_insert);
    
    // Redirect ke halaman yang sama agar form kembali kosong
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WZone Gym Tracker</title>
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
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #cccccc;
            box-sizing: border-box;
            font-size: 14px;
        }
        .btn-simpan {
            width: 100%;
            background-color: #27ae60;
            color: #ffffff;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .btn-simpan:hover {
            background-color: #219150;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 10px;
        }
        th {
            background-color: #ecf0f1;
            color: #2c3e50;
        }
        .btn-hapus {
            background-color: #e74c3c;
            color: #ffffff;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 12px;
        }
        .btn-hapus:hover {
            background-color: #c0392b;
        }

        .btn-edit {
    background-color: #f39c12;
    color: #ffffff;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    font-size: 12px;
}
.btn-edit:hover {
    background-color: #d68910;
}
    </style>
</head>
<body>

<div class="container">
    <h2>Catatan Latihan Gym 🏋️‍♀️</h2>
    
    <form action="" method="POST">
        <div class="form-group">
            <label>Tanggal:</label>
            <input type="date" name="tanggal" required>
        </div>
        <div class="form-group">
            <label>Gerakan:</label>
            <select name="gerakan">
                <option value="Goblet Squat">Goblet Squat</option>
                <option value="Push-up">Push-up</option>
                <option value="Plank">Plank</option>
                <option value="Lat Pulldown">Lat Pulldown</option>
                <option value="Lunges">Lunges</option>
                <option value="Overhead Press">Overhead Press</option>
                <option value="Dumbbell Row">Dumbbell Row</option>
            </select>
        </div>
        <div class="form-group">
            <label>Beban (Kg):</label>
            <input type="number" name="beban" placeholder="Isi 0 jika tanpa beban" required min="0">
        </div>
        <div class="form-group">
            <label>Repetisi/Set:</label>
            <input type="text" name="repetisi" placeholder="Contoh: 3x12" required>
        </div>
        
        <button type="submit" name="simpan" class="btn-simpan">Simpan Latihan</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Gerakan</th>
                <th>Beban</th>
                <th>Repetisi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Logika untuk MENAMPILKAN DATA (Read)
            $query_tampil = "SELECT * FROM catatan_latihan ORDER BY tanggal DESC, id DESC";
            $hasil = mysqli_query($koneksi, $query_tampil);

            if (mysqli_num_rows($hasil) > 0) {
                while ($baris = mysqli_fetch_assoc($hasil)) {
                    echo "<tr>";
                    echo "<td>" . $baris['tanggal'] . "</td>";
                    echo "<td>" . $baris['gerakan'] . "</td>";
                    echo "<td>" . $baris['beban'] . " Kg</td>";
                    echo "<td>" . $baris['repetisi'] . "</td>";
                    // Tombol hapus mengirimkan ID lewat URL (Metode GET)
                   echo "<td>
        <a href='edit.php?id=" . $baris['id'] . "' class='btn-edit'>Edit</a> | 
        <a href='hapus.php?id=" . $baris['id'] . "' class='btn-hapus' onclick='return confirm(\"Yakin ingin menghapus catatan ini?\")'>Hapus</a>
      </td>";
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center;'>Belum ada catatan latihan.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

</body>
</html>