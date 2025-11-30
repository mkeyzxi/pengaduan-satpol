<?php
require __DIR__ . "/../config/database.php";
require __DIR__ . "/../../vendor/autoload.php";

use Phpml\Classification\NaiveBayes;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;

// Fetch dataset
$data = $pdo->query("SELECT text_data, kategori_label FROM training_data")->fetchAll(PDO::FETCH_ASSOC);

$samples = array_column($data, 'text_data');
$labels  = array_column($data, 'kategori_label');

// Vectorizer
$vectorizer = new TokenCountVectorizer(new WhitespaceTokenizer());
$vectorizer->fit($samples);
$vectorizer->transform($samples);

// Train model
$classifier = new NaiveBayes();
$classifier->train($samples, $labels);

// Save Model
file_put_contents("classifier_model.json", json_encode(serialize($classifier)));
file_put_contents("vectorizer.json", json_encode($vectorizer->getVocabulary()));

echo "✔ Model berhasil dilatih dan disimpan!\n";
