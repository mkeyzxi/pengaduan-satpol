<?php
require __DIR__ . "/../../vendor/autoload.php";
require __DIR__ . "/predict.php";

echo "Prediksi: " . predict_text_label("ada siswa bolos sekolah") . "\n";
