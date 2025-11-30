<?php
session_start();

function requireRole($role) {
    if (!isset($_SESSION['user'])) {
        header("Location: /public/login.php");
        exit;
    }

    if ($_SESSION['user']['role'] !== $role) {
        echo "<h2>⚠ Akses Ditolak!</h2>";
        exit;
    }
}
