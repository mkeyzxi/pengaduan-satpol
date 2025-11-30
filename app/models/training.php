<?php

function addTrainingData($pdo,$text,$label){
    $sql = $pdo->prepare("INSERT INTO training_data(text_data,kategori_label) VALUES(?,?)");
    return $sql->execute([$text,$label]);
}

function getTrainingData($pdo){
    return $pdo->query("SELECT * FROM training_data")->fetchAll();
}
