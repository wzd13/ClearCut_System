/**
 * ClearCut — app.js
 *
 * Handles:
 *  - Drag-and-drop and click-to-browse file selection
 *  - Client-side file validation (type, size)
 *  - Image preview
 *  - AJAX upload to api/process.php using FormData
 *  - Processing status / stage indicator
 *  - Result display (original + background-removed)
 *  - Download link
 *  - "Upload Another" reset
 */

(function () {
    'use strict';

    // ─── DOM references ───────────────────────────────────────────────────────

    const dropZone          = document.getElementById('dropZone');
    const fileInput         = document.getElementById('fileInput');
    const chooseBtn         = document.getElementById('chooseBtn');
    const uploadSection     = document.getElementById('uploadSection');
    const previewCard       = document.getElementById('previewCard');
    const previewImage      = document.getElementById('previewImage');
    const previewFilename   = document.getElementById('previewFilename');
    const previewFilesize   = document.getElementById('previewFilesize');
    const clearBtn          = document.getElementById('clearBtn');
    const processBtn        = document.getElementById('processBtn');
    const errorAlert        = document.getElementById('errorAlert');
    const errorMessage      = document.getElementById('errorMessage');
    const processingSection = document.getElementById('processingSection');
    const processingStatus  = document.getElementById('processingStatus');
    const resultSection     = document.getElementById('resultSection');
    const originalImage     = document.getElementById('originalImage');
    const resultImage       = document.getElementById('resultImage');
    const downloadBtn       = document.getElementById('downloadBtn');
    const uploadAnotherBtn  = document.getElementById('uploadAnotherBtn');

    // Processing stage pill elements
    const stage1 = document.getElementById('stage1');
    const stage2 = document.getElementById('stage2');
    const stage3 = document.getElementById('stage3');
    const stage4 = document.getElementById('stage4');
    const stages = [stage1, stage2, stage3, stage4];

    // ─── State ────────────────────────────────────────────────────────────────

    let selectedFile  = null; // The File object the user selected
    let isProcessing  = false; // Guard against duplicate submissions

    // ─── Constants ────────────────────────────────────────────────────────────

    const MAX_SIZE      = 8 * 1024 * 1024; // 8 MB
    const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    const ALLOWED_EXTS  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    // ─── Utility ──────────────────────────────────────────────────────────────

    function formatBytes(bytes) {
        if (bytes < 1024)     return bytes + ' B';
        if (bytes < 1048576)  return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(2) + ' MB';
    }

    function getExtension(filename) {
        return filename.split('.').pop().toLowerCase();
    }

    function delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // ─── UI helpers ───────────────────────────────────────────────────────────

    function showError(message) {
        errorMessage.textContent = message;
        errorAlert.hidden = false;
        errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function hideError() {
        errorAlert.hidden = true;
        errorMessage.textContent = '';
    }

    /**
     * Activate a processing stage pill.
     * Stages before the active one are marked "done" (green).
     * @param {number} active - 1-based stage index
     */
    function setStage(active) {
        stages.forEach(function (el, idx) {
            el.classList.toggle('active', idx + 1 === active);
            el.classList.toggle('done',   idx + 1 < active);
        });
    }

    // ─── File validation ──────────────────────────────────────────────────────

    /**
     * Returns a human-readable error string, or null when the file is valid.
     * The PHP backend always re-validates; this is a quick UX check.
     */
    function validateFile(file) {
        const ext = getExtension(file.name);

        if (!ALLOWED_TYPES.includes(file.type) || !ALLOWED_EXTS.includes(ext)) {
            return 'Invalid image format. Supported formats: JPG, JPEG, PNG, WEBP, GIF.';
        }
        if (file.size > MAX_SIZE) {
            return 'File exceeds the 8 MB limit. Please choose a smaller image.';
        }
        if (file.size === 0) {
            return 'The selected file is empty.';
        }
        return null;
    }

    // ─── Preview ──────────────────────────────────────────────────────────────

    function showPreview(file) {
        selectedFile = file;

        const reader = new FileReader();

        reader.onload = function (e) {
            previewImage.src         = e.target.result;
            previewFilename.textContent = file.name;
            previewFilesize.textContent = formatBytes(file.size);

            dropZone.hidden    = true;
            previewCard.hidden = false;
            hideError();
        };

        reader.onerror = function () {
            showError('The image could not be read. Please try a different file.');
        };

        reader.readAsDataURL(file);
    }

    // ─── Reset / clear ────────────────────────────────────────────────────────

    function resetUpload() {
        selectedFile = null;
        fileInput.value = '';
        previewImage.src = '';
        previewCard.hidden = true;
        dropZone.hidden    = false;
        hideError();
    }

    function showUploadSection() {
        resetUpload();
        uploadSection.hidden     = false;
        processingSection.hidden = true;
        resultSection.hidden     = true;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // ─── File selection handlers ──────────────────────────────────────────────

    function handleFileSelect(file) {
        if (!file) return;
        const error = validateFile(file);
        if (error) {
            showError(error);
            return;
        }
        showPreview(file);
    }

    // Drag over — highlight the zone
    dropZone.addEventListener('dragover', function (e) {
        e.preventDefault();
        dropZone.classList.add('drag-over');
    });

    // Drag leave — remove highlight
    dropZone.addEventListener('dragleave', function (e) {
        // Only remove if leaving the drop zone itself (not a child element)
        if (!dropZone.contains(e.relatedTarget)) {
            dropZone.classList.remove('drag-over');
        }
    });

    // Drop — read the dropped file
    dropZone.addEventListener('drop', function (e) {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        const file = e.dataTransfer.files[0];
        handleFileSelect(file);
    });

    // Click on the drop zone (but not the Choose Image button — it has its own listener)
    dropZone.addEventListener('click', function (e) {
        if (e.target !== chooseBtn && !chooseBtn.contains(e.target)) {
            fileInput.click();
        }
    });

    // Keyboard accessibility for the drop zone
    dropZone.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();
            fileInput.click();
        }
    });

    // "Choose Image" button
    chooseBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        fileInput.click();
    });

    // Native file input change
    fileInput.addEventListener('change', function () {
        handleFileSelect(this.files[0]);
    });

    // ─── Clear button ─────────────────────────────────────────────────────────

    clearBtn.addEventListener('click', function () {
        resetUpload();
    });

    // ─── Process — send image to backend ──────────────────────────────────────

    processBtn.addEventListener('click', function () {
        if (!selectedFile || isProcessing) return;
        processImage();
    });

    async function processImage() {
        isProcessing        = true;
        processBtn.disabled = true;

        // Switch to processing view
        previewCard.hidden       = true;
        dropZone.hidden          = true;
        uploadSection.hidden     = false;
        processingSection.hidden = false;
        resultSection.hidden     = true;
        hideError();

        // Stage 1: Uploading
        setStage(1);
        processingStatus.textContent = 'Uploading image…';

        const formData = new FormData();
        formData.append('image', selectedFile);

        // Stage 2: Analyse (shown while the request is in flight)
        const stage2Timer = setTimeout(function () {
            setStage(2);
            processingStatus.textContent = 'Sending image to Pixelcut…';
        }, 900);

        // Stage 3: Remove BG (still waiting for the API)
        const stage3Timer = setTimeout(function () {
            setStage(3);
            processingStatus.textContent = 'Removing background…';
        }, 2200);

        let response;
        try {
            response = await fetch('api/process.php', {
                method: 'POST',
                body: formData,
            });
        } catch (networkErr) {
            clearTimeout(stage2Timer);
            clearTimeout(stage3Timer);
            handleProcessingError('Unable to connect. Please check your internet connection and try again.');
            return;
        }

        clearTimeout(stage2Timer);
        clearTimeout(stage3Timer);

        let data;
        try {
            data = await response.json();
        } catch (parseErr) {
            handleProcessingError('An unexpected error occurred. Please try again.');
            return;
        }

        if (!data.success) {
            handleProcessingError(data.message || 'Background removal failed. Please try again.');
            return;
        }

        // Stage 4: Done
        setStage(4);
        processingStatus.textContent = 'Complete!';

        // Brief pause so the user can see the "Done" state
        await delay(500);

        showResult(data.result);
    }

    /** Show the error, reset button state, return to the preview card. */
    function handleProcessingError(message) {
        isProcessing        = false;
        processBtn.disabled = false;

        processingSection.hidden = true;
        uploadSection.hidden     = false;
        dropZone.hidden          = true;
        previewCard.hidden       = false;

        showError(message);
    }

    // ─── Show result ──────────────────────────────────────────────────────────

    function showResult(resultId) {
        isProcessing        = false;
        processBtn.disabled = false;

        // Original image — reuse the DataURL already in the preview
        originalImage.src = previewImage.src;
        originalImage.alt = 'Original: ' + (selectedFile ? selectedFile.name : 'image');

        // Result image — served through the PHP download endpoint (inline mode)
        // so Apache never needs to serve outputs/ files directly.
        resultImage.src = 'api/download.php?id=' + encodeURIComponent(resultId) + '&display=1&t=' + Date.now();
        resultImage.alt = 'Background removed result';

        // Download link points to the secure download endpoint
        downloadBtn.href = 'api/download.php?id=' + encodeURIComponent(resultId);

        // Switch to result view
        processingSection.hidden = true;
        uploadSection.hidden     = true;
        resultSection.hidden     = false;

        resultSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // ─── Upload Another ───────────────────────────────────────────────────────

    uploadAnotherBtn.addEventListener('click', function () {
        showUploadSection();
    });

}());
