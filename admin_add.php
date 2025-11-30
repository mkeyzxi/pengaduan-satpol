<?php
// admin_add.php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$text = $_POST['text'] ?? '';
	$label = $_POST['label'] ?? '';
	if ($text !== '' && $label !== '') {
		$stmt = $pdo->prepare("INSERT INTO training_data (text, label) VALUES (?, ?)");
		$stmt->execute([$text, $label]);
		$msg = "Data latih disimpan.";
	} else {
		$msg = "Text dan label harus diisi.";
	}
}
?>
<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<title>Admin Tambah Data Latih</title>
	<style>
		body {
			font-family: Arial;
			max-width: 720px;
			margin: 20px auto;
			padding: 10px
		}

		label {
			display: block;
			margin: 8px 0 4px
		}

		textarea {
			width: 100%;
			height: 120px
		}

		input[type=text] {
			width: 100%
		}

		button {
			padding: 8px 12px
		}

		.note {
			color: green
		}
	</style>
</head>

<body>
	<h2>Tambah Data Latih</h2>
	<?php if (!empty($msg)): ?><p class="note"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
	<form method="post">
		<label>Text Pengaduan</label>
		<textarea name="text"></textarea>
		<label>Label (misal: Parkir Liar, Sampah, Gangguan Ketertiban, Bangunan Ilegal)</label>
		<input type="text" name="label" />
		<br><br>
		<button type="submit">Simpan</button>
	</form>
	<p>Setelah menambahkan banyak data, jalankan <code>php train.php</code> di terminal untuk melatih ulang model.</p>
</body>

</html>