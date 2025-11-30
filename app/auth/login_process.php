<?php
session_start();
require __DIR__ . "/../config/database.php";

// ambil input
$username = $_POST['username'];
$password = $_POST['password'];

// cek user
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    
    $_SESSION['user'] = [
        'id' => $user['id'],
        'nama' => $user['nama'],
        'role' => $user['role']
    ];

    // redirect sesuai role
    if ($user['role'] === 'admin') {
        header("Location: ../../public/admin/dashboard.php");
    } elseif ($user['role'] === 'petugas') {
        header("Location: ../../public/petugas/dashboard.php");
    } else {
        header("Location: ../../public/masyarakat/dashboard.php");
    }
    exit;
}

// jika gagal login
header("Location: ../../public/login.php?error=1");
exit;
