<?php
session_start();
if(isset($_SESSION['user'])){
    if($_SESSION['user']['role']=="admin") header("Location: /public/admin/dashboard.php");
    elseif($_SESSION['user']['role']=="petugas") header("Location: /public/petugas/dashboard.php");
    else header("Location: /public/masyarakat/dashboard.php");
    exit;
}
header("Location: login.php");
