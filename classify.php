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

// handle multipart/form-data (fetch text from POST)
$text = $_POST['text'] ?? null;
$user_name = $_POST['user_name'] ?? null;
$user_phone = $_POST['user_phone'] ?? null;
$location = $_POST['location'] ?? null;
$latitude = isset($_POST['latitude']) ? floatval($_POST['latitude']) : null;
$longitude = isset($_POST['longitude']) ? floatval($_POST['longitude']) : null;

if (!$text || trim($text) === '') {
    http_response_code(400);
    echo json_encode(['error' => 'text is required']);
    exit;
}

// handle photo upload (optional)
$uploadPath = __DIR__ . '/uploads';
if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);
$photoPath = null;
if (!empty($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['photo'];
    // basic validation: mime type & size
    $allowed = ['image/jpeg','image/png','image/jpg'];
    if (!in_array($file['type'], $allowed)) {
        echo json_encode(['error' => 'File type not allowed']);
        exit;
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fname = 'photo_' . time() . '_' . bin2hex(random_bytes(5)) . '.' . $ext;
    $dest = $uploadPath . '/' . $fname;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        $photoPath = 'uploads/' . $fname; // relative path for DB
    }
}

// load model
$modelFile = __DIR__ . '/model/satpolpp_model.bin';
$model = load_model($modelFile);
if (!$model) {
    http_response_code(500);
    echo json_encode(['error' => 'Model not found. Please run train.php or train_eval.php first.']);
    exit;
}

$vectorizer = $model['vectorizer'];
$tfidf = $model['tfidf'];
$classifier = $model['classifier'];

$sample = [ prepare_sample_string($text) ];
$vectorizer->transform($sample);
$tfidf->transform($sample);
$predLabel = $classifier->predict($sample)[0];

// simpan ke DB (complaints)
$stmt = $pdo->prepare("INSERT INTO complaints (user_name, user_phone, text, predicted_label, photo_path, location, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->execute([$user_name, $user_phone, $text, $predLabel, $photoPath, $location, $latitude, $longitude]);

echo json_encode([
    'status' => 'ok',
    'predicted_label' => $predLabel,
    'photo_path' => $photoPath,
    'text' => $text
]);
