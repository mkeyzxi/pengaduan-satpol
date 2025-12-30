<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/sanitize.php';
only_role(['admin']);

$title = "Kelola Kategori";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
  $nama = clean($_POST['nama']);
  $pdo->prepare("INSERT INTO kategori_pengaduan (nama) VALUES (?)")->execute([$nama]);
  header("Location: categories.php?success=1");
  exit;
}

if (isset($_GET['del'])) {
  $id = intval($_GET['del']);
  $pdo->prepare("DELETE FROM kategori_pengaduan WHERE id = ?")->execute([$id]);
  header("Location: categories.php?deleted=1");
  exit;
}

$cats = $pdo->query("SELECT * FROM kategori_pengaduan ORDER BY id DESC")->fetchAll();

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<main class="flex-1">
  <div class="max-w-4xl mx-auto px-4 py-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Kelola Kategori</h1>
        <p class="text-gray-600 mt-1">Manajemen kategori pengaduan</p>
      </div>
      <a href="dashboard.php" class="mt-4 sm:mt-0 inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Kembali
      </a>
    </div>

    <!-- Alerts -->
    <?php if (isset($_GET['success'])): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span>Kategori berhasil ditambahkan!</span>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span>Kategori berhasil dihapus!</span>
      </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

      <!-- Categories List -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
          <h2 class="text-lg font-semibold text-gray-800">Daftar Kategori</h2>
        </div>
        <div class="p-6">
          <?php if (empty($cats)): ?>
            <div class="text-center py-8 text-gray-500">
              <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
              </svg>
              <p>Belum ada kategori</p>
            </div>
          <?php else: ?>
            <ul class="space-y-3">
              <?php foreach ($cats as $c): ?>
                <li class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                  <div class="flex items-center">
                    <span class="w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mr-3">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                      </svg>
                    </span>
                    <span class="font-medium text-gray-700"><?= htmlspecialchars($c['nama']) ?></span>
                  </div>
                  <a href="?del=<?= $c['id'] ?>"
                    onclick="return confirm('Hapus kategori ini?')"
                    class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                    Hapus
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>

      <!-- Add Category Form -->
      <div class="bg-white rounded-xl shadow-md overflow-hidden h-fit">
        <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
          <h2 class="text-lg font-semibold text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Tambah Kategori
          </h2>
        </div>
        <div class="p-6">
          <form method="POST" class="space-y-4">
            <input type="hidden" name="action" value="add">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kategori</label>
              <input type="text" name="nama" required
                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                placeholder="Contoh: Pelanggaran PKL">
            </div>
            <button type="submit"
              class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium px-4 py-2.5 rounded-lg transition-colors flex items-center justify-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Tambah Kategori
            </button>
          </form>
        </div>
      </div>

    </div>
  </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>