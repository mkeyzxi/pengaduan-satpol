<?php
require "../config/auth.php";
require "../config/database.php";

$role = $_SESSION['user']['role'];

if ($role == "admin") {
    $data = $pdo->query("SELECT COUNT(*) as total FROM pengaduan")->fetch();
} elseif ($role == "petugas") {
    $data = $pdo->query("SELECT COUNT(*) as total FROM pengaduan WHERE status!='selesai'")->fetch();
} else {
    $uid = $_SESSION['user']['id'];
    $data = $pdo->query("SELECT COUNT(*) as total FROM pengaduan WHERE user_id=$uid")->fetch();
}
