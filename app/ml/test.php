<?php
require "../../vendor/autoload.php";

use Phpml\Classification\NaiveBayes;
use Phpml\FeatureExtraction\TokenCountVectorizer;

// Load model
$classifier = unserialize(file_get_contents("classifier_model.dat"));
$vectorizer = unserialize(file_get_contents("vectorizer.dat"));

$text = ["Ada warung jual miras di dekat sekolah"];

// Transform input menggunakan vectorizer yang sama
$vectorizer->transform($text);

echo "Prediksi: " . $classifier->predict($text)[0];
