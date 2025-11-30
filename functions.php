<?php
// functions.php
require 'vendor/autoload.php';
use Phpml\FeatureExtraction\TokenCountVectorizer;
use Phpml\Tokenization\WhitespaceTokenizer;
use Phpml\FeatureExtraction\TfIdfTransformer;
use Sastrawi\Stemmer\StemmerFactory;

/**
 * Normalisasi dasar: lowercase, hapus URL, non alnum (tetap spasi), collapse spaces
 */
function normalize_text($text) {
    $t = mb_strtolower($text, 'UTF-8');
    $t = preg_replace('#https?://\S+#', ' ', $t);
    // pertahankan huruf a-z dan angka serta spasi (bahasa indonesia dalam ascii)
    $t = preg_replace('/[^a-z0-9\s]/u', ' ', $t);
    $t = preg_replace('/\s+/u', ' ', $t);
    $t = trim($t);
    return $t;
}

/**
 * Stem bahasa Indonesia (Sastrawi)
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
 * Tokenize + remove stopwords basic
 * Mengembalikan array tokens
 */
function tokenize_and_filter($text) {
    $text = normalize_text($text);
    // stemming sebelum tokenisasi -> bisa juga setelah; kita stem pada frasa utuh
    $text = stem_text($text);

    $tokens = explode(' ', $text);
    $stopwords = [
        'yang','dan','di','ke','dari','pada','dengan','untuk','sebagai','itu','ini','adalah',
        'saya','anda','kami','atau','yg','tdk','tidak','dgn','ga','gak','aja','sdh','sudah'
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
 * Generate ngrams (unigram + bigram) -> setiap ngram digabung jadi token dengan underscore
 * Output: array tokens (strings)
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
 * Prepare a single sample string that will be given to TokenCountVectorizer (whitespace tokenization).
 * We join tokens with spaces, tokens themselves can be ngram tokens like 'sampah_tumpuk'
 */
function prepare_sample_string($rawText) {
    $tokens = tokenize_and_filter($rawText);
    $ngrams = gen_ngrams($tokens, 1, 2); // unigram + bigram
    return implode(' ', $ngrams);
}

/**
 * Save / load model (vectorizer, tfidf, classifier)
 */
function save_model($path, $vectorizer, $tfidf, $classifier) {
    $data = [
        'vectorizer' => $vectorizer,
        'tfidf' => $tfidf,
        'classifier' => $classifier
    ];
    file_put_contents($path, serialize($data));
}

function load_model($path) {
    if (!file_exists($path)) return null;
    $data = @unserialize(file_get_contents($path));
    if (!is_array($data)) return null;
    return $data;
}
