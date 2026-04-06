<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

if (isset($_POST['submit'])) {
    $nama_barang = trim($_POST['nama_barang']);
    $deskripsi   = trim($_POST['deskripsi']);
    $harga       = (int) $_POST['harga'];
    $stok        = (int) $_POST['stok'];
    $gambar      = $_FILES['gambar']['name'];

    if ($_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../images/' . $gambar);
    } else {
        echo "Gagal mengupload gambar!";
        exit;
    }

    $stmt = mysqli_prepare($conn, "INSERT INTO produk (nama_barang, deskripsi, harga, stok, gambar) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssiis", $nama_barang, $deskripsi, $harga, $stok, $gambar);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: dashboard.php");
        exit;
    } else {
        echo "Gagal menambah produk: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk — Toko Baju</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-page">
    <nav class="topbar">
        <span class="brand-name">Toko Baju</span>
        <div class="topbar-right">
            <span class="greeting">Admin Panel</span>
            <a href="../logout.php" class="logout-link">Logout</a>
        </div>
    </nav>

    <div class="form-card">
        <h2>Tambah Produk</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" placeholder="Nama produk" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" placeholder="Deskripsi produk" required></textarea>
            </div>

            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" placeholder="0" required>
            </div>

            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" placeholder="0" required>
            </div>

            <div class="form-group">
                <label>Gambar Produk</label>
                <input type="file" name="gambar" accept="image/*" required>
            </div>

            <div class="form-actions">
                <button type="submit" name="submit" class="btn btn-primary" style="width:auto;">Tambah Produk</button>
                <a href="dashboard.php">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>
