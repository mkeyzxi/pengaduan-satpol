<?php
// app/helpers/textproc.php
require __DIR__ . '/../../vendor/autoload.php';
use Sastrawi\Stemmer\StemmerFactory;

/**
 * Normalisasi dasar
 */
function normalize_text($text) {
    $t = mb_strtolower($text, 'UTF-8');
    $t = preg_replace('#https?://\S+#', ' ', $t);
    $t = preg_replace('/[^a-z0-9\s]/u', ' ', $t);
    $t = preg_replace('/\s+/u', ' ', $t);
    return trim($t);
}

/**
 * Stemming bahasa indonesia (Sastrawi)
 */
function stem_text($text) {
    static $stemmer = null;
    if ($stemmer === null) {
        $factory = new StemmerFactory();
        $stemmer = $factory->createStemmer();
    }
    return $stemmer->stem($text);
}

/**
 * Tokenize dan filter stopwords sederhana
 */
function tokenize_and_filter($text) {
    $text = normalize_text($text);
    $text = stem_text($text);
    $tokens = explode(' ', $text);
    $stopwords = [
        'yang','dan','di','ke','dari','pada','dengan','untuk','sebagai',
        'itu','ini','adalah','saya','anda','kami','atau','yg','tdk','tidak',
        'dgn','aja','sdh','sudah'
    ];
    $out = [];
    foreach ($tokens as $t) {
        $t = trim($t);
        if ($t === '' || in_array($t, $stopwords)) continue;
        $out[] = $t;
    }
    return $out;
}

/**
 * buat n-gram (unigram + bigram)
 */
function gen_ngrams(array $tokens, $minN = 1, $maxN = 2) {
    $out = [];
    $len = count($tokens);
    for ($n = $minN; $n <= $maxN; $n++) {
        for ($i = 0; $i <= $len - $n; $i++) {
            $ng = array_slice($tokens, $i, $n);
            $out[] = implode('_', $ng);
        }
    }
    return $out;
}

/**
 * Siapkan sample untuk vectorizer (sebuah string)
 */
function prepare_sample_string($rawText) {
    $tokens = tokenize_and_filter($rawText);
    $ngrams = gen_ngrams($tokens, 1, 2);
    return implode(' ', $ngrams);
}
