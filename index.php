<?php
// index.php
?>
<!DOCTYPE html>
<html>
<head>
    <title>PDF Summarizer</title>
    <link rel="stylesheet" href="https://cdn.simplecss.org/simple.min.css">
</head>
<body>
    <h2>PDF Summarizer</h2>
    <form action="process.php" method="post" enctype="multipart/form-data">
        <label for="pdf">Choose PDF file:</label>
        <input type="file" name="pdf" accept="application/pdf" required>
        <button type="submit">Upload & Summarize</button>
    </form>
</body>
</html>
