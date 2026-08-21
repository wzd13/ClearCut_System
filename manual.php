<?php
/**
 * ClearCut — manual.php
 * User Manual page — English only.
 */
require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="ClearCut User Manual — learn how to remove image backgrounds with AI.">
    <title>User Manual — ClearCut</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/manual.css">
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
            <a href="index.php" class="nav-link">Home</a>
            <a href="manual.php" class="nav-link active" aria-current="page">User Manual</a>
        </nav>
    </div>
</header>

<!-- ========== MAIN ========== -->
<main class="main" id="main">
    <div class="container">

        <!-- Hero -->
        <div class="manual-hero">
            <h1>User Manual</h1>
            <p>Learn how to remove image backgrounds instantly with ClearCut.</p>
        </div>

        <!-- Table of Contents -->
        <nav class="toc" aria-label="Table of contents">
            <p class="toc-title">Contents</p>
            <ul class="toc-list">
                <li><a href="#overview">Overview</a></li>
                <li><a href="#how-to-use">How to Use</a></li>
                <li><a href="#interface">Interface Explanation</a></li>
                <li><a href="#formats">Supported Formats</a></li>
                <li><a href="#tips">Tips for Best Results</a></li>
                <li><a href="#faq">FAQ</a></li>
            </ul>
        </nav>

        <!-- ── Overview ── -->
        <section class="manual-section" id="overview">
            <h2>Overview</h2>
            <p>
                ClearCut is an AI-powered web application that removes the background from any photo.
                Upload your image, click <strong>Remove Background</strong>, and download a crisp,
                transparent PNG — no design skills or software required.
            </p>
            <p>
                All processing is performed securely on the server using the
                <a href="https://developer.pixelcut.ai/" target="_blank" rel="noopener noreferrer">Pixelcut Background Removal API</a>.
                Your image is never processed inside the browser, and your Pixelcut API key is
                never exposed to the public.
            </p>
        </section>

        <!-- ── How to Use ── -->
        <section class="manual-section" id="how-to-use">
            <h2>How to Use</h2>
            <ol class="steps">
                <li>
                    <span class="step-number" aria-hidden="true">1</span>
                    <div>
                        <strong>Open ClearCut</strong>
                        <p>Visit the ClearCut homepage in your web browser.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number" aria-hidden="true">2</span>
                    <div>
                        <strong>Select an Image</strong>
                        <p>
                            Drag and drop a photo directly into the upload area, or click
                            <strong>Choose Image</strong> to open your file browser and select a file.
                        </p>
                    </div>
                </li>
                <li>
                    <span class="step-number" aria-hidden="true">3</span>
                    <div>
                        <strong>Preview the Image</strong>
                        <p>
                            After selecting a file, a preview appears showing the image thumbnail,
                            file name, and size. Use the <strong>Clear</strong> button to deselect
                            and choose a different image.
                        </p>
                    </div>
                </li>
                <li>
                    <span class="step-number" aria-hidden="true">4</span>
                    <div>
                        <strong>Click Remove Background</strong>
                        <p>Click the purple <strong>Remove Background</strong> button to begin.</p>
                    </div>
                </li>
                <li>
                    <span class="step-number" aria-hidden="true">5</span>
                    <div>
                        <strong>Wait for Processing</strong>
                        <p>
                            Your image is uploaded to the server, then sent to the Pixelcut API for
                            AI background removal. A progress indicator shows the current stage:
                            <em>Upload → Analyse → Remove BG → Done</em>.
                        </p>
                    </div>
                </li>
                <li>
                    <span class="step-number" aria-hidden="true">6</span>
                    <div>
                        <strong>View the Result</strong>
                        <p>
                            Both the original image and the background-removed result are displayed
                            side by side. A grey checkerboard pattern in the result panel represents
                            transparent areas.
                        </p>
                    </div>
                </li>
                <li>
                    <span class="step-number" aria-hidden="true">7</span>
                    <div>
                        <strong>Download the PNG</strong>
                        <p>
                            Click <strong>Download PNG</strong> to save the transparent result as
                            <code>no-bg.png</code> on your computer.
                        </p>
                    </div>
                </li>
                <li>
                    <span class="step-number" aria-hidden="true">8</span>
                    <div>
                        <strong>Upload Another Image</strong>
                        <p>
                            Click <strong>Upload Another</strong> to return to the upload area and
                            process a new image.
                        </p>
                    </div>
                </li>
            </ol>
        </section>

        <!-- ── Interface Explanation ── -->
        <section class="manual-section" id="interface">
            <h2>Interface Explanation</h2>
            <div class="interface-grid">

                <div class="interface-item">
                    <span class="interface-icon" aria-hidden="true">🖼️</span>
                    <h3>Upload Area</h3>
                    <p>
                        The large dashed area at the centre of the home page. Drag an image
                        directly into it, or click anywhere (or the <strong>Choose Image</strong> button)
                        to open the file browser.
                    </p>
                </div>

                <div class="interface-item">
                    <span class="interface-icon" aria-hidden="true">👁️</span>
                    <h3>Image Preview</h3>
                    <p>
                        Appears after you select a file. Shows a thumbnail of your image along with
                        the file name and size. Click <strong>Clear</strong> to deselect and choose
                        a different image.
                    </p>
                </div>

                <div class="interface-item">
                    <span class="interface-icon" aria-hidden="true">✂️</span>
                    <h3>Remove Background Button</h3>
                    <p>
                        Starts the background removal process. Only available when a valid image has
                        been selected. Clicking it uploads the image to the server and calls
                        the Pixelcut API.
                    </p>
                </div>

                <div class="interface-item">
                    <span class="interface-icon" aria-hidden="true">⏳</span>
                    <h3>Processing Status</h3>
                    <p>
                        Displays the current stage of processing with a spinner and status message.
                        The stage pills — <em>Upload → Analyse → Remove BG → Done</em> — show
                        your progress.
                    </p>
                </div>

                <div class="interface-item">
                    <span class="interface-icon" aria-hidden="true">📷</span>
                    <h3>Original Image</h3>
                    <p>
                        After processing, the original uploaded image is shown on the left for
                        easy side-by-side comparison with the result.
                    </p>
                </div>

                <div class="interface-item">
                    <span class="interface-icon" aria-hidden="true">🔲</span>
                    <h3>Result Image</h3>
                    <p>
                        Shows the processed image with the background removed. A checkerboard
                        pattern indicates transparent areas — this pattern will <em>not</em>
                        appear in the downloaded PNG file.
                    </p>
                </div>

                <div class="interface-item">
                    <span class="interface-icon" aria-hidden="true">⬇️</span>
                    <h3>Download PNG</h3>
                    <p>
                        Saves the result as <code>no-bg.png</code> — a transparent PNG you can
                        place on any background in design software, presentations, or documents.
                    </p>
                </div>

                <div class="interface-item">
                    <span class="interface-icon" aria-hidden="true">🔄</span>
                    <h3>Upload Another</h3>
                    <p>
                        Returns to the upload area so you can select and process a new image
                        without reloading the page.
                    </p>
                </div>

            </div>
        </section>

        <!-- ── Supported Formats ── -->
        <section class="manual-section" id="formats">
            <h2>Supported Formats</h2>
            <table class="format-table">
                <thead>
                    <tr>
                        <th scope="col">Format</th>
                        <th scope="col">Extension(s)</th>
                        <th scope="col">Max Size</th>
                        <th scope="col">Supported</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>JPEG</td>
                        <td>.jpg, .jpeg</td>
                        <td>8 MB</td>
                        <td class="yes">✔ Yes</td>
                    </tr>
                    <tr>
                        <td>PNG</td>
                        <td>.png</td>
                        <td>8 MB</td>
                        <td class="yes">✔ Yes</td>
                    </tr>
                    <tr>
                        <td>WebP</td>
                        <td>.webp</td>
                        <td>8 MB</td>
                        <td class="yes">✔ Yes</td>
                    </tr>
                    <tr>
                        <td>GIF</td>
                        <td>.gif</td>
                        <td>8 MB</td>
                        <td class="yes">✔ Yes</td>
                    </tr>
                </tbody>
            </table>
            <p class="note">
                <strong>Note:</strong> The maximum file size is <strong>8 MB</strong> per image.
                If your image is larger, please resize or compress it before uploading.
            </p>
        </section>

        <!-- ── Tips ── -->
        <section class="manual-section" id="tips">
            <h2>Tips for Best Results</h2>
            <ul class="tip-list">
                <li>
                    <strong>Use a clear, well-lit image.</strong>
                    Good focus, sufficient brightness, and reasonable resolution improve accuracy.
                </li>
                <li>
                    <strong>Keep the main subject fully in frame.</strong>
                    Avoid cropping the subject's edges before uploading.
                </li>
                <li>
                    <strong>Clear subject/background separation produces cleaner edges.</strong>
                    High contrast between the subject and its background helps the AI produce
                    more precise cutouts.
                </li>
                <li>
                    <strong>Avoid heavily blurred or heavily compressed images.</strong>
                    Motion blur, heavy JPEG artefacts, or very low resolution can reduce the
                    quality of the result.
                </li>
                <li>
                    <strong>Fine details such as hair or fur may have imperfections.</strong>
                    Very fine or complex edges are inherently difficult for any AI model.
                    For best results, use a photo taken against a plain background.
                </li>
                <li>
                    <strong>Try again if the result is not perfect.</strong>
                    Slight differences in lighting or composition between uploads can sometimes
                    produce a different — and better — result.
                </li>
            </ul>
        </section>

        <!-- ── FAQ ── -->
        <section class="manual-section" id="faq">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-list">

                <details class="faq-item">
                    <summary>Which image formats are supported?</summary>
                    <p>ClearCut supports JPG/JPEG, PNG, WebP, and GIF images up to 8 MB each.</p>
                </details>

                <details class="faq-item">
                    <summary>What is the maximum file size?</summary>
                    <p>
                        The maximum file size is <strong>8 MB</strong>. If your image is larger,
                        please resize or compress it with a tool such as
                        <a href="https://squoosh.app/" target="_blank" rel="noopener noreferrer">Squoosh</a>
                        before uploading.
                    </p>
                </details>

                <details class="faq-item">
                    <summary>Why does processing take time?</summary>
                    <p>
                        Your image is uploaded to the web server, then sent over the internet
                        to the Pixelcut AI API. The API analyses the image, identifies the subject,
                        and removes the background — a process that requires network communication
                        and cloud computing resources. Processing typically takes 3–15 seconds
                        depending on image size and server load.
                    </p>
                </details>

                <details class="faq-item">
                    <summary>Why does the first request sometimes take longer?</summary>
                    <p>
                        On some server configurations, the first request may take slightly longer
                        while the server initialises its connection to the Pixelcut API.
                        Subsequent requests within the same session are usually faster.
                    </p>
                </details>

                <details class="faq-item">
                    <summary>What happens if the Pixelcut API fails?</summary>
                    <p>
                        If the Pixelcut API is temporarily unavailable or returns an error,
                        ClearCut displays a friendly error message and allows you to try again.
                        No image data is retained. Please check your internet connection and
                        retry after a moment.
                    </p>
                </details>

                <details class="faq-item">
                    <summary>Why is the result transparent?</summary>
                    <p>
                        The result is a PNG file with a fully transparent background. This lets
                        you place the subject on any colour, gradient, or image in design software,
                        presentations, or documents. The grey checkerboard visible in the preview
                        represents transparency — it will <strong>not</strong> appear in the
                        downloaded file.
                    </p>
                </details>

                <details class="faq-item">
                    <summary>How do I download the result?</summary>
                    <p>
                        After processing, click the <strong>Download PNG</strong> button.
                        The file is saved to your computer as <code>no-bg.png</code>.
                        Open it in any application that supports transparent PNG files,
                        such as Photoshop, GIMP, Canva, or PowerPoint.
                    </p>
                </details>

                <details class="faq-item">
                    <summary>Is my Pixelcut API key visible to users?</summary>
                    <p>
                        No. The API key is stored only in <code>config.php</code> on the server.
                        It is never included in HTML, JavaScript, or any client-side code, and
                        is never transmitted to or visible in the browser.
                    </p>
                </details>

                <details class="faq-item">
                    <summary>Are my uploaded images stored permanently?</summary>
                    <p>
                        No. Uploaded images are stored temporarily to allow Pixelcut to process
                        them, and are deleted from the server as soon as processing is complete.
                        Result files are automatically removed after one hour.
                    </p>
                </details>

            </div>
        </section>

    </div><!-- /.container -->
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/manual.js"></script>
</body>
</html>
