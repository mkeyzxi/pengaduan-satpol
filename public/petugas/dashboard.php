<?php
session_start();
require __DIR__ . "/../../app/core/middleware.php";
require __DIR__ . "/../../app/config/database.php";

only_role(['petugas']);

$title = "Dashboard Petugas";

$pending = $pdo->query("SELECT COUNT(*) AS total FROM pengaduan WHERE status='diajukan'")->fetch()['total'];
$diproses = $pdo->query("SELECT COUNT(*) AS total FROM pengaduan WHERE status='diproses'")->fetch()['total'];
$selesai = $pdo->query("SELECT COUNT(*) AS total FROM pengaduan WHERE status='selesai'")->fetch()['total'];
$total = $pdo->query("SELECT COUNT(*) AS total FROM pengaduan")->fetch()['total'];

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<main class="flex-1">
	<div class="max-w-7xl mx-auto px-4 py-8">

		<!-- Page Header -->
		<div class="mb-8">
			<h1 class="text-3xl font-bold text-gray-800">Dashboard Petugas</h1>
			<p class="text-gray-600 mt-2">Selamat datang, <?= htmlspecialchars($_SESSION['user']['nama']) ?>!</p>
		</div>

		<!-- Stats Cards -->
		<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

			<!-- Pending -->
			<div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-yellow-500">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-sm font-medium text-gray-500">Menunggu Tindakan</p>
						<p class="text-3xl font-bold text-gray-800 mt-1"><?= $pending ?></p>
					</div>
					<div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
						<svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
				</div>
			</div>

			<!-- Diproses -->
			<div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-sm font-medium text-gray-500">Sedang Diproses</p>
						<p class="text-3xl font-bold text-gray-800 mt-1"><?= $diproses ?></p>
					</div>
					<div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
						<svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
						</svg>
					</div>
				</div>
			</div>

			<!-- Selesai -->
			<div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-sm font-medium text-gray-500">Selesai</p>
						<p class="text-3xl font-bold text-gray-800 mt-1"><?= $selesai ?></p>
					</div>
					<div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
						<svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
				</div>
			</div>

			<!-- Total -->
			<div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-primary-500">
				<div class="flex items-center justify-between">
					<div>
						<p class="text-sm font-medium text-gray-500">Total Pengaduan</p>
						<p class="text-3xl font-bold text-gray-800 mt-1"><?= $total ?></p>
					</div>
					<div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
						<svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
						</svg>
					</div>
				</div>
			</div>
		</div>

		<!-- Quick Actions -->
		<div class="bg-white rounded-xl shadow-md p-6">
			<h2 class="text-xl font-bold text-gray-800 mb-4">Menu Cepat</h2>
			<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

				<a href="pengaduan.php" class="flex items-center p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors group border border-yellow-200">
					<div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center mr-4 group-hover:bg-yellow-600 transition-colors">
						<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
						</svg>
					</div>
					<div>
						<p class="font-semibold text-gray-800">Lihat Semua Pengaduan</p>
						<p class="text-sm text-gray-500"><?= $pending ?> pengaduan menunggu tindakan</p>
					</div>
					<svg class="w-5 h-5 ml-auto text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>

				<a href="pengaduan.php?status=diajukan" class="flex items-center p-4 bg-primary-50 hover:bg-primary-100 rounded-lg transition-colors group border border-primary-200">
					<div class="w-12 h-12 bg-primary-600 rounded-lg flex items-center justify-center mr-4 group-hover:bg-primary-700 transition-colors">
						<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
					<div>
						<p class="font-semibold text-gray-800">Pengaduan Baru</p>
						<p class="text-sm text-gray-500">Filter pengaduan yang baru diajukan</p>
					</div>
					<svg class="w-5 h-5 ml-auto text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
					</svg>
				</a>

			</div>
		</div>

	</div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>