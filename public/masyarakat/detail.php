<?php
session_start();

require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';

only_role(['masyarakat']);

/* =========================
   AMBIL ID DARI URL
========================= */
$id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user']['id'];

if ($id <= 0) {
    header("Location: dashboard.php");
    exit;
}

/* =========================
   AMBIL DETAIL PENGADUAN
   (HANYA MILIK USER INI)
========================= */
$stmt = $pdo->prepare("
    SELECT *
    FROM pengaduan
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$id, $user_id]);
$pengaduan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pengaduan) {
    echo "<div style='padding:20px'>Pengaduan tidak ditemukan.</div>";
    exit;
}

/* =========================
   AMBIL RIWAYAT TINDAK LANJUT
========================= */
$stmt = $pdo->prepare("
    SELECT 
        t.*, 
        u.nama AS nama_petugas
    FROM tindak_lanjut_pengaduan t
    LEFT JOIN users u ON t.petugas_id = u.id
    WHERE t.pengaduan_id = ?
    ORDER BY t.tanggal_update DESC
");
$stmt->execute([$id]);
$tindak_lanjut = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pengaduan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-4">

<a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

<h4>Detail Pengaduan #<?= $pengaduan['id'] ?></h4>

<!-- =========================
     DETAIL PENGADUAN
========================= -->
<div class="card mb-4">
    <div class="card-body">
        <p><b>Deskripsi:</b><br>
            <?= nl2br(htmlspecialchars($pengaduan['deskripsi'])) ?>
        </p>

        <p><b>Prediksi AI:</b>
            <span class="badge bg-info">
                <?= htmlspecialchars($pengaduan['prediksi_label']) ?>
            </span>
        </p>

        <p><b>Status:</b>
            <span class="badge bg-secondary">
                <?= htmlspecialchars($pengaduan['status']) ?>
            </span>
        </p>

        <p><b>Tanggal Lapor:</b>
            <?= date('d-m-Y H:i', strtotime($pengaduan['created_at'])) ?>
        </p>
    </div>
</div>

<!-- =========================
     RIWAYAT TINDAK LANJUT
========================= -->
<h5>Riwayat Tindak Lanjut Petugas</h5>

<?php if (empty($tindak_lanjut)): ?>
    <div class="alert alert-warning">
        Belum ada tindak lanjut dari petugas.
    </div>
<?php else: ?>

<table class="table table-bordered table-striped">
    <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Petugas</th>
            <th>Catatan</th>
            <th>Status</th>
            <th>Bukti</th>
            <th>Waktu</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($tindak_lanjut as $i => $tl): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($tl['nama_petugas'] ?? '-') ?></td>
            <td><?= nl2br(htmlspecialchars($tl['catatan'])) ?></td>
            <td><?= htmlspecialchars($tl['status_akhir']) ?></td>
            <td>
                <?php if (!empty($tl['foto_bukti'])): ?>
                    <a href="../uploads/<?= htmlspecialchars($tl['foto_bukti']) ?>"
                       target="_blank"
                       class="btn btn-sm btn-success">
                        Lihat
                    </a>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td>
                <?= date('d-m-Y H:i', strtotime($tl['tanggal_update'])) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php endif; ?>

</body>
</html>
