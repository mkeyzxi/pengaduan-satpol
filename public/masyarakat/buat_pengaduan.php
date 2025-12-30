<!-- C:\xampp\htdocs\pengaduan\public\masyarakat\buat_pengaduan.php -->
<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/sanitize.php';

only_role(['masyarakat']);
$title = "Buat Pengaduan";

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<div class="p-6 max-w-xl mx-auto">

    <a href="dashboard.php" class="text-blue-600 hover:underline mb-4 inline-block">
        ← Kembali
    </a>

    <h1 class="text-2xl font-bold mb-4">Buat Pengaduan Baru</h1>

    <form action="../../app/controllers/pengaduanController.php"
          method="POST"
          enctype="multipart/form-data"
          class="bg-white p-6 rounded shadow">

        <input
            name="lokasi"
            placeholder="Lokasi kejadian"
            class="w-full border p-2 rounded mb-3"
            required>

        <textarea
            name="deskripsi"
            rows="4"
            placeholder="Deskripsikan kejadian"
            class="w-full border p-2 rounded mb-3"
            required></textarea>

        <label class="block mb-1 font-medium">Foto (opsional)</label>
        <input
            type="file"
            name="foto"
            class="w-full border p-2 rounded mb-4">

        <button
            class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
            Kirim Laporan
        </button>
    </form>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
