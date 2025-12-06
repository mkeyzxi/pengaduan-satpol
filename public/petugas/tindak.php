<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/upload.php';
only_role(['petugas']);

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: pengaduan.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $status  = $_POST['status'];
    $koreksi = $_POST['koreksi'] ?? null;
    $catatan = $_POST['catatan'] ?? '';
    $fileName = null;

    if (!empty($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
        $fileName = uploadFile($_FILES['bukti'], __DIR__ . "/../../public/uploads/");
    }

    $pdo->prepare("UPDATE pengaduan SET prediksi_label=?, status=? WHERE id=?")
        ->execute([$koreksi, $status, $id]);

    if ($catatan || $fileName) {
        $pdo->prepare("UPDATE pengaduan SET deskripsi = CONCAT(deskripsi, '\n\n[Catatan Petugas]: ', ?, '\n[Bukti Foto]: ', ?) WHERE id=?")
            ->execute([$catatan, $fileName, $id]);
    }

    header("Location: pengaduan.php");
    exit;
}

$q = $pdo->prepare("SELECT p.*, u.nama FROM pengaduan p LEFT JOIN users u ON p.user_id=u.id WHERE p.id=?");
$q->execute([$id]);
$pengaduan = $q->fetch();

if (!$pengaduan) {
    header("Location: pengaduan.php");
    exit;
}
?>

<!doctype html>
<html>
<head>
    <title>Proses Pengaduan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">

<a href="pengaduan.php" class="btn btn-secondary mb-2">← Kembali</a>

<h4>Pengaduan #<?= $pengaduan['id'] ?></h4>
<p><b>Pelapor:</b> <?= htmlspecialchars($pengaduan['nama']) ?></p>
<p><b>Deskripsi:</b><br><?= nl2br(htmlspecialchars($pengaduan['deskripsi'])) ?></p>

<form method="post" enctype="multipart/form-data">

<label class="fw-bold">Prediksi AI / Koreksi:</label>
<input type="text" name="koreksi" value="<?= $pengaduan['prediksi_label'] ?>" class="form-control mb-3">

<label>Status</label>
<select name="status" class="form-control mb-2">
    <option value="diproses">Diproses</option>
    <option value="selesai">Selesai</option>
    <option value="tidak sesuai">Tidak Sesuai</option>
    <option value="ditolak">Ditolak</option>
</select>

<label>Catatan</label>
<textarea name="catatan" class="form-control mb-3"></textarea>

<label>Bukti Tindak (Opsional)</label>
<input type="file" name="bukti" class="form-control mb-3">

<button class="btn btn-primary">Update</button>
</form>

</body>
</html>
