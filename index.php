<?php
/**
 * ClearCut — index.php
 * Main application page.
 */
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ClearCut — AI-powered image background removal. Upload any photo and get a transparent PNG in seconds.">
    <title>ClearCut — AI Background Removal</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ========== HEADER ========== -->
<header class="header">
    <div class="container header-inner">
        <a href="index.php" class="logo" aria-label="ClearCut home">
            <span class="logo-icon" aria-hidden="true">✂</span>
            <span class="logo-text">ClearCut</span>
        </a>
        <nav class="nav" aria-label="Main navigation">
            <a href="index.php" class="nav-link active" aria-current="page">Home</a>
            <a href="manual.php" class="nav-link">User Manual</a>
        </nav>
    </div>
</header>

<!-- ========== MAIN ========== -->
<main class="main" id="main">
    <div class="container">

        <!-- Hero -->
        <section class="hero" aria-labelledby="heroTitle">
            <h1 class="hero-title" id="heroTitle">Remove Image Backgrounds Instantly</h1>
            <p class="hero-subtitle">
                Upload any photo and let AI remove the background in seconds.
                Download your result as a crisp, transparent PNG.
            </p>
        </section>

        <!-- ── Upload Section ── -->
        <section class="section" id="uploadSection" aria-labelledby="uploadLabel">
            <h2 class="sr-only" id="uploadLabel">Upload Image</h2>

            <!-- Drop Zone -->
            <div
                class="drop-zone"
                id="dropZone"
                role="button"
                tabindex="0"
                aria-label="Image upload area. Click or drag an image here to begin."
            >
                <div class="drop-zone-icon" aria-hidden="true">🖼️</div>
                <p class="drop-zone-title">Drag &amp; Drop your image here</p>
                <p class="drop-zone-subtitle">— or —</p>
                <button class="btn btn-secondary" id="chooseBtn" type="button">Choose Image</button>
                <p class="drop-zone-hint">Supports JPG, JPEG, PNG, WEBP, GIF &nbsp;·&nbsp; Max 8 MB</p>
                <!-- Hidden file input; triggered by JS -->
                <input
                    type="file"
                    id="fileInput"
                    accept=".jpg,.jpeg,.png,.webp,.gif"
                    aria-hidden="true"
                    tabindex="-1"
                >
            </div>

            <!-- Error Message -->
            <div class="alert alert-error" id="errorAlert" role="alert" aria-live="assertive" hidden>
                <span class="alert-icon" aria-hidden="true">⚠️</span>
                <span id="errorMessage"></span>
            </div>

            <!-- Image Preview Card (shown after file selection) -->
            <div class="preview-card" id="previewCard" hidden>
                <div class="preview-image-wrap">
                    <img id="previewImage" src="" alt="Preview of the selected image">
                </div>
                <div class="preview-info">
                    <p class="preview-filename" id="previewFilename"></p>
                    <p class="preview-filesize" id="previewFilesize"></p>
                </div>
                <div class="preview-actions">
                    <button class="btn btn-ghost" id="clearBtn" type="button">
                        <span aria-hidden="true">✕</span> Clear
                    </button>
                    <button class="btn btn-primary" id="processBtn" type="button">
                        <span aria-hidden="true">✂</span> Remove Background
                    </button>
                </div>
            </div>
        </section>

        <!-- ── Processing Section ── -->
        <section
            class="section processing-section"
            id="processingSection"
            aria-labelledby="processingLabel"
            aria-live="polite"
            hidden
        >
            <h2 class="sr-only" id="processingLabel">Processing</h2>
            <div class="processing-card">
                <div class="spinner" role="status" aria-label="Loading"></div>
                <p class="processing-status" id="processingStatus">Uploading image…</p>
                <div class="processing-stages" aria-hidden="true">
                    <span class="stage" id="stage1">Upload</span>
                    <span class="stage-arrow">›</span>
                    <span class="stage" id="stage2">Analyse</span>
                    <span class="stage-arrow">›</span>
                    <span class="stage" id="stage3">Remove BG</span>
                    <span class="stage-arrow">›</span>
                    <span class="stage" id="stage4">Done</span>
                </div>
            </div>
        </section>

        <!-- ── Result Section ── -->
        <section class="section result-section" id="resultSection" aria-labelledby="resultLabel" hidden>
            <h2 class="section-title" id="resultLabel">Your Result</h2>

            <div class="result-grid">
                <!-- Original -->
                <div class="result-card">
                    <h3 class="result-label">Original Image</h3>
                    <div class="result-image-wrap">
                        <img id="originalImage" src="" alt="Original uploaded image">
                    </div>
                </div>

                <!-- Background Removed -->
                <div class="result-card">
                    <h3 class="result-label">Background Removed</h3>
                    <div class="result-image-wrap checkerboard">
                        <img id="resultImage" src="" alt="Processed image with background removed">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="result-actions">
                <button class="btn btn-secondary" id="uploadAnotherBtn" type="button">
                    <span aria-hidden="true">↑</span> Upload Another
                </button>
                <a class="btn btn-primary" id="downloadBtn" href="#" download="no-bg.png">
                    <span aria-hidden="true">⬇</span> Download PNG
                </a>
            </div>
        </section>

    </div><!-- /.container -->
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/app.js?v=<?= filemtime(__DIR__ . '/assets/js/app.js') ?>"></script>
</body>
</html>
