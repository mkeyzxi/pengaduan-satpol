<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	require __DIR__ . '/../app/config/database.php';
	require __DIR__ . '/../app/helpers/sanitize.php';

	$nama  = clean($_POST['nama']);
	$email = clean($_POST['email']);
	$pass  = $_POST['password'];
	$role  = "masyarakat";

	// hash password
	$hash = password_hash($pass, PASSWORD_BCRYPT);

	// cek jika email sudah ada
	$check = $pdo->prepare("SELECT id FROM users WHERE email=?");
	$check->execute([$email]);

	if ($check->rowCount() > 0) {
		die("⚠️ Email sudah digunakan!");
	}

	// simpan user baru
	$stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
	$stmt->execute([$nama, $email, $hash, $role]);

	header("Location: login.php");
	exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register</title>
</head>

<body>
	<form method="post">
		<label for="nama">Nama:</label>
		<input type="text" id="nama" name="nama" required><br><br>

		<label for="email">Email:</label>
		<input type="email" id="email" name="email" required><br><br>

		<label for="password">Password:</label>
		<input type="password" id="password" name="password" required><br><br>

		<button type="submit">Register</button>
		<a href="login.php">Sudah punya akun?</a>
	</form>

</body>

</html>