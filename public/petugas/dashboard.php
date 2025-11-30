<?php
session_start();
require "../../app/core/middleware.php";
require "../../app/config/database.php";

only_role(['petugas']);

$data = $pdo->query("SELECT COUNT(*) AS total FROM pengaduan WHERE status!='selesai'")->fetch();
?>

<h2>Dashboard Petugas</h2>
<p>Pengaduan yang harus ditindaklanjuti: <strong><?=$data['total']?></strong></p>
<a href="../login.php?logout=1">Logout</a>
