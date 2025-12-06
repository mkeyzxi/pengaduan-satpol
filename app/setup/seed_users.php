<?php
require __DIR__ . "/../config/database.php";

$users = [
    ['Admin Satpol', 'admin@satpolpp.go.id', '123456', 'admin'],
    ['Petugas Lapangan', 'petugas@satpolpp.go.id', '123456', 'petugas'],
    ['Warga Contoh', 'user@test.com', '123456', 'masyarakat'],
];

foreach ($users as $u) {
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$u[1]]);

    if ($stmt->fetch()) {
        echo "❌ User {$u[1]} sudah ada, skip.\n";
        continue;
    }

    $stmt = $pdo->prepare("
        INSERT INTO users (nama, email, password, role)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $u[0],
        $u[1],
        password_hash($u[2], PASSWORD_DEFAULT),
        $u[3]
    ]);

    echo "✔ User {$u[1]} berhasil dibuat.\n";
}
