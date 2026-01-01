<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';

only_role(['masyarakat']);

$title = "Dashboard Masyarakat";

$uid = $_SESSION['user']['id'];
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build query with search
$baseQuery = "FROM pengaduan WHERE user_id = ?";
$params = [$uid];

if ($search !== '') {
  $baseQuery .= " AND deskripsi LIKE ?";
  $params[] = "%{$search}%";
}

// Get total count
$countStmt = $pdo->prepare("SELECT COUNT(*) " . $baseQuery);
$countStmt->execute($params);
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $perPage);

// Get paginated data
$dataQuery = "SELECT * " . $baseQuery . " ORDER BY created_at DESC LIMIT " . intval($perPage) . " OFFSET " . intval($offset);
$stmt = $pdo->prepare($dataQuery);
$stmt->execute($params);
$reports = $stmt->fetchAll();

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<main class="flex-1">
  <div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Success Alert -->
    <?php if (isset($_GET['success'])): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span>Pengaduan berhasil dikirim!</span>
      </div>
    <?php endif; ?>

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Riwayat Pengaduan</h1>
        <p class="text-gray-600 mt-1">Selamat datang, <?= htmlspecialchars($_SESSION['user']['nama']) ?>!</p>
      </div>
      <a href="buat_pengaduan.php"
        class="mt-4 sm:mt-0 inline-flex items-center px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-lg transition-colors">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
        </svg>
        Buat Pengaduan
      </a>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-xl shadow-md p-4 mb-6">
      <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
          placeholder="Cari deskripsi pengaduan..."
          class="flex-1 border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
        <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm transition-colors flex items-center">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          Cari
        </button>
        <?php if ($search): ?>
          <a href="dashboard.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition-colors">Reset</a>
        <?php endif; ?>
      </form>
    </div>

    <!-- Results Count -->
    <div class="mb-4 text-sm text-gray-600">
      Menampilkan <?= count($reports) ?> dari <?= $totalItems ?> pengaduan
    </div>

    <!-- Reports Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-primary-600 text-white">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold">ID</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Deskripsi</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Prediksi AI</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Waktu</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php if (empty($reports)): ?>
              <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                  <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                  </svg>
                  <p class="text-lg font-medium">Belum ada pengaduan</p>
                  <p class="text-sm mt-1">Silakan buat pengaduan baru</p>
                </td>
              </tr>
            <?php endif; ?>
            <?php foreach ($reports as $r): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-700 font-medium"><?= $r['id'] ?></td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  <div class="max-w-xs truncate"><?= htmlspecialchars($r['deskripsi']) ?></div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-medium">
                    <?= htmlspecialchars($r['prediksi_label']) ?>
                  </span>
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
                  <a href="detail.php?id=<?= $r['id'] ?>"
                    class="inline-flex items-center px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-medium rounded transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Detail
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($totalPages > 1): ?>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
          <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gray-600">
              Halaman <?= $page ?> dari <?= $totalPages ?>
            </p>
            <div class="flex gap-2">
              <?php
              $queryParams = [];
              if ($search) $queryParams['search'] = $search;
              ?>

              <?php if ($page > 1): ?>
                <a href="?<?= http_build_query(array_merge($queryParams, ['page' => $page - 1])) ?>"
                  class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition-colors">
                  &laquo; Prev
                </a>
              <?php endif; ?>

              <?php
              $startPage = max(1, $page - 2);
              $endPage = min($totalPages, $page + 2);
              for ($i = $startPage; $i <= $endPage; $i++):
              ?>
                <a href="?<?= http_build_query(array_merge($queryParams, ['page' => $i])) ?>"
                  class="px-4 py-2 <?= $i == $page ? 'bg-primary-600 text-white' : 'bg-gray-200 hover:bg-gray-300 text-gray-700' ?> rounded-lg text-sm transition-colors">
                  <?= $i ?>
                </a>
              <?php endfor; ?>

              <?php if ($page < $totalPages): ?>
                <a href="?<?= http_build_query(array_merge($queryParams, ['page' => $page + 1])) ?>"
                  class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg text-sm transition-colors">
                  Next &raquo;
                </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>

  </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>