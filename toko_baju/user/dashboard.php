<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.html");
    exit;
}

$query  = "SELECT * FROM produk WHERE stok > 0";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Toko Baju</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="dashboard-page">
    <nav class="topbar">
        <span class="brand-name">Toko Baju</span>
        <div class="topbar-right">
            <span class="greeting">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
            <a href="../logout.php" class="logout-link">Logout</a>
        </div>
    </nav>

    <div class="main-content">
        <div class="page-header">
            <h2>Produk Tersedia</h2>
        </div>

        <div class="product-grid">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="product-card">
                <img src="../images/<?= htmlspecialchars($row['gambar']) ?>" alt="<?= htmlspecialchars($row['nama_barang']) ?>">
                <div class="card-body">
                    <div class="card-name"><?= htmlspecialchars($row['nama_barang']) ?></div>
                    <div class="card-desc"><?= htmlspecialchars($row['deskripsi']) ?></div>
                    <div class="card-price">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                    <div class="card-stock">Stok: <?= $row['stok'] ?></div>
                    <a href="beli_produk.php?id=<?= $row['id'] ?>" class="link-beli" style="display:block;text-align:center;padding:8px;border-radius:8px;text-decoration:none;font-weight:500;font-size:0.9rem;">Beli Sekarang</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
