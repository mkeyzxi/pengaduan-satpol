<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
only_role(['admin']);

$title = "Dashboard Admin";

// statistik sederhana
$total_pengaduan = $pdo->query("SELECT COUNT(*) total FROM pengaduan")->fetch()['total'];
$total_users     = $pdo->query("SELECT COUNT(*) total FROM users")->fetch()['total'];
$total_pending   = $pdo->query("SELECT COUNT(*) total FROM pengaduan WHERE status = 'diajukan'")->fetch()['total'];
$total_selesai   = $pdo->query("SELECT COUNT(*) total FROM pengaduan WHERE status = 'selesai'")->fetch()['total'];

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<main class="flex-1">
    <div class="max-w-7xl mx-auto px-4 py-8">

        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Admin</h1>
            <p class="text-gray-600 mt-2">Selamat datang, <?= htmlspecialchars($_SESSION['user']['nama']) ?>!</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

            <!-- Total Pengaduan -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-primary-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Pengaduan</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1"><?= $total_pengaduan ?></p>
                    </div>
                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Total Users -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Users</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1"><?= $total_users ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Pending -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Menunggu Proses</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1"><?= $total_pending ?></p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Selesai -->
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Selesai</p>
                        <p class="text-3xl font-bold text-gray-800 mt-1"><?= $total_selesai ?></p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-xl shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4">Menu Cepat</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

                <a href="users.php" class="flex items-center p-4 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors group">
                    <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center mr-4 group-hover:bg-primary-700 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Kelola Users</p>
                        <p class="text-sm text-gray-500">Tambah, edit, hapus user</p>
                    </div>
                </a>

                <a href="training.php" class="flex items-center p-4 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors group">
                    <div class="w-10 h-10 bg-primary-600 rounded-lg flex items-center justify-center mr-4 group-hover:bg-primary-700 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Data Latih</p>
                        <p class="text-sm text-gray-500">Kelola data training AI</p>
                    </div>
                </a>

                <a href="report.php" class="flex items-center p-4 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors group">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center mr-4 group-hover:bg-green-700 transition-colors">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">Laporan Grafik</p>
                        <p class="text-sm text-gray-500">Lihat statistik pengaduan</p>
                    </div>
                </a>

            </div>
        </div>

    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>