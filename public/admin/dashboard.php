<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
only_role(['admin']);

// statistik sederhana
$total_pengaduan = $pdo->query("SELECT COUNT(*) total FROM pengaduan")->fetch()['total'];
$total_users = $pdo->query("SELECT COUNT(*) total FROM users")->fetch()['total'];
$total_kategori = $pdo->query("SELECT COUNT(*) total FROM kategori_pengaduan")->fetch()['total'];
?>

<!doctype html>
<html>
<head><title>Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-3">
<h3>Admin Dashboard</h3>
<div class="mb-3">
<a class="btn btn-secondary" href="../logout.php">Logout</a>
</div>

<div class="row">
  <div class="col-md-4"><div class="card p-3">Total Pengaduan: <b><?= $total_pengaduan ?></b></div></div>
  <div class="col-md-4"><div class="card p-3">Total Users: <b><?= $total_users ?></b></div></div>
  <div class="col-md-4"><div class="card p-3">Total Kategori: <b><?= $total_kategori ?></b></div></div>
</div>

<hr>
<div class="mt-3">
  <a class="btn btn-primary" href="users.php">Kelola Users</a>
  <a class="btn btn-primary" href="categories.php">Kelola Kategori</a>
  <a class="btn btn-primary" href="training.php">Kelola Data Latih</a>
  <a class="btn btn-success" href="report.php">Laporan Grafik</a>
</div>
</body>
</html>
