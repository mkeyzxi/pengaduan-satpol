<?php
$user = $_SESSION['user'] ?? null;
$role = $user['role'] ?? '';
$userName = $user['nama'] ?? 'Guest';

// Dynamic base path calculation
$basePath = '';
$currentDir = dirname($_SERVER['SCRIPT_NAME']);
$pathParts = explode('/', trim($currentDir, '/'));
$publicIndex = array_search('public', $pathParts);
if ($publicIndex !== false) {
    $depth = count($pathParts) - $publicIndex - 1;
    for ($i = 0; $i < $depth; $i++) {
        $basePath .= '../';
    }
}
if (empty($basePath)) $basePath = './';
?>

<nav class="bg-primary-700 text-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between items-center h-16">
            <!-- Logo/Brand -->
            <div class="flex items-center space-x-3">

                <span class="font-bold text-lg hidden sm:block">Pengaduan Satpol PP</span>
                <span class="font-bold text-lg sm:hidden">Satpol PP</span>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-1">
                <?php if ($role === 'admin'): ?>
                    <a href="<?= $basePath ?>admin/dashboard.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Dashboard</a>
                    <a href="<?= $basePath ?>admin/users.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Users</a>
                    <a href="<?= $basePath ?>admin/training.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Data Latih</a>
                    <a href="<?= $basePath ?>admin/report.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Laporan</a>
                <?php elseif ($role === 'petugas'): ?>
                    <a href="<?= $basePath ?>petugas/dashboard.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Dashboard</a>
                    <a href="<?= $basePath ?>petugas/pengaduan.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Pengaduan</a>
                <?php elseif ($role === 'masyarakat'): ?>
                    <a href="<?= $basePath ?>masyarakat/dashboard.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Dashboard</a>
                    <a href="<?= $basePath ?>masyarakat/buat_pengaduan.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Buat Pengaduan</a>
                <?php endif; ?>

                <?php if ($user): ?>
                    <div class="flex items-center space-x-3 ml-4 pl-4 border-l border-primary-500">
                        <span class="text-sm text-primary-200 hidden lg:block"><?= htmlspecialchars($userName) ?></span>
                        <a href="<?= $basePath ?>logout.php"
                            class="bg-primary-800 hover:bg-primary-900 px-4 py-2 rounded-md text-sm font-medium transition-colors">
                            Logout
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded-md hover:bg-primary-600 transition-colors" aria-label="Toggle menu">
                <svg id="menu-icon-open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg id="menu-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-primary-600 mt-2 pt-4">
            <div class="flex flex-col space-y-1">
                <?php if ($role === 'admin'): ?>
                    <a href="<?= $basePath ?>admin/dashboard.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Dashboard</a>
                    <a href="<?= $basePath ?>admin/users.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Users</a>
                    <a href="<?= $basePath ?>admin/training.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Data Latih</a>
                    <a href="<?= $basePath ?>admin/report.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Laporan</a>
                <?php elseif ($role === 'petugas'): ?>
                    <a href="<?= $basePath ?>petugas/dashboard.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Dashboard</a>
                    <a href="<?= $basePath ?>petugas/pengaduan.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Pengaduan</a>
                <?php elseif ($role === 'masyarakat'): ?>
                    <a href="<?= $basePath ?>masyarakat/dashboard.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Dashboard</a>
                    <a href="<?= $basePath ?>masyarakat/buat_pengaduan.php" class="px-3 py-2 rounded-md hover:bg-primary-600 transition-colors">Buat Pengaduan</a>
                <?php endif; ?>

                <?php if ($user): ?>
                    <div class="mt-3 pt-3 border-t border-primary-600">
                        <div class="px-3 py-2 text-sm text-primary-200">
                            <span class="font-medium"><?= htmlspecialchars($userName) ?></span>
                            <span class="text-primary-400 ml-2">(<?= ucfirst($role) ?>)</span>
                        </div>
                        <a href="<?= $basePath ?>logout.php" class="flex items-center px-3 py-2 text-red-300 hover:bg-red-900/30 rounded-md transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Logout
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
        const menu = document.getElementById('mobile-menu');
        const iconOpen = document.getElementById('menu-icon-open');
        const iconClose = document.getElementById('menu-icon-close');

        menu.classList.toggle('hidden');
        iconOpen.classList.toggle('hidden');
        iconClose.classList.toggle('hidden');
    });
</script>