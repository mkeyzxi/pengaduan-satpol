<?php
session_start();
require "../../app/core/middleware.php";
require "../../app/config/database.php";
require "../../app/helpers/sanitize.php";

only_role(['masyarakat']);

$kategori = $pdo->query("SELECT * FROM kategori_pengaduan")->fetchAll();
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

<label>Kategori</label>
<select name="kategori" class="form-control mb-2">
<?php foreach($kategori as $k){ ?>
<option value="<?=$k['id']?>"><?=$k['nama']?></option>
<?php } ?>
</select>

<input name="lokasi" placeholder="Lokasi kejadian" class="form-control mb-2">

<textarea name="deskripsi" class="form-control mb-2" rows="4" placeholder="Deskripsikan kejadian"></textarea>

<input type="file" name="foto" class="form-control mb-2">

<button class="btn btn-primary w-100">Kirim Laporan</button>
</form>

</body>
</html>
