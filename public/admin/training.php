<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/sanitize.php';
require __DIR__ . "/../../vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\IOFactory;

only_role(['admin']);

$title = "Data Latih AI";
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

  if ($_POST['action'] === 'add') {
    $text  = clean($_POST['text']);
    $label = clean($_POST['label']);

    $pdo->prepare("INSERT INTO training_data (text_data, label) VALUES (?, ?)")
      ->execute([$text, $label]);

    $message = 'Data latih berhasil ditambahkan!';
    $messageType = 'success';
  } elseif ($_POST['action'] === 'retrain') {
    exec("php " . __DIR__ . "/../../app/ml/train.php", $out, $rc);
    $message = 'Model berhasil dilatih ulang!';
    $messageType = 'success';
  } elseif ($_POST['action'] === 'import_excel') {
    $filePath = $_FILES['excel_file']['tmp_name'];
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();
    $imported = 0;

    foreach ($rows as $index => $row) {
      if ($index == 0) continue;
      $text   = $row[1];
      $label  = $row[2];

      if ($text && $label) {
        $pdo->prepare("INSERT INTO training_data (text_data, label) VALUES (?, ?)")
          ->execute([$text, $label]);
        $imported++;
      }
    }
    $message = "$imported data berhasil diimport!";
    $messageType = 'success';
  }
}

if (isset($_GET['del'])) {
  $pdo->prepare("DELETE FROM training_data WHERE id=?")->execute([intval($_GET['del'])]);
  header("Location: training.php?deleted=1");
  exit;
}

// Search & Pagination
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 10;
$offset = ($page - 1) * $perPage;

// Build query with search
$whereClause = '';
$params = [];
if ($search !== '') {
  $whereClause = " WHERE (text_data LIKE ? OR label LIKE ?)";
  $searchParam = "%{$search}%";
  $params = [$searchParam, $searchParam];
}

// Get total count
$countQuery = "SELECT COUNT(*) FROM training_data" . $whereClause;
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $perPage);

// Get paginated data
$dataQuery = "SELECT * FROM training_data" . $whereClause . " ORDER BY id DESC LIMIT " . intval($perPage) . " OFFSET " . intval($offset);
$dataStmt = $pdo->prepare($dataQuery);
$dataStmt->execute($params);
$data = $dataStmt->fetchAll();

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<main class="flex-1">
  <div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Data Latih AI</h1>
        <p class="text-gray-600 mt-1">Kelola data training untuk klasifikasi pengaduan</p>
      </div>

    </div>

    <!-- Alerts -->
    <?php if ($message): ?>
      <div class="<?= $messageType === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' ?> border px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span><?= $message ?></span>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span>Data berhasil dihapus!</span>
      </div>
    <?php endif; ?>

    <!-- Action Cards Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

      <!-- Add Training Data -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-100">
          <h2 class="text-lg font-semibold text-gray-800 flex items-center">

            Tambah Data Latih
          </h2>
        </div>
        <div class="p-6">
          <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Teks Pengaduan</label>
              <textarea name="text" rows="3" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
                placeholder="Masukkan contoh teks pengaduan..."></textarea>
            </div>
            <!-- <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Label Klasifikasi</label>
              <input type="text" name="label" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                placeholder="Contoh: Orang Bolos">
              <select name="label" required>
                <option value="" disabled selected>Pilih Label</option>
                <option value="Orang Bolos">Orang Bolos</option>
                <option value="Penertiban Pasar">Penertiban Pasar</option>
                <option value="Sapi Liar">Sapi Liar</option>

              </select>
            </div> -->

            <div>
  <label class="block text-sm font-medium text-gray-700 mb-2">Label Klasifikasi</label>
  
  <!-- Input Text
  <input type="text" name="label_custom" required
    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors mb-4"
    placeholder="Contoh: Orang Bolos"> -->

  <!-- Selectt -->
  <select name="label" required
    class="w-full border border-gray-300 rounded-lg px-4 py-2 bg-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors appearance-none cursor-pointer">
    <option value="" disabled selected>Pilih Label</option>
    <option value="Orang Bolos">Orang Bolos</option>
    <option value="Penertiban Pasar">Penertiban Pasar</option>
    <option value="Sapi Liar">Sapi Liar</option>
  </select>
</div>
            <button type="submit"
              class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2.5 rounded-lg transition-colors">
              Tambah Data
            </button>
          </form>
        </div>
      </div>

      <!-- Import Excel -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
          <h2 class="text-lg font-semibold text-gray-800 flex items-center">
            Import Excel
          </h2>
        </div>
        <div class="p-6">
          <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="hidden" name="action" value="import_excel">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">File Excel (.xlsx)</label>
              <input type="file" name="excel_file" accept=".xlsx" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
            </div>
            <p class="text-xs text-gray-500">Format: No | Text | Label</p>
            <button type="submit"
              class="w-full bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2.5 rounded-lg transition-colors">
              Upload Excel
            </button>
          </form>
        </div>
      </div>

      <!-- Retrain Model -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
          <h2 class="text-lg font-semibold text-gray-800 flex items-center">
            Latih Ulang Model
          </h2>
        </div>
        <div class="p-6">
          <p class="text-sm text-gray-600 mb-4">Setelah menambahkan data baru, latih ulang model untuk meningkatkan akurasi klasifikasi.</p>
          <form method="POST">
            <input type="hidden" name="action" value="retrain">
            <button type="submit"
              class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2.5 rounded-lg transition-colors flex items-center justify-center">
              Retrain Model
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <h2 class="text-lg font-semibold text-gray-800">Data Latih (<?= $totalItems ?> total)</h2>
          <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
              placeholder="Cari teks atau label..."
              class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-64">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
            <?php if ($search): ?>
              <a href="training.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition-colors"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <line x1="6" y1="6" x2="18" y2="18" stroke="white" stroke-width="2" stroke-linecap="round" />
                  <line x1="18" y1="6" x2="6" y2="18" stroke="white" stroke-width="2" stroke-linecap="round" />
                </svg></a>
            <?php endif; ?>
          </form>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-primary-600 text-white">
            <tr>
              <th class="px-6 py-3 text-left text-sm font-semibold w-16">ID</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Teks</th>
              <th class="px-6 py-3 text-left text-sm font-semibold w-40">Label</th>
              <th class="px-6 py-3 text-left text-sm font-semibold w-24">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php if (empty($data)): ?>
              <tr>
                <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                  Belum ada data latih
                </td>
              </tr>
            <?php endif; ?>
            <?php foreach ($data as $r): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-700"><?= $r['id'] ?></td>
                <td class="px-6 py-4 text-sm text-gray-700">
                  <div class="max-w-lg truncate"><?= htmlspecialchars($r['text_data']) ?></div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-1 bg-primary-100 text-primary-700 rounded-full text-xs font-medium">
                    <?= htmlspecialchars($r['label']) ?>
                  </span>
                </td>
                <td class="px-6 py-4">
                  <a href="?del=<?= $r['id'] ?>"
                    onclick="return confirm('Hapus data ini?')"
                    class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Hapus
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
              Menampilkan <?= count($data) ?> dari <?= $totalItems ?> data (Halaman <?= $page ?> dari <?= $totalPages ?>)
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