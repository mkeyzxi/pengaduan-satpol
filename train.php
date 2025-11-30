<?php
// train.php
require 'vendor/autoload.php';
require 'db.php';
require 'functions.php';

use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Classification\NaiveBayes;

$modelFile = __DIR__ . '/model/satpolpp_model.bin';
if (!is_dir(__DIR__ . '/model')) mkdir(__DIR__ . '/model', 0755, true);

// ambil data latih
$stmt = $pdo->query("SELECT text, label FROM training_data");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($rows) < 2) {
    echo "Butuh minimal 2 data latih. Tambahkan data ke tabel training_data.\n";
    exit;
}

$samples = [];
$labels = [];
foreach ($rows as $r) {
    $samples[] = prepare_sample_string($r['text']); // now preprocessed + ngrams
    $labels[] = $r['label'];
}

// vectorizer (whitespace tokenizer)
$vectorizer = new TokenCountVectorizer(new WhitespaceTokenizer());
$vectorizer->fit($samples);
$vectorizer->transform($samples);

$tfidf = new TfIdfTransformer();
$tfidf->fit($samples);
$tfidf->transform($samples);

$classifier = new NaiveBayes();
$classifier->train($samples, $labels);

save_model($modelFile, $vectorizer, $tfidf, $classifier);

echo "Model dilatih dan disimpan di: $modelFile\n";
