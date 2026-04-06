<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.html");
    exit;
}

$id_produk = (int) $_GET['id'];
$id_user   = (int) $_SESSION['id'];

$stmt_produk = mysqli_prepare($conn, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt_produk, "i", $id_produk);
mysqli_stmt_execute($stmt_produk);
$produk_result = mysqli_stmt_get_result($stmt_produk);
$produk = mysqli_fetch_assoc($produk_result);

if (!$produk) {
    echo "Produk tidak ditemukan.";
    exit;
}

if (isset($_POST['beli'])) {
    $metode       = $_POST['metode'];
    $total_produk = 1;

    if ($produk['stok'] < $total_produk) {
        echo "<script>alert('Maaf, stok habis!'); window.location='dashboard.php';</script>";
        exit;
    }

    $allowed_metode = ['cod', 'transfer'];
    if (!in_array($metode, $allowed_metode)) {
        echo "Metode pembayaran tidak valid.";
        exit;
    }

    $total_harga = $produk['harga'] * $total_produk;

    $stmt_order = mysqli_prepare($conn, "INSERT INTO pesanan (id_barang, total, id_user, metode_pembayaran, status_pembayaran) VALUES (?, ?, ?, ?, 'belum')");
    mysqli_stmt_bind_param($stmt_order, "iiis", $id_produk, $total_harga, $id_user, $metode);

    if (mysqli_stmt_execute($stmt_order)) {
        $stok_baru = $produk['stok'] - $total_produk;
        $stmt_stok = mysqli_prepare($conn, "UPDATE produk SET stok = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt_stok, "ii", $stok_baru, $id_produk);
        mysqli_stmt_execute($stmt_stok);

        echo "<script>alert('Berhasil memesan! Stok sisa: $stok_baru'); window.location='dashboard.php';</script>";
    } else {
        echo "Gagal membuat pesanan: " . mysqli_error($conn);
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Produk — Toko Baju</title>
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

    <div class="form-card">
        <h2>Beli Produk</h2>

        <div class="product-summary">
            <img src="../images/<?= htmlspecialchars($produk['gambar']) ?>" alt="<?= htmlspecialchars($produk['nama_barang']) ?>"
                alt="<?= htmlspecialchars($produk['nama_barang']) ?>" 
                class="current-img" 
                style="width:100%;max-width:480px;height:360px;object-fit:cover;border-radius:8px;">
            <div><br>
                <label for="prod-name"><b>Nama Produk</b></label>
                <div class="prod-name"><?= htmlspecialchars($produk['nama_barang']) ?></div><br>
                <label for="prod-desc"><b>Deskripsi</b></label>
                <div class="prod-desc"><?= htmlspecialchars($produk['deskripsi']) ?></div><br>
                <label for="prod-price"><b>Harga</b></label>
                <div class="prod-price">Rp <?= number_format($produk['harga'], 0, ',', '.') ?></div><br>
                <label for="prod-stock"><b>Stok</b></label>
                <div class="prod-stock">Stok tersedia: <?= $produk['stok'] ?></div><br>
            </div>
        </div>

        <form method="POST">
            <div class="form-group">
                <label for="metode">Metode Pembayaran</label>
                <select id="metode" name="metode" required>
                    <option value="">Pilih Metode</option>
                    <option value="transfer">Transfer Bank</option>
                    <option value="cod">Cash on Delivery (COD)</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" name="beli" class="btn btn-primary">Pesan Sekarang</button>
                <a href="dashboard.php">Kembali</a>
            </div>
        </form>
    </div>
</body>
</html>