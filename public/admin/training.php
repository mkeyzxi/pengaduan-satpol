<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/sanitize.php';
require __DIR__ . "/../../vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
only_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'add') {
        $text  = clean($_POST['text']);
        $label = clean($_POST['label']);

        $pdo->prepare("INSERT INTO training_data (text_data, label) VALUES (?, ?)")
            ->execute([$text, $label]);

        header("Location: training.php");
        exit;
    } elseif ($_POST['action'] === 'retrain') {
        exec("php " . __DIR__ . "/../../app/ml/train.php", $out, $rc);

    } elseif ($_POST['action'] === 'import_excel') {

    $filePath = $_FILES['excel_file']['tmp_name'];

    $filePath = $_FILES['excel_file']['tmp_name'];
    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();
    $rows = $sheet->toArray();

    foreach ($rows as $index => $row) {
        if ($index == 0) continue; // skip header

        $no     = $row[0];
        $text   = $row[1];
        $label  = $row[2];

        if ($text && $label) {
            $pdo->prepare("INSERT INTO training_data (text_data, label) VALUES (?, ?)")
                ->execute([$text, $label]);
        }
    }

    header("Location: training.php");
    exit;
}

}

if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM training_data WHERE id=?")->execute([intval($_GET['del'])]);
    header("Location: training.php");
    exit;
}

$data = $pdo->query("SELECT * FROM training_data ORDER BY id DESC")->fetchAll();

// CSV tambahkan data

?>
<!doctype html>
<html>

<head>
    <title>Kelola Data Latih</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-3">
    <a class="btn btn-secondary mb-2" href="dashboard.php">← Kembali</a>
    <h5>Tambah Data Latih</h5>
    <form method="post">
        <input type="hidden" name="action" value="add">
        <textarea name="text" class="form-control mb-2" rows="3" required></textarea>
        <input class="form-control mb-2" name="label" placeholder="Label hasil koreksi" required>
        <button class="btn btn-primary">Tambah</button>
    </form>

    <hr>
    <form method="post">
        <input type="hidden" name="action" value="retrain">
        <button class="btn btn-success">Latih Ulang Model (Retrain)</button>
    </form>
    <hr>

    <h5>Import Excel (.xlsx)</h5>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="import_excel">
        <input type="file" name="excel_file" accept=".xlsx" class="form-control mb-2" required>
        <button class="btn btn-info">Upload Excel</button>
    </form>
    <hr>
    <h4>Data Latih</h4>

    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Text</th>
                <th>Label AI</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= htmlspecialchars($r['text_data']) ?></td>
                    <td><?= htmlspecialchars($r['label']) ?></td>
                    <td>
                        <a class="btn btn-danger btn-sm" href="?del=<?= $r['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    

</body>

</html>