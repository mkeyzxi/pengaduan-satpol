<?php

function getPengaduan($pdo){
    return $pdo->query("SELECT p.*, u.nama FROM pengaduan p 
                        LEFT JOIN users u ON p.user_id=u.id 
                        ORDER BY p.id DESC")->fetchAll();
}

function updateStatus($pdo,$id,$status){
    $sql = $pdo->prepare("UPDATE pengaduan SET status=? WHERE id=?");
    return $sql->execute([$status,$id]);
}
