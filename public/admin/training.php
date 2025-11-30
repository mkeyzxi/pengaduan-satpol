<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/sanitize.php';
only_role(['admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $text = clean($_POST['text']);
        $label = clean($_POST['label']);
        $pdo->prepare("INSERT INTO training_data (text_data, kategori_label) VALUES (?, ?)")->execute([$text, $label]);
        header("Location: training.php");
        exit;
    } elseif ($_POST['action'] === 'retrain') {
        // jalankan script training (CLI recommended), tapi kita panggil langsung
        exec("php " . __DIR__ . "/../../app/ml/train.php", $out, $rc);
        // redirect kembali
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
?>
<!doctype html>
<html>
<head><title>Kelola Data Latih</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-3">
<a class="btn btn-secondary mb-2" href="dashboard.php">← Kembali</a>
<h4>Data Latih</h4>
<table class="table">
<thead><tr><th>ID</th><th>Text</th><th>Label</th><th>Aksi</th></tr></thead>
<tbody>
<?php foreach($data as $r): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= htmlspecialchars($r['text_data']) ?></td>
<td><?= htmlspecialchars($r['kategori_label']) ?></td>
<td><a class="btn btn-danger btn-sm" href="?del=<?= $r['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<hr>
<h5>Tambah Data Latih</h5>
<form method="post">
<input type="hidden" name="action" value="add">
<textarea name="text" class="form-control mb-2" rows="3" required></textarea>
<input class="form-control mb-2" name="label" placeholder="Label (kategori)" required>
<button class="btn btn-primary">Tambah</button>
</form>

<hr>
<form method="post">
<input type="hidden" name="action" value="retrain">
<button class="btn btn-success">Latih Ulang Model (retrain)</button>
</form>

</body>
</html>
