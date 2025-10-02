<?php
function summarizeText($text, $maxSentences = 5) {
    // Break into sentences
    $sentences = preg_split('/(?<=[.?!])\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);

    if (count($sentences) <= $maxSentences) {
        return implode(' ', $sentences);
    }

    // Build word frequency table
    $words = str_word_count(strtolower(strip_tags($text)), 1);
    $stopwords = ['the','a','an','and','or','but','if','while','of','in','on','for','to','with','as','by','at','from','is','are','was','were','be','been'];
    $freq = [];
    foreach ($words as $word) {
        if (!in_array($word, $stopwords) && strlen($word) > 2) {
            $freq[$word] = ($freq[$word] ?? 0) + 1;
        }
    }

    // Score sentences by word frequency
    $scores = [];
    foreach ($sentences as $i => $sentence) {
        $score = 0;
        foreach (str_word_count(strtolower($sentence), 1) as $word) {
            $score += $freq[$word] ?? 0;
        }
        $scores[$i] = $score;
    }

    // Sort sentences by score, pick top N
    arsort($scores);
    $topIndexes = array_slice(array_keys($scores), 0, $maxSentences);

    // Keep original order of top sentences
    sort($topIndexes);
    $summary = [];
    foreach ($topIndexes as $i) {
        $summary[] = $sentences[$i];
    }

    return implode(' ', $summary);
}
?>
