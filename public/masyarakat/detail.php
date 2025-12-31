<?php
session_start();

require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';

only_role(['masyarakat']);

$title = "Detail Pengaduan";

$id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user']['id'];

if ($id <= 0) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT *
    FROM pengaduan
    WHERE id = ? AND user_id = ?
");
$stmt->execute([$id, $user_id]);
$pengaduan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pengaduan) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->prepare("
    SELECT 
        t.*, 
        u.nama AS nama_petugas
    FROM tindak_lanjut_pengaduan t
    LEFT JOIN users u ON t.petugas_id = u.id
    WHERE t.pengaduan_id = ?
    ORDER BY t.tanggal_update DESC
");
$stmt->execute([$id]);
$tindak_lanjut = $stmt->fetchAll(PDO::FETCH_ASSOC);

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<main class="flex-1">
    <div class="max-w-4xl mx-auto px-4 py-8">

        <!-- Back Link -->
        <a href="dashboard.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium mb-6 group">
            <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Dashboard
        </a>

        <!-- Detail Card -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                <h1 class="text-xl font-bold text-gray-800">Pengaduan #<?= $pengaduan['id'] ?></h1>
                <?php
                $statusColors = [
                    'diajukan' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                    'diproses' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'selesai' => 'bg-green-100 text-green-700 border-green-200',
                    'tidak sesuai' => 'bg-orange-100 text-orange-700 border-orange-200',
                    'ditolak' => 'bg-red-100 text-red-700 border-red-200'
                ];
                $statusClass = $statusColors[$pengaduan['status']] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                ?>
                <span class="px-3 py-1 rounded-full text-sm font-medium border <?= $statusClass ?>">
                    <?= ucfirst(htmlspecialchars($pengaduan['status'])) ?>
                </span>
            </div>

            <div class="p-6 space-y-4">
                <!-- Deskripsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-500 mb-1">Deskripsi</label>
                    <p class="text-gray-800 bg-gray-50 rounded-lg p-4"><?= nl2br(htmlspecialchars($pengaduan['deskripsi'])) ?></p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Prediksi AI -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Klasifikasi</label>
                        <span class="inline-flex items-center px-3 py-1.5 bg-primary-100 text-primary-700 rounded-lg text-sm font-medium">

                            <?= htmlspecialchars($pengaduan['prediksi_label']) ?>
                        </span>
                    </div>

                    <!-- Tanggal Lapor -->
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Tanggal Lapor</label>
                        <span class="inline-flex items-center text-gray-700 text-sm">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <?= date('d F Y, H:i', strtotime($pengaduan['created_at'])) ?> WIB
                        </span>
                    </div>
                </div>

                <!-- Foto if exists -->
                <?php if (!empty($pengaduan['foto'])): ?>
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-2">Foto Bukti</label>
                        <img src="../uploads/<?= htmlspecialchars($pengaduan['foto']) ?>"
                            alt="Foto pengaduan"
                            class="rounded-lg max-w-sm border border-gray-200">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tindak Lanjut Section -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-800 flex items-center">

                    Riwayat Tindak Lanjut
                </h2>
            </div>

            <div class="p-6">
                <?php if (empty($tindak_lanjut)): ?>
                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        <span>Belum ada tindak lanjut dari petugas.</span>
                    </div>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($tindak_lanjut as $i => $tl): ?>
                            <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-3">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mr-3 font-bold text-sm">
                                            <?= $i + 1 ?>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-800"><?= htmlspecialchars($tl['nama_petugas'] ?? 'Petugas') ?></p>
                                            <p class="text-xs text-gray-500"><?= date('d M Y, H:i', strtotime($tl['tanggal_update'])) ?></p>
                                        </div>
                                    </div>
                                    <?php
                                    $tlStatusClass = $statusColors[$tl['status_akhir']] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                    ?>
                                    <span class="mt-2 sm:mt-0 px-2 py-1 rounded-full text-xs font-medium border <?= $tlStatusClass ?>">
                                        <?= htmlspecialchars($tl['status_akhir']) ?>
                                    </span>
                                </div>

                                <p class="text-gray-700 text-sm bg-gray-50 rounded p-3">
                                    <?= nl2br(htmlspecialchars($tl['catatan'])) ?>
                                </p>

                                <?php if (!empty($tl['foto_bukti'])): ?>
                                    <div class="mt-3">
                                        <a href="../uploads/<?= htmlspecialchars($tl['foto_bukti']) ?>"
                                            target="_blank"
                                            class="inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Lihat Bukti
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>