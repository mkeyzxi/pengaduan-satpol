<?php
session_start();
$title = "Login - Sistem Pengaduan";

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    if ($role === 'admin') header("Location: admin/dashboard.php");
    elseif ($role === 'petugas') header("Location: petugas/dashboard.php");
    else header("Location: masyarakat/dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login Sistem Pengaduan Masyarakat Satpol PP">

    <!-- PWA Meta Tags -->
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#4a5d23">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Pengaduan Satpol PP">
    <link rel="apple-touch-icon" href="icons/icon-192x192.png">

    <title><?= $title ?></title>
    <link rel="stylesheet" href="style/style.css">

    <!-- PWA Service Worker Registration -->
    <script src="js/pwa-register.js" defer></script>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-primary-100 via-primary-50 to-primary-100">



    <!-- Main Content -->
    <main class="flex-1 flex items-center justify-center px-4 py-12">
        <div class="w-full max-w-md">
            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <!-- Card Header -->
                <div class=" px-6 py-8 text-center">

                    <h2 class="text-2xl font-bold text-primary-600">Selamat Datang</h2>
                    <p class="text-gray-600 mt-2">Silakan masuk ke akun Anda</p>
                </div>

                <!-- Card Body -->
                <div class="px-6 py-5">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                            <span>Email atau password salah!</span>
                        </div>
                    <?php endif; ?>

                    <form action="../app/auth/login_process.php" method="POST" class="space-y-5">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <input type="email" id="email" name="email" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                    placeholder="masukkan email">
                            </div>
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input type="password" id="password" name="password" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                                    placeholder="masukkan password">
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                            <span>Masuk</span>
                            
                        </button>
                    </form>
                </div>

                <!-- Card Footer -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 text-center">
                    <p class="text-gray-600">
                        Belum punya akun?
                        <a href="register.php" class="text-primary-600 hover:text-primary-700 font-semibold hover:underline">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </main>


</body>

</html>