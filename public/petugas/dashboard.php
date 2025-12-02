<?php
session_start();
require "../../app/core/middleware.php";
require "../../app/config/database.php";

only_role(['petugas']);

$data = $pdo->query("SELECT COUNT(*) AS total FROM pengaduan WHERE status!='selesai'")->fetch();
?>

<!doctype html>
<html>
<head><title>Dashboard Petugas</title></head>
<body style="font-family:Arial;padding:20px;">
<h2>Dashboard Petugas</h2>
<p>Pengaduan yang harus ditindaklanjuti: <strong><?= $data['total'] ?></strong></p>
<a href="pengaduan.php">📌 Lihat Pengaduan</a> | <a href="../logout.php">Logout</a>
</body>
</html>
