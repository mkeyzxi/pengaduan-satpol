<!-- C:\xampp\htdocs\pengaduan\public\admin\users.php -->

<?php
session_start();
require __DIR__ . '/../../app/core/middleware.php';
require __DIR__ . '/../../app/config/database.php';
require __DIR__ . '/../../app/helpers/sanitize.php';
only_role(['admin']);

// add user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
  $nama  = clean($_POST['nama']);
  $email = clean($_POST['email']);
  $pass  = $_POST['password'];
  $role  = $_POST['role'];

  // hash password
  $hash = password_hash($pass, PASSWORD_BCRYPT);

  // cek jika email atau username sudah ada
  $check = $pdo->prepare("SELECT id FROM users WHERE email=?");
  $check->execute([$email]);

  if ($check->rowCount() > 0) {
    die("⚠️ Email sudah digunakan!");
  }

  // simpan user baru
  $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
  $stmt->execute([$nama, $email, $hash, $role]);

  header("Location: users.php");
  exit;
}
// delete
if (isset($_GET['del'])) {
  $id = intval($_GET['del']);
  $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
  header("Location: users.php");
  exit;
}
// if (isset($_GET['put'])) { =================================== perbarua untuk detail dan edit
//   $id = intval($_GET['del']);
//   $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
//   header("Location: users.php");
//   exit;
// }

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>
<!doctype html>
<html>

<head>
  <title>Kelola Users</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="p-3">
  <a href="dashboard.php" class="btn btn-secondary mb-2">Kembali</a>
  <h4>Daftar Users</h4>
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Email</th>
        <th>Role</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= $u['id'] ?></td>
          <td><?= htmlspecialchars($u['nama']) ?></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= $u['role'] ?></td>
          <td>
            <a class="btn btn-sm btn-danger" href="?put=<?= $u['id'] ?>">Edit</a>
            <a class="btn btn-sm btn-danger" href="?del=<?= $u['id'] ?>" onclick="return confirm('Hapus?')">Hapus</a>
            </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <hr>
  <h5>Tambah User</h5>
  <form method="post">
    <input type="hidden" name="action" value="add">
    <input class="form-control mb-2" name="nama" placeholder="Nama" required>
    <input class="form-control mb-2" name="email" placeholder="Email" type="email" required>
    <input class="form-control mb-2" name="password" placeholder="Password" type="password" required>
    <select class="form-control mb-2" name="role">
      <option value="masyarakat">Masyarakat</option>
      <option value="petugas">Petugas</option>
      <option value="admin">Admin</option>
    </select>
    <button class="btn btn-primary">Tambah</button>
  </form>
</body>

</html>