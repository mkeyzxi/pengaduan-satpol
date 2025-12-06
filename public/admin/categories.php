
<!-- C:\xampp\htdocs\pengaduan\public\admin\categories.php -->
<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/sanitize.php';
only_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $nama = clean($_POST['nama']);
    $pdo->prepare("INSERT INTO kategori_pengaduan (nama) VALUES (?)")->execute([$nama]);
    header("Location: categories.php");
    exit;
}

if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $pdo->prepare("DELETE FROM kategori_pengaduan WHERE id = ?")->execute([$id]);
    header("Location: categories.php");
    exit;
}

$cats = $pdo->query("SELECT * FROM kategori_pengaduan ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html>
<head><title>Kelola Kategori</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-3">
<a class="btn btn-secondary mb-2" href="dashboard.php">← Kembali</a>
<h4>Daftar Kategori</h4>
<ul class="list-group mb-3">
<?php foreach($cats as $c): ?>
<li class="list-group-item d-flex justify-content-between align-items-center">
  <?= htmlspecialchars($c['nama']) ?>
  <a class="btn btn-sm btn-danger" href="?del=<?= $c['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a>
</li>
<?php endforeach; ?>
</ul>

<h5>Tambah Kategori</h5>
<form method="post">
<input type="hidden" name="action" value="add">
<input class="form-control mb-2" name="nama" placeholder="Nama Kategori" required>
<button class="btn btn-primary">Tambah</button>
</form>
</body>
</html>
