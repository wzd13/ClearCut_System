<?php
/**
 * ClearCut — api/download.php
 *
 * Securely serves a processed result PNG as a file download.
 *
 * Usage:  api/download.php?id=<result_id>
 *
 * The result_id must be exactly 32 lowercase hexadecimal characters
 * (the format produced by bin2hex(random_bytes(16)) in process.php).
 * Any other value is rejected to prevent directory traversal.
 */

require_once dirname(__DIR__) . '/config.php';

// Only allow GET.
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: text/plain');
    exit('Method not allowed.');
}

// ─── 1. Validate the result identifier ───────────────────────────────────────

$id = $_GET['id'] ?? '';

// Exactly 32 lowercase hex chars — reject anything else immediately.
if (!preg_match('/^[a-f0-9]{32}$/', $id)) {
    http_response_code(400);
    header('Content-Type: text/plain');
    exit('Invalid request.');
}

// ─── 2. Build the expected file path ─────────────────────────────────────────

$filename = $id . '_result.png';
$filepath = OUTPUT_DIR . $filename;

// ─── 3. Prevent directory traversal with realpath() ──────────────────────────

$realOutputDir = realpath(OUTPUT_DIR);
$realFilepath  = realpath($filepath);

// realpath() returns false when the file does not exist.
if ($realFilepath === false || strpos($realFilepath, $realOutputDir . DIRECTORY_SEPARATOR) !== 0) {
    error_log('[ClearCut] download.php 403 — id=' . $id . ' realOutputDir=' . $realOutputDir . ' realFilepath=' . var_export($realFilepath, true));
    http_response_code(403);
    header('Content-Type: text/plain');
    exit('Access denied.');
}

// ─── 4. Confirm the file exists ───────────────────────────────────────────────

if (!file_exists($realFilepath)) {
    http_response_code(404);
    header('Content-Type: text/plain');
    exit('The download file could not be found. It may have expired.');
}

// ─── 5. Confirm the file is actually a PNG ───────────────────────────────────

$finfo    = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $realFilepath);
finfo_close($finfo);

if ($mimeType !== 'image/png') {
    http_response_code(400);
    header('Content-Type: text/plain');
    exit('Invalid file type.');
}

// ─── 6. Stream the file ───────────────────────────────────────────────────────
// ?display=1  → inline (used by the browser preview <img> tag)
// default     → attachment (triggers Save As dialog for the Download button)

$inline = isset($_GET['display']) && $_GET['display'] === '1';

header('Content-Type: image/png');
header('Content-Disposition: ' . ($inline ? 'inline' : 'attachment; filename="no-bg.png"'));
header('Content-Length: ' . filesize($realFilepath));
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($realFilepath);
exit;
