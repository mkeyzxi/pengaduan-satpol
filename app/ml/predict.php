<?php

require __DIR__ . "/../../vendor/autoload.php";
require __DIR__ . '/../helpers/textproc.php';

function predict_text_label($text)
{
    $modelPath = __DIR__ . "/classifier.dat";
    $vecPath   = __DIR__ . "/vectorizer.dat";
    $tfidfPath = __DIR__ . "/tfidf.dat";

    if (!file_exists($modelPath) || !file_exists($vecPath) || !file_exists($tfidfPath)) {
        return "tidak diketahui";
    }

    $classifier = unserialize(file_get_contents($modelPath));
    $vectorizer = unserialize(file_get_contents($vecPath));
    $tfidf      = unserialize(file_get_contents($tfidfPath));

    $sample = [prepare_sample_string($text)];

    $vectorizer->transform($sample);
    $tfidf->transform($sample);

    $result = $classifier->predict($sample);

    return $result[0] ?? "tidak diketahui";
}
