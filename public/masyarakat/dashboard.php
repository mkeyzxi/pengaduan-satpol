<!-- C:\xampp\htdocs\pengaduan\public\masyarakat\dashboard.php -->
<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';

only_role(['masyarakat']);

$title = "Dashboard Masyarakat";

$uid = $_SESSION['user']['id'];
$stmt = $pdo->prepare("SELECT * FROM pengaduan WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$uid]);
$reports = $stmt->fetchAll();

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<div class="p-6 max-w-6xl mx-auto">

  <?php if (isset($_GET['success'])): ?>
    <div class="bg-green-100 text-green-700 px-4 py-3 rounded mb-4">
      Pengaduan berhasil dikirim!
    </div>
  <?php endif; ?>

  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Riwayat Pengaduan Anda</h1>
    <a href="buat_pengaduan.php"
      class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      + Buat Pengaduan
    </a>
  </div>

  <div class="overflow-x-auto bg-white rounded shadow">
    <table class="w-full border-collapse">
      <thead class="bg-gray-200">
        <tr>
          <th class="p-3 text-left">ID</th>
          <th class="p-3 text-left">Deskripsi</th>
          <th class="p-3 text-left">Prediksi AI</th>
          <th class="p-3 text-left">Status</th>
          <th class="p-3 text-left">Waktu</th>
          <th class="p-3 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reports as $r): ?>

          <tr class="border-t hover:bg-gray-50">
            <td class="p-3"><?= $r['id'] ?></td>
            <td class="p-3"><?= htmlspecialchars($r['deskripsi']) ?></td>
            <td class="p-3">
              <span class="px-2 py-1 rounded bg-indigo-100 text-indigo-700">
                <?= htmlspecialchars($r['prediksi_label']) ?>
              </span>
            </td>
            <td class="p-3">
              <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-700">
                <?= htmlspecialchars($r['status']) ?>
              </span>
            </td>
            <td class="p-3"><?= $r['created_at'] ?></td>
            <td class="p-3">
              <a href="detail.php?id=<?= $r['id'] ?>"
                class="text-blue-600 hover:underline font-semibold">
                Detail
              </a>
            </td>
          </tr>

        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>