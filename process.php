<?php
// process.php

// Load Composer autoloader (this makes smalot/pdfparser available)
// require __DIR__ . './vendor/autoload.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

require 'summarizer.php';

use Smalot\PdfParser\Parser;

if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = __DIR__ . 'uploads/';

    // Make sure upload directory exists and is writable
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = basename($_FILES['pdf']['name']);
    $filePath = $uploadDir . $fileName;

    // Debug: check paths
    echo "<pre>";
    echo "Temp File: " . $_FILES['pdf']['tmp_name'] . PHP_EOL;
    echo "Target Path: " . $filePath . PHP_EOL;
    echo "</pre>";

    if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $filePath)) {
        die("❌ Failed to move uploaded file. Check folder permissions on: $uploadDir");
    }

    // Parse PDF
    $parser = new Parser();
    $pdf = $parser->parseFile($filePath);
    $text = $pdf->getText();

    require_once __DIR__ . '/summarizer.php';
    $summary = summarizeText($text);

    echo "<h2>✅ PDF Summary</h2>";
    echo "<p>" . nl2br(htmlspecialchars($summary)) . "</p>";

} else {
    die("❌ No file uploaded or upload error.");
}

echo "<hr>";
echo "<a href='index.php' style='display:inline-block;margin-top:10px;padding:10px 15px;background:#007BFF;color:#fff;text-decoration:none;border-radius:5px;'>⬅ Upload Another PDF</a>";
