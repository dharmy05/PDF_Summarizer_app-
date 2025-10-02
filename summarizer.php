<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

/**
 * Load .env variables
 */
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

/**
 * Summarize text using OpenAI API if available, otherwise fallback.
 *
 * @param string $text
 * @param int $maxSentences
 * @return string
 */
function summarizeText($text, $maxSentences = 5) {
    $apiKey = $_ENV['OPENAI_API_KEY'] ?? null;

    if ($apiKey) {
        $summary = callOpenAiSummarizer($text, $maxSentences, $apiKey);
        if ($summary) {
            return $summary;
        }
    }

    // fallback if API not set or fails
    return fallbackSummarizer($text, $maxSentences);
}

// Call OpenAI API for summarization
function callOpenAiSummarizer($text, $maxSentences, $apiKey) {
    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer {$apiKey}"
    ]);

    $data = [
        "model" => "gpt-4o-mini",  // fast + cost-effective model
        "messages" => [
            ["role" => "system", "content" => "You are a helpful assistant that summarizes PDFs into concise text."],
            ["role" => "user", "content" => "Summarize this text into {$maxSentences} sentences:\n\n{$text}"]
        ],
        "max_tokens" => 500,
    ];

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $decoded = json_decode($response, true);
    return $decoded['choices'][0]['message']['content'] ?? null;
}

/**
 * Fallback summarizer (keyword frequency based)
 */
function fallbackSummarizer($text, $maxSentences = 5) {
    $sentences = preg_split('/(?<=[.?!])\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);

    if (count($sentences) <= $maxSentences) {
        return implode(' ', $sentences);
    }

    $words = str_word_count(strtolower(strip_tags($text)), 1);
    $stopwords = ['the','a','an','and','or','but','if','while','of','in','on','for','to','with','as','by','at','from','is','are','was','were','be','been'];
    $freq = [];
    foreach ($words as $word) {
        if (!in_array($word, $stopwords) && strlen($word) > 2) {
            $freq[$word] = ($freq[$word] ?? 0) + 1;
        }
    }

    $scores = [];
    foreach ($sentences as $i => $sentence) {
        $score = 0;
        foreach (str_word_count(strtolower($sentence), 1) as $word) {
            $score += $freq[$word] ?? 0;
        }
        $scores[$i] = $score;
    }

    arsort($scores);
    $topIndexes = array_slice(array_keys($scores), 0, $maxSentences);
    sort($topIndexes);

    $summary = [];
    foreach ($topIndexes as $i) {
        $summary[] = $sentences[$i];
    }

    return implode(' ', $summary);
}
