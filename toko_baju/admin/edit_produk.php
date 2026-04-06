<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

$id = (int) $_GET['id'];

$query = mysqli_prepare($conn, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($query, "i", $id);
mysqli_stmt_execute($query);
$result = mysqli_stmt_get_result($query);
$p = mysqli_fetch_assoc($result);

if (!$p) {
    echo "Produk tidak ditemukan.";
    exit;
}

if (isset($_POST['update'])) {
    $nama_barang = trim($_POST['nama_barang']);
    $deskripsi   = trim($_POST['deskripsi']);
    $harga       = (int) $_POST['harga'];
    $stok        = (int) $_POST['stok'];
    $gambar_lama = $_POST['gambar_lama'];

    if ($_FILES['gambar']['name'] != '' && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $gambar = $_FILES['gambar']['name'];
        move_uploaded_file($_FILES['gambar']['tmp_name'], '../images/' . $gambar);
    } else {
        $gambar = $gambar_lama;
    }

    $stmt = mysqli_prepare($conn, "UPDATE produk SET nama_barang = ?, deskripsi = ?, harga = ?, stok = ?, gambar = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssiisi", $nama_barang, $deskripsi, $harga, $stok, $gambar, $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Produk berhasil diperbarui'); window.location.href='dashboard.php';</script>";
    } else {
        echo "Gagal memperbarui produk: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk — Toko Baju</title>
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
        <h2>Edit Produk</h2>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="gambar_lama" value="<?= htmlspecialchars($p['gambar']) ?>">

            <div class="form-group">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" value="<?= htmlspecialchars($p['nama_barang']) ?>" required>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="deskripsi" required><?= htmlspecialchars($p['deskripsi']) ?></textarea>
            </div>

            <div class="form-group">
                <label>Harga (Rp)</label>
                <input type="number" name="harga" value="<?= $p['harga'] ?>" required>
            </div>

            <div class="form-group">
                <label>Stok</label>
                <input type="number" name="stok" value="<?= $p['stok'] ?>" required>
            </div>

            <div class="form-group">
                <label>Gambar Sekarang</label><br>
                <img src="../images/<?= htmlspecialchars($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama_barang']) ?>" width="120" height="90" class="current-img">
            </div>

            <div class="form-group">
                <label>Ganti Gambar (opsional)</label>
                <input type="file" name="gambar" accept="image/*">
            </div>

            <div class="form-actions">
                <button type="submit" name="update" class="btn btn-primary" style="width:auto;">Simpan Perubahan</button>
                <a href="dashboard.php">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>
