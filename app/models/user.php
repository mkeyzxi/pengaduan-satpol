<?php

function getUsers($pdo){
    return $pdo->query("SELECT * FROM users")->fetchAll();
}

function addUser($pdo, $nama, $email, $pass, $role){
    $sql = $pdo->prepare("INSERT INTO users(nama,email,password,role) VALUES(?,?,?,?)");
    return $sql->execute([$nama,$email,password_hash($pass,PASSWORD_BCRYPT),$role]);
}

function deleteUser($pdo, $id){
    $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
}
