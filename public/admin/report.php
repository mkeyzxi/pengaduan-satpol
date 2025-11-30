<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
only_role(['admin']);

// ambil 30 hari terakhir count per hari
$stmt = $pdo->prepare("
    SELECT DATE(created_at) as tgl, COUNT(*) as cnt
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
    $counts[] = intval($d['cnt']);
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Laporan - Grafik Trend Pengaduan</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body class="p-3">
  <a class="btn btn-secondary mb-3" href="dashboard.php">← Kembali</a>
  <h4>Grafik Trend Pengaduan (30 hari terakhir)</h4>
  <div id="reportArea" style="background:#fff;padding:10px;border-radius:6px;">
    <canvas id="trendChart" width="900" height="350"></canvas>
  </div>
  <div class="mt-3">
    <button id="downloadPdf" class="btn btn-primary">Download PDF</button>
  </div>

<script>
const labels = <?= json_encode($labels) ?>;
const dataVals = <?= json_encode($counts) ?>;

const ctx = document.getElementById('trendChart').getContext('2d');
const chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: labels,
        datasets: [{
            label: 'Jumlah Pengaduan',
            data: dataVals,
            fill: false,
            tension: 0.2,
            borderColor: 'rgb(75, 192, 192)'
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: true } }
    }
});

document.getElementById('downloadPdf').addEventListener('click', async function(){
    const area = document.getElementById('reportArea');
    const canvas = await html2canvas(area, { scale: 2 });
    const imgData = canvas.toDataURL('image/png');
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('landscape', 'pt', 'a4');
    const pageWidth = pdf.internal.pageSize.getWidth();
    const pageHeight = pdf.internal.pageSize.getHeight();
    // calculate width/height to fit
    const imgProps = pdf.getImageProperties(imgData);
    const imgWidth = pageWidth - 40;
    const imgHeight = (imgProps.height * imgWidth) / imgProps.width;
    pdf.addImage(imgData, 'PNG', 20, 30, imgWidth, imgHeight);
    pdf.save('laporan_pengaduan.pdf');
});
</script>
</body>
</html>
