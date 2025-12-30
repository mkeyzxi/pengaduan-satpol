<!-- C:\xampp\htdocs\pengaduan\public\petugas\tindak.php -->
<?php
session_start();

require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/upload.php';

only_role(['petugas']);

$id = intval($_GET['id'] ?? 0);
$petugas_id = $_SESSION['user']['id'];

if ($id <= 0) {
    header("Location: pengaduan.php");
    exit;
}

/* =========================
   PROSES SIMPAN TINDAK LANJUT
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $status         = $_POST['status'];
    $prediksi_baru  = $_POST['prediksi_label']; // hasil konfirmasi petugas
    $catatan        = trim($_POST['catatan'] ?? '');
    $fileName       = null;

    // upload bukti (opsional)
    if (!empty($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
        $fileName = uploadFile($_FILES['bukti'], __DIR__ . "/../../public/uploads/");
    }

    // 1️⃣ update pengaduan (prediksi + status)
    $stmt = $pdo->prepare("
        UPDATE pengaduan
        SET prediksi_label = ?, status = ?
        WHERE id = ?
    ");
    $stmt->execute([
        $prediksi_baru,
        $status,
        $id
    ]);

    // 2️⃣ simpan histori tindak lanjut (SELALU dicatat)
    $stmt = $pdo->prepare("
        INSERT INTO tindak_lanjut_pengaduan
        (pengaduan_id, petugas_id, catatan, status_akhir, foto_bukti)
        VALUES (?,?,?,?,?)
    ");
    $stmt->execute([
        $id,
        $petugas_id,
        $catatan !== '' ? $catatan : 'Konfirmasi / koreksi klasifikasi AI',
        $status,
        $fileName
    ]);

    header("Location: pengaduan.php");
    exit;
}

/* =========================
   AMBIL DATA PENGADUAN
========================= */
$q = $pdo->prepare("
    SELECT p.*, u.nama 
    FROM pengaduan p
    LEFT JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$q->execute([$id]);
$pengaduan = $q->fetch();

if (!$pengaduan) {
    header("Location: pengaduan.php");
    exit;
}
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Proses Pengaduan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">

<a href="pengaduan.php" class="btn btn-secondary mb-3">← Kembali</a>

<h4>Pengaduan #<?= $pengaduan['id'] ?></h4>

<div class="mb-3">
    <b>Pelapor:</b><br>
    <?= htmlspecialchars($pengaduan['nama']) ?>
</div>

<div class="mb-4">
    <b>Deskripsi:</b><br>
    <?= nl2br(htmlspecialchars($pengaduan['deskripsi'])) ?>
</div>

<form method="post" enctype="multipart/form-data">

    <!-- =====================
         KONFIRMASI PREDIKSI AI
    ====================== -->
    <label class="fw-bold">Klasifikasi (Prediksi AI)</label>
    <select name="prediksi_label" class="form-control mb-3" required>
        <?php
        $opsi = ['Orang Bolos', 'Penertiban Pasar', 'Sapi Liar'];
        foreach ($opsi as $o):
        ?>
            <option value="<?= $o ?>"
                <?= $pengaduan['prediksi_label'] === $o ? 'selected' : '' ?>>
                <?= $o ?>
            </option>
        <?php endforeach; ?>
    </select>

    <!-- =====================
         STATUS PENGADUAN
    ====================== -->
    <label>Status</label>
    <select name="status" class="form-control mb-3" required>
        <option value="diproses" <?= $pengaduan['status']=='diproses'?'selected':'' ?>>Diproses</option>
        <option value="selesai" <?= $pengaduan['status']=='selesai'?'selected':'' ?>>Selesai</option>
        <option value="tidak sesuai" <?= $pengaduan['status']=='tidak sesuai'?'selected':'' ?>>Tidak Sesuai</option>
        <option value="ditolak" <?= $pengaduan['status']=='ditolak'?'selected':'' ?>>Ditolak</option>
    </select>

    <!-- =====================
         CATATAN PETUGAS
    ====================== -->
    <label>Catatan Petugas</label>
    <textarea name="catatan" class="form-control mb-3"
              placeholder="Tulis hasil tindak lanjut / alasan koreksi (opsional)"></textarea>

    <!-- =====================
         BUKTI FOTO
    ====================== -->
    <label>Bukti Tindak (Opsional)</label>
    <input type="file" name="bukti" class="form-control mb-4">

    <button class="btn btn-primary">Simpan Tindak Lanjut</button>

</form>

</body>
</html>

