<?php
function login_check(){
    return isset($_SESSION['user']);
}

function logout(){
    session_destroy();
    header("Location: /public/login.php");
}
