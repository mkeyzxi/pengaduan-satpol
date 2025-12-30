<!-- C:\xampp\htdocs\pengaduan\public\petugas\pengaduan.php -->
<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
only_role(['petugas']);

$filter_status = $_GET['status'] ?? '';
$filter_label  = $_GET['label'] ?? '';

$q = "SELECT p.*, u.nama AS nama_user 
      FROM pengaduan p
      LEFT JOIN users u ON p.user_id = u.id
      WHERE 1=1";

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
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = $pdo->query("SELECT DISTINCT prediksi_label FROM pengaduan WHERE prediksi_label IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
?>
<!doctype html>
<html>
<head>
  <title>Manajemen Pengaduan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-3">

<a class="btn btn-secondary mb-2" href="dashboard.php">← Kembali</a>
<h4>Daftar Pengaduan</h4>

<form method="get" class="row g-2 mb-3">

  <div class="col-md-3">
    <select name="status" class="form-control">
      <option value="">-- Semua Status --</option>
      <?php
      $status_list = ['diajukan','diproses','selesai','tidak sesuai','ditolak'];
      foreach ($status_list as $st): ?>
        <option value="<?= $st ?>" <?= $filter_status == $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-3">
    <select name="label" class="form-control">
      <option value="">-- Semua Label AI --</option>
      <?php foreach ($labels as $l): ?>
        <option value="<?= htmlspecialchars($l) ?>" <?= $filter_label == $l ? 'selected' : '' ?>><?= htmlspecialchars($l) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-md-3">
    <button class="btn btn-primary">Filter</button>
  </div>
</form>

<table class="table table-bordered table-striped">
<thead class="table-dark">
<tr>
  <th>ID</th>
  <th>User</th>
  <th>Klasifikasi</th>
  <th>Status</th>
  <th>Waktu</th>
  <th>Aksi</th>
</tr>
</thead>

<tbody>
<?php if (empty($rows)): ?>
  <tr><td colspan="6" class="text-center text-muted">Tidak ada data</td></tr>
<?php endif; ?>

<?php foreach ($rows as $r): ?>
<tr>
  <td><?= $r['id'] ?></td>
  <td><?= htmlspecialchars($r['nama_user'] ?: '-') ?></td>
  <td><?= $r['prediksi_label'] ?: '<span class="badge bg-secondary">Unknown</span>' ?></td>
  <td><span class="badge bg-warning text-dark"><?= htmlspecialchars($r['status']) ?></span></td>
  <td><?= $r['created_at'] ?></td>
  <td><a class="btn btn-sm btn-success" href="tindak.php?id=<?= $r['id'] ?>">Proses</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

</body>
</html>
