<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/sanitize.php';
only_role(['admin']);

$title = "Kelola Users";

// add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
  $nama  = clean($_POST['nama']);
  $email = clean($_POST['email']);
  $pass  = $_POST['password'];
  $role  = $_POST['role'];

  $hash = password_hash($pass, PASSWORD_BCRYPT);

  $check = $pdo->prepare("SELECT id FROM users WHERE email=?");
  $check->execute([$email]);

  if ($check->rowCount() > 0) {
    header("Location: users.php?error=email_exists");
    exit;
  }

  $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
  $stmt->execute([$nama, $email, $hash, $role]);

  header("Location: users.php?success=added");
  exit;
}

// delete
if (isset($_GET['del'])) {
  $id = intval($_GET['del']);
  $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
  header("Location: users.php?success=deleted");
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
  $whereClause = " WHERE (nama LIKE ? OR email LIKE ?)";
  $searchParam = "%{$search}%";
  $params = [$searchParam, $searchParam];
}

// Get total count
$countQuery = "SELECT COUNT(*) FROM users" . $whereClause;
$countStmt = $pdo->prepare($countQuery);
$countStmt->execute($params);
$totalItems = $countStmt->fetchColumn();
$totalPages = ceil($totalItems / $perPage);

// Get paginated data
$dataQuery = "SELECT * FROM users" . $whereClause . " ORDER BY id DESC LIMIT " . intval($perPage) . " OFFSET " . intval($offset);
$dataStmt = $pdo->prepare($dataQuery);
$dataStmt->execute($params);
$users = $dataStmt->fetchAll();

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<main class="flex-1">
  <div class="max-w-7xl mx-auto px-4 py-8">

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Kelola Users</h1>
        <p class="text-gray-600 mt-1">Manajemen pengguna sistem</p>
      </div>

    </div>
    <!-- Alerts -->
    <?php if (isset($_GET['success'])): ?>
      <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <span>Operasi berhasil!</span>
      </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && $_GET['error'] === 'email_exists'): ?>
      <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <span>Email sudah digunakan!</span>
      </div>
    <?php endif; ?>

    <!-- Add User Form -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden ">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h2 class="text-lg font-semibold text-gray-800">Tambah User Baru</h2>
      </div>
      <div class="p-6">
        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <input type="hidden" name="action" value="add">

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama</label>
            <input type="text" name="nama" required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
              placeholder="masukkan nama lengkap">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
            <input type="email" name="email" required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
              placeholder="masukkan email">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <input type="password" name="password" required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
              placeholder="masukkan password">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
            <select name="role" required
              class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
              <option value="masyarakat">Masyarakat</option>
              <option value="petugas">Petugas</option>
              <!-- <option value="admin">Admin</option> -->
            </select>
          </div>

          <div class="md:col-span-2">
            <button type="submit"
              class="w-full sm:w-auto bg-primary-600 hover:bg-primary-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-colors flex items-center justify-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
              </svg>
              Tambah User
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mt-8">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <h2 class="text-lg font-semibold text-gray-800">Daftar Users (<?= $totalItems ?> total)</h2>
          <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
              placeholder="Cari nama atau email..."
              class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500 w-64">
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm transition-colors">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </button>
            <?php if ($search): ?>
              <a href="users.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm transition-colors"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
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
              <th class="px-6 py-3 text-left text-sm font-semibold">ID</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Nama</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Email</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Role</th>
              <th class="px-6 py-3 text-left text-sm font-semibold">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200">
            <?php foreach ($users as $u): ?>
              <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 text-sm text-gray-700"><?= $u['id'] ?></td>
                <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= htmlspecialchars($u['nama']) ?></td>
                <td class="px-6 py-4 text-sm text-gray-700"><?= htmlspecialchars($u['email']) ?></td>
                <td class="px-6 py-4">
                  <?php
                  $roleColors = [
                    'admin' => 'bg-purple-100 text-purple-700',
                    'petugas' => 'bg-blue-100 text-blue-700',
                    'masyarakat' => 'bg-green-100 text-green-700'
                  ];
                  $colorClass = $roleColors[$u['role']] ?? 'bg-gray-100 text-gray-700';
                  ?>
                  <span class="px-2 py-1 rounded-full text-xs font-medium <?= $colorClass ?>">
                    <?= ucfirst($u['role']) ?>
                  </span>
                </td>
                <td class="px-6 py-4">
                  <?php if ($u['role'] !== 'admin'): ?>
                    <a href="?del=<?= $u['id'] ?>"
                      onclick="return confirm('Hapus user ini?')"
                      class="inline-flex items-center px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                      Hapus
                    </a>

                  <?php endif; ?>
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
              Menampilkan <?= count($users) ?> dari <?= $totalItems ?> data (Halaman <?= $page ?> dari <?= $totalPages ?>)
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