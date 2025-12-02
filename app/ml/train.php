<?php
require __DIR__ . "/../../vendor/autoload.php";
require __DIR__ . "/../config/database.php";

use Phpml\Classification\NaiveBayes;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;

// ambil data train
$data = $pdo->query("SELECT text_data, kategori_label FROM training_data")->fetchAll(PDO::FETCH_ASSOC);

$samples = array_column($data, 'text_data');
$labels  = array_column($data, 'kategori_label');

// vectorizer
$vectorizer = new TokenCountVectorizer(new WhitespaceTokenizer());
$vectorizer->fit($samples);
$vectorizer->transform($samples);

// TF-IDF
$tfidf = new TfIdfTransformer();
$tfidf->fit($samples);
$tfidf->transform($samples);

// train
$classifier = new NaiveBayes();
$classifier->train($samples, $labels);

// save model
file_put_contents(__DIR__ . "/classifier.dat", serialize($classifier));
file_put_contents(__DIR__ . "/vectorizer.dat", serialize($vectorizer));
file_put_contents(__DIR__ . "/tfidf.dat", serialize($tfidf));

echo "✔ Model berhasil dilatih dan disimpan!\n";
