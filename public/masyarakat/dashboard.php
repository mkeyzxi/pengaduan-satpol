<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
only_role(['masyarakat']);

$uid = $_SESSION['user']['id'];
$rows = $pdo->prepare("SELECT p.*, k.nama as kategori_nama FROM pengaduan p LEFT JOIN kategori_pengaduan k ON p.kategori_id=k.id WHERE p.user_id=? ORDER BY p.created_at DESC");
$rows->execute([$uid]);
$reports = $rows->fetchAll();


?>

<?php if(isset($_GET['success'])): ?>
<div class="alert alert-success">Pengaduan berhasil dikirim!</div>
<?php endif; ?>

<!doctype html>
<html>
<head><title>Dashboard Masyarakat</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-3">
<a class="btn btn-secondary mb-2" href="../logout.php">Logout</a>
<h4>Riwayat Pengaduan Anda</h4>
<a class="btn btn-primary mb-3" href="buat_pengaduan.php">Buat Pengaduan Baru</a>

<table class="table table-striped">
<thead><tr><th>ID</th><th>Pesan Pengaduan</th><th>Label</th><th>Status</th><th>Waktu</th></tr></thead>
<tbody>
<?php foreach($reports as $r): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= htmlspecialchars($r['deskripsi']) ?></td>
<td><?= htmlspecialchars($r['prediksi_label']) ?></td>
<td><?= htmlspecialchars($r['status']) ?></td>
<td><?= $r['created_at'] ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</body>
</html>
