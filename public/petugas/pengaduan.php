<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
only_role(['petugas']);

$title = "Daftar Pengaduan";

$filter_status = $_GET['status'] ?? '';
$filter_label  = $_GET['label'] ?? '';

$q = "SELECT p.*, u.nama AS nama_user 
      FROM pengaduan p
      LEFT JOIN users u ON p.user_id = u.id
      WHERE 1=1";

$params = [];

if ($filter_status) {
  $q .= " AND p.status = ?";
  $params[] = $filter_status;
}

if ($filter_label) {
  $q .= " AND p.prediksi_label = ?";
  $params[] = $filter_label;
}

$q .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($q);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = $pdo->query("SELECT DISTINCT prediksi_label FROM pengaduan WHERE prediksi_label IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<main class="flex-1">
  <div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Daftar Pengaduan</h1>
        <p class="text-gray-600 mt-1">Kelola dan tindak lanjuti pengaduan masyarakat</p>
      </div>
      <a href="dashboard.php" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali
      </a>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-xl shadow-md p-6 mb-6">
      <form method="GET" class="flex flex-col sm:flex-row gap-4">
        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select name="status" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">-- Semua Status --</option>
            <?php
            $status_list = ['diajukan', 'diproses', 'selesai', 'tidak sesuai', 'ditolak'];
            foreach ($status_list as $st): ?>
              <option value="<?= $st ?>" <?= $filter_status == $st ? 'selected' : '' ?>><?= ucfirst($st) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="flex-1">
          <label class="block text-sm font-medium text-gray-700 mb-1">Label Klasifikasi</label>
          <select name="label" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <option value="">-- Semua Label AI --</option>
            <?php foreach ($labels as $l): ?>
              <option value="<?= htmlspecialchars($l) ?>" <?= $filter_label == $l ? 'selected' : '' ?>><?= htmlspecialchars($l) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="flex items-end">
          <button type="submit" class="w-full sm:w-auto bg-primary-600 hover:bg-primary-700 text-white font-medium px-6 py-2 rounded-lg transition-colors flex items-center justify-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            Filter
          </button>
        </div>
      </form>
    </div>

    <!-- Results Count -->
    <div class="mb-4 text-sm text-gray-600">
      Menampilkan <?= count($rows) ?> pengaduan
      <?php if ($filter_status || $filter_label): ?>
        <a href="pengaduan.php" class="text-primary-600 hover:underline ml-2">Reset filter</a>
      <?php endif; ?>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-primary-700 text-white">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold">ID</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Pelapor</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Klasifikasi</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Waktu</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php if (empty($rows)): ?>
              <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                  <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <p class="text-lg font-medium">Tidak ada data</p>
                  <p class="text-sm mt-1">Tidak ditemukan pengaduan dengan filter ini</p>
                </td>
              </tr>
            <?php endif; ?>

            <?php foreach ($rows as $r): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm font-medium text-gray-900">#<?= $r['id'] ?></td>
                <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($r['nama_user'] ?: '-') ?></td>
                <td class="px-6 py-4">
                  <?php if ($r['prediksi_label']): ?>
                    <span class="px-2 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-medium">
                      <?= htmlspecialchars($r['prediksi_label']) ?>
                    </span>
                  <?php else: ?>
                    <span class="px-2 py-1 bg-gray-100 text-gray-500 rounded-full text-xs font-medium">Unknown</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4">
                  <?php
                  $statusColors = [
                    'diajukan' => 'bg-yellow-100 text-yellow-700',
                    'diproses' => 'bg-blue-100 text-blue-700',
                    'selesai' => 'bg-green-100 text-green-700',
                    'tidak sesuai' => 'bg-orange-100 text-orange-700',
                    'ditolak' => 'bg-red-100 text-red-700'
                  ];
                  $statusClass = $statusColors[$r['status']] ?? 'bg-gray-100 text-gray-700';
                  ?>
                  <span class="px-2 py-1 rounded-full text-xs font-medium <?= $statusClass ?>">
                    <?= ucfirst(htmlspecialchars($r['status'])) ?>
                  </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                  <?= date('d M Y, H:i', strtotime($r['created_at'])) ?>
                </td>
                <td class="px-6 py-4">
                  <a href="tindak.php?id=<?= $r['id'] ?>"
                    class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Proses
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>