<?php
session_start();
require "../../app/core/middleware.php";
require "../../app/config/database.php";
require "../../app/helpers/sanitize.php";

only_role(['masyarakat']);
?>

<html>
<head>
<title>Buat Pengaduan</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">

<a href="dashboard.php" class="btn btn-secondary mb-3">← Kembali</a>

<h3>Buat Pengaduan Baru</h3>

<form action="../../app/controllers/pengaduanController.php" method="POST" enctype="multipart/form-data">

<input name="lokasi" placeholder="Lokasi kejadian" class="form-control mb-2" required>

<textarea name="deskripsi" class="form-control mb-2" rows="4" placeholder="Deskripsikan kejadian" required></textarea>

<label class="mb-1">Foto (opsional)</label>
<input type="file" name="foto" class="form-control mb-3">

<button class="btn btn-primary w-100">Kirim Laporan</button>
</form>

</body>
</html>
