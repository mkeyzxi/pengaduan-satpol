<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require "../config/database.php";
require "../helpers/sanitize.php";
require "../core/middleware.php";
require "../ml/predict.php";

only_role(['masyarakat']);

// -----------------------
// DATA INPUT
// -----------------------
$user_id = $_SESSION['user']['id'];
$lokasi = clean($_POST['lokasi']);
$deskripsi = clean($_POST['deskripsi']);
$foto = null;

// -----------------------
// UPLOAD FOTO (Opsional)
// -----------------------
if (!empty($_FILES['foto']['name'])) {
    $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'mp4'];

    if (in_array($ext, $allowed)) {
        $filename = "foto_" . time() . "." . $ext;
        $uploadPath = "../../public/uploads/" . $filename;

        if (!is_dir("../../public/uploads")) {
            mkdir("../../public/uploads", 0777, true);
        }

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
            $foto = $filename;
        }
    }
}

// -----------------------
// PREDIKSI ML
// -----------------------
$prediksi = predict_text_label($deskripsi);

if (!$prediksi || $prediksi === "") {
    $prediksi = "unknown";
}

// -----------------------
// INSERT DATABASE
// -----------------------
$stmt = $pdo->prepare("
    INSERT INTO pengaduan (user_id, kategori_id, deskripsi, lokasi, foto, prediksi_label, status, created_at)
    VALUES (?, NULL, ?, ?, ?, ?, 'diajukan', NOW())
");

$stmt->execute([$user_id, $deskripsi, $lokasi, $foto, $prediksi]);

header("Location: ../../public/masyarakat/dashboard.php?success=1");
exit;
?>
