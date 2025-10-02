<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PDF Summarizer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height:100vh;">
<div class="container text-center">
    <div class="card shadow-lg p-4 rounded-4 mx-auto" style="max-width: 500px;">
        <h1 class="mb-3">📄 PDF Summarizer</h1>
        <p class="text-muted">Upload a PDF to generate a quick summary.</p>
        <form action="process.php" method="post" enctype="multipart/form-data">
            <input class="form-control mb-3" type="file" name="pdf" accept="application/pdf" required>
            <button type="submit" class="btn btn-primary w-100">Upload & Summarize</button>
        </form>
    </div>
</div>
</body>
</html>
