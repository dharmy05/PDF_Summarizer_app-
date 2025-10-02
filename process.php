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

 

} else {
    die("❌ No file uploaded or upload error.");
}
?> 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PDF Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-5">
<div class="container">
    <div class="card shadow-lg p-4 rounded-4">
        <h2 class="mb-3">✅ Summary of: <span class="text-primary"><?= htmlspecialchars($fileName) ?></span></h2>
        <div class="bg-white border rounded-3 p-3 mb-3" style="max-height: 400px; overflow-y: auto;">
            <p class="mb-0"><?= nl2br(htmlspecialchars($summary)) ?></p>
        </div>
        <a href="index.php" class="btn btn-outline-primary">⬅ Upload Another PDF</a>
    </div>
</div>
</body>
</html>