<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

$query  = "SELECT * FROM produk";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin — Toko Baju</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-page">
    <nav class="topbar">
        <span class="brand-name">Toko Baju</span>
        <div class="topbar-right">
            <span class="greeting">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> (Admin)</span>
            <a href="../logout.php" class="logout-link">Logout</a>
        </div>
    </nav>

    <div class="main-content">
        <div class="page-header">
            <h2>Daftar Produk</h2>
            <a href="tambah_produk.php" class="btn btn-primary" style="width:auto;">+ Tambah Produk</a>
        </div>

        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Deskripsi</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td data-label="No"><?= $no++ ?></td>
                        <td data-label="Nama"><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td data-label="Deskripsi"><?= htmlspecialchars($row['deskripsi']) ?></td>
                        <td data-label="Harga">Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                        <td data-label="Stok"><?= $row['stok'] ?></td>
                        <td data-label="Gambar">
                            <img src="../images/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_barang']) ?>" width="80" height="60">
                        </td>
                        <td data-label="Aksi">
                            <div class="action-links">
                                <a href="edit_produk.php?id=<?= $row['id'] ?>" class="link-edit">Edit</a>
                                <a href="hapus_produk.php?id=<?= $row['id'] ?>" class="link-hapus" onclick="return confirm('Yakin hapus produk ini?')">Hapus</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
