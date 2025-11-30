<?php
// classify.php
require 'vendor/autoload.php';
require 'db.php';
require 'functions.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Only POST allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    // fallback to form-encoded
    $input = $_POST;
}

if (empty($input['text'])) {
    http_response_code(400);
    echo json_encode(['error' => 'text is required']);
    exit;
}

$text = $input['text'];
$user_name = $input['user_name'] ?? null;
$user_phone = $input['user_phone'] ?? null;

$modelFile = __DIR__ . '/model/satpolpp_model.bin';
$model = load_model($modelFile);
if (!$model) {
    http_response_code(500);
    echo json_encode(['error' => 'Model not found. Please run train.php first.']);
    exit;
}

$vectorizer = $model['vectorizer'];
$tfidf = $model['tfidf'];
$classifier = $model['classifier'];

// preprocess & transform
$sample = [ normalize_text($text) ];
$vectorizer->transform($sample);
$tfidf->transform($sample);

// prediksi
$predLabel = $classifier->predict($sample)[0];

// simpan pengaduan ke DB
$stmt = $pdo->prepare("INSERT INTO complaints (user_name, user_phone, text, predicted_label) VALUES (?, ?, ?, ?)");
$stmt->execute([$user_name, $user_phone, $text, $predLabel]);

echo json_encode([
    'status' => 'ok',
    'predicted_label' => $predLabel,
    'text' => $text
]);
