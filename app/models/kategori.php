<?php

function getKategori($pdo){
    return $pdo->query("SELECT * FROM kategori")->fetchAll();
}

function addKategori($pdo,$nama){
    $pdo->prepare("INSERT INTO kategori(nama) VALUES(?)")->execute([$nama]);
}
