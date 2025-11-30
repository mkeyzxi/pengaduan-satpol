<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/upload.php';
require __DIR__ . '/../../app/helpers/sanitize.php';
only_role(['petugas']);

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) { header("Location: pengaduan.php"); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $catatan = $_POST['catatan'] ?? '';
    $bukti = null;
    if (!empty($_FILES['bukti']) && $_FILES['bukti']['error']===UPLOAD_ERR_OK) {
        $bukti = uploadFile($_FILES['bukti'], __DIR__ . '/../../public/uploads/');
    }
    $pdo->prepare("UPDATE pengaduan SET status=?, created_at=created_at WHERE id=?")->execute([$status,$id]);
    // Simpan catatan / bukti ke tabel lain jika perlu — buat sederhana: append ke kolom deskripsi
    if ($catatan || $bukti) {
        $r = $pdo->prepare("UPDATE pengaduan SET deskripsi = CONCAT(deskripsi, '\n\n[Catatan Petugas]: ', ?, '\n[Foto Bukti]: ', ?) WHERE id = ?");
        $r->execute([$catatan, $bukti, $id]);
    }
    header("Location: pengaduan.php");
    exit;
}

$report = $pdo->prepare("SELECT p.*, u.nama FROM pengaduan p LEFT JOIN users u ON p.user_id=u.id WHERE p.id=?");
$report->execute([$id]);
$r = $report->fetch();
if (!$r) { header("Location: pengaduan.php"); exit; }
?>
<!doctype html>
<html>
<head><title>Proses Pengaduan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-3">
<a href="pengaduan.php" class="btn btn-secondary mb-2">← Kembali</a>
<h4>Pengaduan #<?= $r['id'] ?></h4>
<p><b>Pelapor:</b> <?= htmlspecialchars($r['nama']) ?></p>
<p><b>Deskripsi:</b><br><?= nl2br(htmlspecialchars($r['deskripsi'])) ?></p>

<form method="post" enctype="multipart/form-data">
<select name="status" class="form-control mb-2">
<option value="diterima">Diterima</option>
<option value="proses">Proses</option>
<option value="selesai">Selesai</option>
<option value="ditolak">Ditolak</option>
</select>
<textarea name="catatan" class="form-control mb-2" placeholder="Catatan / tindakan yang dilakukan"></textarea>
<input type="file" name="bukti" class="form-control mb-2">
<button class="btn btn-primary">Simpan</button>
</form>
</body>
</html>
