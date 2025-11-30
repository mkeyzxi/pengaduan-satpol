<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
only_role(['petugas']);

// filter params
$filter_status = $_GET['status'] ?? '';
$filter_label = $_GET['label'] ?? '';

// build query
$q = "SELECT p.*, u.nama, k.nama as kategori_nama FROM pengaduan p LEFT JOIN users u ON p.user_id=u.id LEFT JOIN kategori k ON p.kategori_id=k.id WHERE 1=1";
$params = [];

if ($filter_status) {
    $q .= " AND p.status = ?";
    $params[] = $filter_status;
}
if ($filter_label) {
    $q .= " AND p.prediksi_label = ?";
    $params[] = $filter_label;
}
$q .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($q);
$stmt->execute($params);
$rows = $stmt->fetchAll();

// get distinct predicted labels to show in filter
$labels = $pdo->query("SELECT DISTINCT prediksi_label FROM pengaduan WHERE prediksi_label IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
?>

<!doctype html>
<html>
<head><title>Pengaduan - Petugas</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-3">
<a class="btn btn-secondary mb-2" href="dashboard.php">← Kembali</a>
<h4>Daftar Pengaduan</h4>

<form method="get" class="row g-2 mb-3">
  <div class="col-md-3">
    <select name="status" class="form-control">
      <option value="">-- Semua Status --</option>
      <option value="diajukan" <?= $filter_status=='diajukan'?'selected':'' ?>>Diajukan</option>
      <option value="diterima" <?= $filter_status=='diterima'?'selected':'' ?>>Diterima</option>
      <option value="proses" <?= $filter_status=='proses'?'selected':'' ?>>Proses</option>
      <option value="selesai" <?= $filter_status=='selesai'?'selected':'' ?>>Selesai</option>
      <option value="ditolak" <?= $filter_status=='ditolak'?'selected':'' ?>>Ditolak</option>
    </select>
  </div>
  <div class="col-md-3">
    <select name="label" class="form-control">
      <option value="">-- Semua Label Prediksi --</option>
      <?php foreach ($labels as $l): ?>
        <option <?= $filter_label==$l?'selected':'' ?> value="<?= htmlspecialchars($l) ?>"><?= htmlspecialchars($l) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-md-3">
    <button class="btn btn-primary">Filter</button>
  </div>
</form>

<table class="table table-striped">
<thead><tr><th>ID</th><th>User</th><th>Kategori</th><th>Prediksi</th><th>Status</th><th>Waktu</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= htmlspecialchars($r['nama']) ?></td>
<td><?= htmlspecialchars($r['kategori_nama']) ?></td>
<td><?= htmlspecialchars($r['prediksi_label']) ?></td>
<td><?= htmlspecialchars($r['status']) ?></td>
<td><?= $r['created_at'] ?></td>
<td>
  <a class="btn btn-sm btn-info" href="tindak.php?id=<?= $r['id'] ?>">Detail/Proses</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</body>
</html>
