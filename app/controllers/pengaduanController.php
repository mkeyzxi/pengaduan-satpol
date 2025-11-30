<?php
// app/controllers/pengaduanController.php
session_start();
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../helpers/upload.php';
require __DIR__ . '/../helpers/sanitize.php';
require __DIR__ . '/../ml/predict.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /public/masyarakat/buat_pengaduan.php");
    exit;
}

if (!isset($_SESSION['user'])) {
    header("Location: /public/login.php");
    exit;
}

$desc = clean($_POST['deskripsi'] ?? '');
$user_id = $_SESSION['user']['id'];
$kategori = intval($_POST['kategori'] ?? 0);
$lokasi = clean($_POST['lokasi'] ?? '');

$fotoName = null;
if (!empty($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $fotoName = uploadFile($_FILES['foto'], __DIR__ . '/../../public/uploads/');
}

// Prediksi label via ML helper
$prediksi = predict_text_label($desc);

// simpan
$stmt = $pdo->prepare("INSERT INTO pengaduan(user_id, kategori_id, deskripsi, lokasi, foto, prediksi_label, status) VALUES (?, ?, ?, ?, ?, ?, 'diajukan')");
$stmt->execute([$user_id, $kategori, $desc, $lokasi, $fotoName, $prediksi]);

header("Location: /public/masyarakat/dashboard.php");
exit;
