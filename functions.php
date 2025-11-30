<?php
// functions.php
require 'vendor/autoload.php';

use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Phpml\Classification\NaiveBayes;

function normalize_text($text)
{
	// lowercase
	$t = mb_strtolower($text, 'UTF-8');
	// remove URLs, mentions, numbers, punctuation
	$t = preg_replace('#https?://\S+#', ' ', $t);
	$t = preg_replace('/[^a-z0-9\s]/u', ' ', $t);
	// collapse spaces
	$t = preg_replace('/\s+/u', ' ', $t);
	$t = trim($t);
	return $t;
}

function tokenize_and_filter($text)
{
	// simple tokenizer: split whitespace
	$text = normalize_text($text);
	$tokens = explode(' ', $text);
	// basic stopwords (bahasa indonesia minimal)
	$stopwords = [
		'yang',
		'dan',
		'di',
		'ke',
		'dari',
		'pada',
		'dengan',
		'untuk',
		'sebagai',
		'itu',
		'ini',
		'adalah',
		'saya',
		'anda',
		'kami',
		'atau',
		'ke',
		'dgn',
		'yg',
		'tdk',
		'tidak'
	];
	$result = [];
	foreach ($tokens as $t) {
		$t = trim($t);
		if ($t === '' || in_array($t, $stopwords)) continue;
		$result[] = $t;
	}
	return $result;
}

// fungsi untuk menyimpan/panggil model (vectorizer + transformer + classifier)
function save_model($path, $vectorizer, $tfidf, $classifier)
{
	$data = [
		'vectorizer' => $vectorizer,
		'tfidf' => $tfidf,
		'classifier' => $classifier
	];
	file_put_contents($path, serialize($data));
}

function load_model($path)
{
	if (!file_exists($path)) return null;
	$data = @unserialize(file_get_contents($path));
	if (!is_array($data)) return null;
	return $data;
}

function prepare_samples_from_texts(array $texts)
{
	// returns array of strings (preprocessed)
	$out = [];
	foreach ($texts as $t) {
		$out[] = normalize_text($t);
	}
	return $out;
}
