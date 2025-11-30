<?php
// app/ml/predict.php
require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../helpers/textproc.php';

function predict_text_label($text) {
    $modelDir = __DIR__ . '/';
    if (!file_exists($modelDir . 'classifier.dat')) {
        return null;
    }
    $classifier = unserialize(file_get_contents($modelDir . 'classifier.dat'));
    $vectorizer = unserialize(file_get_contents($modelDir . 'vectorizer.dat'));
    $tfidf = unserialize(file_get_contents($modelDir . 'tfidf.dat'));

    $sample = [ prepare_sample_string($text) ];
    // transform using same pipeline
    $vectorizer->transform($sample);
    $tfidf->transform($sample);

    $pred = $classifier->predict($sample);
    return $pred[0] ?? null;
}
