<?php
session_start();
include '../includes/koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.html");
    exit;
}

$id = (int) $_GET['id'];

// 1. Ambil nama file gambar dulu sebelum datanya dihapus
$stmt_img = mysqli_prepare($conn, "SELECT gambar FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt_img, "i", $id);
mysqli_stmt_execute($stmt_img);
$result = mysqli_stmt_get_result($stmt_img);
$row = mysqli_fetch_assoc($result);

// 2. Hapus file gambar di folder images/
if ($row && !empty($row['gambar'])) {
    $gambar_path = '../images/' . $row['gambar'];
    if (file_exists($gambar_path)) {
        unlink($gambar_path);
    }
}

// 3. Hapus data dari database menggunakan Prepared Statement
$stmt_del = mysqli_prepare($conn, "DELETE FROM produk WHERE id = ?");
mysqli_stmt_bind_param($stmt_del, "i", $id);

if (mysqli_stmt_execute($stmt_del)) {
    echo "<script>alert('Produk berhasil dihapus!'); window.location='dashboard.php';</script>";
} else {
    echo "Gagal menghapus produk: " . mysqli_error($conn);
}
?>
