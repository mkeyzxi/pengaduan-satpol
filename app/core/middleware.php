<?php

function only_role($roles = []) {
    if (!isset($_SESSION['user'])) {
        header("Location: /public/login.php");
        exit;
    }

    if (!in_array($_SESSION['user']['role'], $roles)) {
        die("<h3>Akses ditolak!</h3>");
    }
}
