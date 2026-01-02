<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
only_role(['admin']);

$title = "Laporan Pengaduan";

/* =============================
   AMBIL DATA 30 HARI TERAKHIR
============================= */
$stmt = $pdo->prepare("
    SELECT DATE(created_at) AS tgl, COUNT(*) AS cnt
    FROM pengaduan
    WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    GROUP BY DATE(created_at)
    ORDER BY DATE(created_at) ASC
");
$stmt->execute();
$data = $stmt->fetchAll();

$labels = [];
$counts = [];
foreach ($data as $d) {
  $labels[] = $d['tgl'];
  $counts[] = (int)$d['cnt'];
}

require __DIR__ . '/../layouts/header.php';
require __DIR__ . '/../layouts/navbar.php';
?>

<!-- LIBRARY -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<style>
  /* =============================
   SAFE MODE UNTUK PDF
============================= */
  .pdf-export,
  .pdf-export * {
    color: #000 !important;
    background-color: #fff !important;
    border-color: #ddd !important;
    box-shadow: none !important;
    text-shadow: none !important;
    filter: none !important;
  }
</style>

<main class="flex-1">
  <div class="max-w-7xl mx-auto px-4 py-8">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
      <div>
        <h1 class="text-3xl font-bold text-gray-800">Laporan Pengaduan</h1>
        <p class="text-gray-600 mt-1">Grafik trend pengaduan 30 hari terakhir</p>
      </div>

      <button
        id="downloadPdf"
        class="mt-4 sm:mt-0 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">
        Download PDF
      </button>
    </div>
    <!-- SUMMARY -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-8">
      <div class="bg-white rounded-xl shadow-md p-6 text-center">
        <p class="text-sm text-gray-500">Total (30 Hari)</p>
        <p class="text-3xl font-bold text-green-600"><?= array_sum($counts) ?></p>
      </div>

      <div class="bg-white rounded-xl shadow-md p-6 text-center">
        <p class="text-sm text-gray-500">Rata-rata / Hari</p>
        <p class="text-3xl font-bold text-blue-600">
          <?= count($counts) ? round(round(array_sum($counts) / count($counts)), 1) : 0 ?>
        </p>
      </div>

      <div class="bg-white rounded-xl shadow-md p-6 text-center">
        <p class="text-sm text-gray-500 ">Hari Tertinggi</p>
        <p class="text-3xl font-bold text-yellow-600">
          <?= count($counts) ? max($counts) : 0 ?>
        </p>
      </div>
    </div>
    <!-- AREA YANG DIEKSPOR KE PDF -->
    <div id="reportArea" class="bg-white rounded-xl shadow-md overflow-hidden">

      <div class="px-6 py-4 border-b bg-gray-50">
        <h2 class="text-lg font-semibold text-gray-800">
          Trend Pengaduan (30 Hari Terakhir)
        </h2>
      </div>

      <div class="p-6">
        <?php if (empty($data)): ?>
          <div class="text-center py-12 text-gray-500">
            <p>Belum ada data pengaduan</p>
          </div>
        <?php else: ?>
          <div style="height:400px">
            <canvas id="trendChart"></canvas>
          </div>
        <?php endif; ?>
      </div>

    </div>



  </div>
</main>

<script>
  const labels = <?= json_encode($labels) ?>;
  const values = <?= json_encode($counts) ?>;

  <?php if (!empty($data)): ?>
    new Chart(document.getElementById('trendChart'), {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Jumlah Pengaduan',
          data: values,
          borderColor: '#556B2F',
          backgroundColor: 'rgba(85,107,47,0.15)',
          fill: true,
          tension: 0.3,
          borderWidth: 3,
          pointRadius: 4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  <?php endif; ?>

  /* =============================
     EXPORT PDF (FIXED)
  ============================= */
  document.getElementById('downloadPdf').onclick = async () => {
    const area = document.getElementById('reportArea');

    // FORCE DESKTOP WIDTH
    area.classList.add('pdf-export');
    area.style.width = '1024px';

    const canvas = await html2canvas(area, {
      scale: 2,
      backgroundColor: '#ffffff',
      useCORS: true
    });

    const img = canvas.toDataURL('image/png');
    const {
      jsPDF
    } = window.jspdf;
    const pdf = new jsPDF('landscape', 'pt', 'a4');

    const pageW = pdf.internal.pageSize.getWidth();
    const imgProps = pdf.getImageProperties(img);
    const imgW = pageW - 80;
    const imgH = (imgProps.height * imgW) / imgProps.width;

    pdf.setFontSize(18);
    pdf.text('Laporan Pengaduan - Satpol PP', 40, 40);
    pdf.setFontSize(11);
    pdf.text('Generated: ' + new Date().toLocaleDateString('id-ID'), 40, 60);

    pdf.addImage(img, 'PNG', 40, 80, imgW, imgH);
    pdf.save('laporan_pengaduan.pdf');

    // RESET
    area.classList.remove('pdf-export');
    area.style.width = '';
  };
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>