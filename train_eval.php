<?php
// train_eval.php
require 'vendor/autoload.php';
require 'db.php';
require 'functions.php';

use Phpml\CrossValidation\StratifiedRandomSplit;
use Phpml\Classification\NaiveBayes;
use Phpml\Metric\Accuracy;
use Phpml\Metric\ConfusionMatrix;
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Dataset\ArrayDataset;

$stmt = $pdo->query("SELECT text, label FROM training_data");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (count($rows) < 10) {
    echo "Butuh lebih banyak data (minimal 10). Saat ini: " . count($rows) . "\n";
    exit;
}

$samples = $labels = [];
foreach ($rows as $r) {
    $samples[] = prepare_sample_string($r['text']);
    $labels[] = $r['label'];
}
// split stratified: testSize 0.25
$dataset = new ArrayDataset($samples, $labels);
$split = new StratifiedRandomSplit($dataset, 0.25);
// $split = new StratifiedRandomSplit($samples, $labels, 0.25);

$trainSamples = $split->getTrainSamples();
$trainLabels = $split->getTrainLabels();
$testSamples = $split->getTestSamples();
$testLabels = $split->getTestLabels();

// transform with same vectorizer/tfidf pipeline
$vectorizer = new TokenCountVectorizer(new WhitespaceTokenizer());
$vectorizer->fit($trainSamples);
$vectorizer->transform($trainSamples);
$vectorizer->transform($testSamples);

$tfidf = new TfIdfTransformer();
$tfidf->fit($trainSamples);
$tfidf->transform($trainSamples);
$tfidf->transform($testSamples);

$classifier = new NaiveBayes();
$classifier->train($trainSamples, $trainLabels);

$predicted = $classifier->predict($testSamples);

$acc = Accuracy::score($testLabels, $predicted);
$cm = ConfusionMatrix::compute($testLabels, $predicted);

echo "Evaluasi model:\n";
echo "Jumlah train: " . count($trainSamples) . "\n";
echo "Jumlah test: " . count($testSamples) . "\n";
echo "Accuracy: " . round($acc * 100, 2) . "%\n\n";

echo "Confusion Matrix:\n";
print_r($cm);

// simpan model yang dilatih menggunakan seluruh data (opsional)
echo "\nMelatih ulang model menggunakan seluruh dataset dan menyimpan...\n";
$vectorizer_all = new TokenCountVectorizer(new WhitespaceTokenizer());
$vectorizer_all->fit($samples);
$vectorizer_all->transform($samples);
$tfidf_all = new TfIdfTransformer();
$tfidf_all->fit($samples);
$tfidf_all->transform($samples);
$classifier_all = new NaiveBayes();
$classifier_all->train($samples, $labels);
$modelFile = __DIR__ . '/model/satpolpp_model.bin';
if (!is_dir(__DIR__ . '/model')) mkdir(__DIR__ . '/model',0755,true);
save_model($modelFile, $vectorizer_all, $tfidf_all, $classifier_all);
echo "Model akhir disimpan di: $modelFile\n";
