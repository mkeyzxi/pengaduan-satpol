<?php
require "../config/database.php";
require "../core/session.php";

if ($_SERVER['REQUEST_METHOD']=="POST"){
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $q = $pdo->prepare("SELECT * FROM users WHERE email=? AND status=1 LIMIT 1");
    $q->execute([$email]);
    $user = $q->fetch();

    if ($user && password_verify($pass,$user['password'])) {
        $_SESSION['user'] = $user;

        if ($user['role']=="admin") header("Location: /admin/dashboard.php");
        elseif ($user['role']=="petugas") header("Location: /petugas/dashboard.php");
        else header("Location: /masyarakat/dashboard.php");
    } else {
        echo "Login gagal!";
    }
}
