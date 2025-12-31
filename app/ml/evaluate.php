<?php
require __DIR__ . "/../../vendor/autoload.php";
require __DIR__ . "/../helpers/textproc.php";
require __DIR__ . "/../config/database.php";

// load model
$classifier = unserialize(file_get_contents(__DIR__ . "/classifier.dat"));
$vectorizer = unserialize(file_get_contents(__DIR__ . "/vectorizer.dat"));
$tfidf      = unserialize(file_get_contents(__DIR__ . "/tfidf.dat"));

// ambil data test (contoh 20% dari 600 yaitu 120)
$data = $pdo->query("
    SELECT text_data, label 
    FROM training_data 
    ORDER BY RAND() 
    LIMIT 120
")->fetchAll(PDO::FETCH_ASSOC);

$samples = [];
$labels  = [];

foreach ($data as $row) {
    $samples[] = prepare_sample_string($row['text_data']);
    $labels[]  = $row['label'];
}

// transform
$vectorizer->transform($samples);
$tfidf->transform($samples);

// predict
$predictions = $classifier->predict($samples);

// ====== HITUNG METRIK ======
$classes = array_unique($labels);

// confusion matrix
$cm = [];
foreach ($classes as $c1) {
    foreach ($classes as $c2) {
        $cm[$c1][$c2] = 0;
    }
}

for ($i = 0; $i < count($labels); $i++) {
    $actual = $labels[$i];
    $pred   = $predictions[$i];
    $cm[$actual][$pred]++;
}

// accuracy
$correct = 0;
foreach ($classes as $c) {
    $correct += $cm[$c][$c];
}
$accuracy = $correct / count($labels);

// precision, recall, f1 (macro)
$precisionSum = 0;
$recallSum    = 0;
$f1Sum        = 0;

foreach ($classes as $c) {
    $tp = $cm[$c][$c];

    $fp = 0;
    foreach ($classes as $other) {
        if ($other !== $c) {
            $fp += $cm[$other][$c];
        }
    }

    $fn = 0;
    foreach ($classes as $other) {
        if ($other !== $c) {
            $fn += $cm[$c][$other];
        }
    }

    $precision = ($tp + $fp) > 0 ? $tp / ($tp + $fp) : 0;
    $recall    = ($tp + $fn) > 0 ? $tp / ($tp + $fn) : 0;
    $f1        = ($precision + $recall) > 0
                    ? 2 * $precision * $recall / ($precision + $recall)
                    : 0;

    $precisionSum += $precision;
    $recallSum    += $recall;
    $f1Sum        += $f1;
}

$macroPrecision = $precisionSum / count($classes);
$macroRecall    = $recallSum / count($classes);
$macroF1        = $f1Sum / count($classes);

// ====== OUTPUT ======
echo "Akurasi          : " . number_format($accuracy * 100, 2) . "%\n";
echo "Precision (Macro): " . number_format($macroPrecision * 100, 2) . "%\n";
echo "Recall (Macro)   : " . number_format($macroRecall * 100, 2) . "%\n";
echo "F1-Score (Macro) : " . number_format($macroF1 * 100, 2) . "%\n";
