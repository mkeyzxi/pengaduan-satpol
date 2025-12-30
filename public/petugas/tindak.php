<?php
session_start();

require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/upload.php';

only_role(['petugas']);

$title = "Proses Pengaduan";

$id = intval($_GET['id'] ?? 0);
$petugas_id = $_SESSION['user']['id'];

if ($id <= 0) {
  header("Location: pengaduan.php");
  exit;
}

// PROSES SIMPAN TINDAK LANJUT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $status         = $_POST['status'];
  $prediksi_baru  = $_POST['prediksi_label'];
  $catatan        = trim($_POST['catatan'] ?? '');
  $fileName       = null;

  // upload bukti (opsional)
  if (!empty($_FILES['bukti']) && $_FILES['bukti']['error'] === UPLOAD_ERR_OK) {
    $fileName = uploadFile($_FILES['bukti'], __DIR__ . "/../uploads/");
  }

  // update pengaduan (prediksi + status)
  $stmt = $pdo->prepare("
        UPDATE pengaduan
        SET prediksi_label = ?, status = ?
        WHERE id = ?
    ");
  $stmt->execute([$prediksi_baru, $status, $id]);

  // simpan histori tindak lanjut
  $stmt = $pdo->prepare("
        INSERT INTO tindak_lanjut_pengaduan
        (pengaduan_id, petugas_id, catatan, status_akhir, foto_bukti)
        VALUES (?,?,?,?,?)
    ");
  $stmt->execute([
    $id,
    $petugas_id,
    $catatan !== '' ? $catatan : 'Konfirmasi / koreksi klasifikasi AI',
    $status,
    $fileName
  ]);

  header("Location: pengaduan.php?processed=1");
  exit;
}

// AMBIL DATA PENGADUAN
$q = $pdo->prepare("
    SELECT p.*, u.nama 
    FROM pengaduan p
    LEFT JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$q->execute([$id]);
$pengaduan = $q->fetch();

if (!$pengaduan) {
  header("Location: pengaduan.php");
  exit;
}

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<main class="flex-1">
  <div class="max-w-3xl mx-auto px-4 py-8">

    <!-- Back Link -->
    <a href="pengaduan.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium mb-6 group">
      <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Daftar
    </a>

    <!-- Pengaduan Detail Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-6">
      <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <h1 class="text-xl font-bold text-gray-800">Pengaduan #<?= $pengaduan['id'] ?></h1>
      </div>

      <div class="p-6 space-y-4">
        <div>
          <label class="block text-sm font-medium text-gray-500 mb-1">Pelapor</label>
          <div class="flex items-center">
            <div class="w-10 h-10 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mr-3">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <span class="font-medium text-gray-800"><?= htmlspecialchars($pengaduan['nama']) ?></span>
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi Pengaduan</label>
          <p class="text-gray-800 bg-gray-50 rounded-lg p-4 border border-gray-200">
            <?= nl2br(htmlspecialchars($pengaduan['deskripsi'])) ?>
          </p>
        </div>

        <?php if (!empty($pengaduan['lokasi'])): ?>
          <div>
            <label class="block text-sm font-medium text-gray-500 mb-1">Lokasi</label>
            <p class="text-gray-700"><?= htmlspecialchars($pengaduan['lokasi']) ?></p>
          </div>
        <?php endif; ?>

        <?php if (!empty($pengaduan['foto'])): ?>
          <div>
            <label class="block text-sm font-medium text-gray-500 mb-2">Foto Lampiran</label>
            <img src="../uploads/<?= htmlspecialchars($pengaduan['foto']) ?>"
              alt="Foto pengaduan"
              class="rounded-lg max-w-sm border border-gray-200">
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Tindak Lanjut Form -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
        <h2 class="text-lg font-bold text-gray-800 flex items-center">
          <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
          </svg>
          Form Tindak Lanjut
        </h2>
      </div>

      <div class="p-6">
        <form method="POST" enctype="multipart/form-data" class="space-y-5">

          <!-- Klasifikasi AI -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Klasifikasi (Konfirmasi/Koreksi AI)
            </label>
            <select name="prediksi_label" required
              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
              <?php
              $opsi = ['Orang Bolos', 'Penertiban Pasar', 'Sapi Liar'];
              foreach ($opsi as $o):
              ?>
                <option value="<?= $o ?>" <?= $pengaduan['prediksi_label'] === $o ? 'selected' : '' ?>>
                  <?= $o ?>
                </option>
              <?php endforeach; ?>
            </select>
            <p class="text-xs text-gray-500 mt-1">Prediksi AI saat ini: <span class="font-medium"><?= htmlspecialchars($pengaduan['prediksi_label']) ?></span></p>
          </div>

          <!-- Status -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Status Pengaduan</label>
            <select name="status" required
              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
              <option value="diproses" <?= $pengaduan['status'] == 'diproses' ? 'selected' : '' ?>>Diproses</option>
              <option value="selesai" <?= $pengaduan['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
              <option value="tidak sesuai" <?= $pengaduan['status'] == 'tidak sesuai' ? 'selected' : '' ?>>Tidak Sesuai</option>
              <option value="ditolak" <?= $pengaduan['status'] == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
            </select>
          </div>

          <!-- Catatan -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Catatan Petugas</label>
            <textarea name="catatan" rows="4"
              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
              placeholder="Tuliskan hasil tindak lanjut atau alasan koreksi klasifikasi (opsional)"></textarea>
          </div>

          <!-- Bukti Foto -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Tindak Lanjut (Opsional)</label>
            <input type="file" name="bukti" accept="image/*"
              class="w-full border border-gray-300 rounded-lg px-4 py-2.5 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
          </div>

          <!-- Submit -->
          <button type="submit"
            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>Simpan Tindak Lanjut</span>
          </button>
        </form>
      </div>
    </div>

  </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>