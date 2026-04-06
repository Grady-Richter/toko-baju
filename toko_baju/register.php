<?php
include __DIR__ . '/includes/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register.html");
    exit;
}

$username = trim($_POST['username']);
$password = $_POST['password'];
$role     = $_POST['role'];

$allowed_roles = ['user'];
if (!in_array($role, $allowed_roles)) {
    echo "Role tidak valid!<br>";
    echo "<a href='register.html'>Kembali</a>";
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $username, $hashed_password, $role);

if ($stmt->execute()) {
    echo "Registrasi berhasil!<br>";
    echo "<a href='login.html'>Login</a>";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>