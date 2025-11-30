<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login Sistem Pengaduan</title>
</head>
<body>

<h2>Login</h2>

<?php if(isset($_GET['error'])): ?>
<p style="color:red;">⚠ Username atau password salah!</p>
<?php endif; ?>

<form action="../app/auth/login_process.php" method="POST">
    <label>Username</label><br>
    <input type="text" name="username" required><br><br>

    <label>Password</label><br>
    <input type="password" name="password" required><br><br>

    <button type="submit">Login</button>
</form>

</body>
</html>
