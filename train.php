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

// ambil data latih dari DB
$stmt = $pdo->query("SELECT text, label FROM training_data");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (count($rows) < 2) {
    echo "Butuh minimal 2 data latih. Tambahkan data ke tabel training_data.\n";
    exit;
}

$texts = [];
$labels = [];
foreach ($rows as $r) {
    $texts[] = normalize_text($r['text']);
    $labels[] = $r['label'];
}

// Vectorizer & TF-IDF
$vectorizer = new TokenCountVectorizer(new WhitespaceTokenizer());
$vectorizer->fit($texts);
$vectorizer->transform($texts); // membuat representasi count

$tfidf = new TfIdfTransformer();
$tfidf->fit($texts);
$tfidf->transform($texts);

// train NaiveBayes
$classifier = new NaiveBayes();
$classifier->train($texts, $labels);

// simpan model (serialize vectorizer, tfidf, classifier)
save_model($modelFile, $vectorizer, $tfidf, $classifier);

echo "Model dilatih dan disimpan di: $modelFile\n";
