<?php
session_start();

include __DIR__ . '/includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.html");
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    if (password_verify($password, $row['password'])) {
        $_SESSION['id']       = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role']     = $row['role'];

        if ($row['role'] === 'admin') {
            header("Location: admin/dashboard.php");
            exit;
        } elseif ($row['role'] === 'user') {
            header("Location: user/dashboard.php");
            exit;
        } else {
            echo "Role tidak valid!<br>";
            echo "<a href='login.html'>Coba Lagi</a>";
        }
    } else {
        echo "Login gagal! Password salah.<br>";
        echo "<a href='login.html'>Coba Lagi</a>";
    }
} else {
    echo "Login gagal! Username tidak ditemukan.<br>";
    echo "<a href='login.html'>Coba Lagi</a>";
}

$stmt->close();
$conn->close();
?>
