<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/sanitize.php';

only_role(['masyarakat']);
$title = "Buat Pengaduan";

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<main class="flex-1">
  <div class="max-w-2xl mx-auto px-4 py-8">

    <!-- Back Link -->
    <a href="dashboard.php" class="inline-flex items-center text-primary-600 hover:text-primary-700 font-medium mb-6 group">
      <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      Kembali ke Dashboard
    </a>
    <!-- Info Card -->
    <div class="bg-primary-50 border border-primary-200 rounded-xl p-4 mt-6">
      <div class="flex">
        <svg class="w-5 h-5 text-primary-600 mr-3 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
        </svg>
        <div class="text-sm text-primary-700">
          <p class="font-medium mb-1">Informasi Penting</p>
          <ul class="list-disc list-inside space-y-1 text-primary-600">
            <li>Pengaduan akan diproses oleh petugas dalam 1-3 hari kerja</li>
            <li>Pastikan informasi yang diberikan akurat dan benar</li>
            <li>Anda dapat memantau status pengaduan di dashboard</li>
          </ul>
        </div>
      </div>
    </div>
    <!-- Form Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mt-8">
      <div class="px-6 py-4 border-b border-gray-200 bg-primary-50">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">

          Buat Pengaduan Baru
        </h1>
      </div>

      <div class="p-6">
        <form action="../../app/controllers/pengaduanController.php"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-6">

          <!-- Lokasi -->
          <div>
            <label for="lokasi" class="block text-sm font-medium text-gray-700 mb-2">
              Lokasi Kejadian <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
              </span>
              <input type="text" id="lokasi" name="lokasi" required
                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                placeholder="Contoh: Jl. Sudirman No. 123, Depan Bank ABC">
            </div>
          </div>

          <!-- Deskripsi -->
          <div>
            <label for="deskripsi" class="block text-sm font-medium text-gray-700 mb-2">
              Deskripsi Kejadian <span class="text-red-500">*</span>
            </label>
            <textarea id="deskripsi" name="deskripsi" rows="5" required
              class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors resize-none"
              placeholder="Jelaskan secara detail kejadian yang ingin Anda laporkan. Sertakan waktu kejadian jika memungkinkan."></textarea>
            <p class="text-xs text-gray-500 mt-1">Deskripsi akan dianalisis oleh AI untuk klasifikasi otomatis.</p>
          </div>

          <!-- Foto -->
          <div>
            <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
              Foto Bukti (Opsional)
            </label>
            <div class="relative">
              <input type="file" id="foto" name="foto" accept="image/*"
                class="w-full border border-gray-300 rounded-lg px-4 py-3 text-sm file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors">
            </div>
            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, GIF. Maksimal 5MB.</p>
          </div>

          <!-- Submit Button -->
          <button type="submit"
            class="w-full bg-primary-600 hover:bg-primary-700 text-white font-semibold py-3.5 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">

            <span>Kirim Laporan</span>
          </button>
        </form>
      </div>
    </div>



  </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>