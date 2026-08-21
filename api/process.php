<?php
/**
 * ClearCut — api/process.php
 *
 * Receives an uploaded image, validates it, sends it to the Pixelcut
 * Background Removal API, and returns a JSON result identifier.
 *
 * Always returns JSON:
 *   { "success": true,  "result": "<result_id>" }
 *   { "success": false, "message": "Human-readable error." }
 */

require_once dirname(__DIR__) . '/config.php';

// Force JSON output regardless of what PHP might normally print.
header('Content-Type: application/json; charset=UTF-8');

// Only allow POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Method not allowed.']));
}

// ─── Helper functions ────────────────────────────────────────────────────────

/** Delete a file silently if it exists. */
function cleanup_file(string $path): void
{
    if (file_exists($path)) {
        @unlink($path);
    }
}

/** Delete output PNG files older than 1 hour (lightweight cleanup). */
function cleanup_old_outputs(): void
{
    $files = glob(OUTPUT_DIR . '*_result.png');
    if (empty($files)) {
        return;
    }
    $expiry = time() - 3600; // 1 hour
    foreach ($files as $file) {
        if (filemtime($file) < $expiry) {
            @unlink($file);
        }
    }
}

/** Send a JSON error response and terminate. */
function error_response(string $message, int $httpCode = 400): never
{
    http_response_code($httpCode);
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}

// ─── 1. Check that a file was uploaded ───────────────────────────────────────

if (!isset($_FILES['image'])) {
    error_response('No image was uploaded.');
}

$file = $_FILES['image'];

// Map PHP upload error codes to friendly messages.
if ($file['error'] !== UPLOAD_ERR_OK) {
    $uploadErrors = [
        UPLOAD_ERR_INI_SIZE   => 'The file exceeds the server upload size limit.',
        UPLOAD_ERR_FORM_SIZE  => 'The file exceeds the allowed form size limit.',
        UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded. Please try again.',
        UPLOAD_ERR_NO_FILE    => 'No file was selected.',
        UPLOAD_ERR_NO_TMP_DIR => 'The server temporary folder is missing.',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write the uploaded file to disk.',
        UPLOAD_ERR_EXTENSION  => 'A server extension rejected the upload.',
    ];
    $msg = $uploadErrors[$file['error']] ?? 'The image upload failed. Please try again.';
    error_response($msg);
}

// ─── 2. Validate file size ────────────────────────────────────────────────────

if ($file['size'] === 0) {
    error_response('The uploaded file is empty.');
}

if ($file['size'] > MAX_FILE_SIZE) {
    error_response('File exceeds the 8 MB limit. Please choose a smaller image.');
}

// ─── 3. Validate MIME type with finfo (never trust $_FILES['type']) ───────────

$finfo    = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mimeType, ALLOWED_MIME_TYPES, true)) {
    error_response('Invalid image format. Supported formats: JPG, JPEG, PNG, WEBP, GIF.');
}

// ─── 4. Validate file extension ───────────────────────────────────────────────

$originalName = basename($file['name']);
$ext          = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
    error_response('Invalid file extension. Supported formats: JPG, JPEG, PNG, WEBP, GIF.');
}

// ─── 5. Confirm the file is an actual readable image ──────────────────────────

$imageInfo = @getimagesize($file['tmp_name']);
if ($imageInfo === false) {
    error_response('The image could not be read. Please upload a valid image file.');
}

// ─── 6. Move the file to uploads/ with a secure random filename ───────────────

if (!is_dir(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Generate a random, unguessable filename — never use the original name.
$randomName   = bin2hex(random_bytes(16)) . '.' . $ext;
$uploadedPath = UPLOAD_DIR . $randomName;

if (!move_uploaded_file($file['tmp_name'], $uploadedPath)) {
    error_response('Failed to save the uploaded image. Please try again.');
}

// ─── 7. Call the Pixelcut Background Removal API (direct file upload) ────────
// Sending the file as multipart/form-data avoids the need for a publicly
// accessible URL, so this works correctly on localhost/dev environments.

$ch = curl_init(PIXELCUT_API_ENDPOINT);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => [
        'image'  => new CURLFile($uploadedPath, $mimeType, $randomName),
        'format' => 'png',
    ],
    CURLOPT_HTTPHEADER     => [
        'Accept: application/json',
        'X-API-KEY: ' . PIXELCUT_API_KEY,
    ],
    CURLOPT_TIMEOUT        => 90,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);

$apiResponse = curl_exec($ch);
$httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError   = curl_error($ch);
curl_close($ch);

// Temporary upload is no longer needed after the API call.
cleanup_file($uploadedPath);

if ($curlError) {
    error_log('[ClearCut] cURL error calling Pixelcut API: ' . $curlError);
    error_response('Unable to connect to Pixelcut. Please check your internet connection and try again.', 503);
}

if ($httpCode !== 200) {
    // Log the HTTP code for debugging without exposing it to the user.
    error_log('[ClearCut] Pixelcut API returned HTTP ' . $httpCode . ': ' . $apiResponse);
    error_response('Pixelcut API request failed. Please try again in a moment.', 502);
}

// ─── 9. Parse the API response ────────────────────────────────────────────────

$data = json_decode($apiResponse, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log('[ClearCut] Pixelcut returned invalid JSON: ' . $apiResponse);
    error_response('Invalid response received from Pixelcut. Please try again.', 502);
}

// Pixelcut returns { "result_url": "https://..." }
$resultUrl = $data['result_url'] ?? $data['image_url'] ?? null;

if (empty($resultUrl) || !filter_var($resultUrl, FILTER_VALIDATE_URL)) {
    error_log('[ClearCut] Pixelcut response missing result_url: ' . $apiResponse);
    error_response('Background removal failed. No result was returned by Pixelcut.', 502);
}

// ─── 10. Download the result PNG from Pixelcut ────────────────────────────────

$ch2 = curl_init($resultUrl);
curl_setopt_array($ch2, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS      => 5,
    CURLOPT_TIMEOUT        => 60,
    CURLOPT_CONNECTTIMEOUT => 15,
    CURLOPT_SSL_VERIFYPEER => true,
    CURLOPT_SSL_VERIFYHOST => 2,
]);

$resultData      = curl_exec($ch2);
$resultHttpCode  = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
$resultCurlError = curl_error($ch2);
curl_close($ch2);

if ($resultCurlError || $resultHttpCode !== 200 || empty($resultData)) {
    error_log('[ClearCut] Failed to download result from: ' . $resultUrl);
    error_response('Failed to retrieve the processed image. Please try again.', 502);
}

// ─── 11. Verify the downloaded content is actually a PNG ──────────────────────

// Write to a system temp file so finfo can inspect the actual bytes.
$tmpResult = tempnam(sys_get_temp_dir(), 'clearcut_result_');
file_put_contents($tmpResult, $resultData);

$finfo2     = finfo_open(FILEINFO_MIME_TYPE);
$resultMime = finfo_file($finfo2, $tmpResult);
finfo_close($finfo2);

if ($resultMime !== 'image/png') {
    @unlink($tmpResult);
    error_log('[ClearCut] Result file is not a PNG, got: ' . $resultMime);
    error_response('The result could not be verified as a PNG. Please try again.', 502);
}

// ─── 12. Save the result to outputs/ with a secure random filename ────────────

if (!is_dir(OUTPUT_DIR)) {
    mkdir(OUTPUT_DIR, 0755, true);
}

// Opportunistically clean up old result files (> 1 hour old).
cleanup_old_outputs();

$resultId       = bin2hex(random_bytes(16));
$resultFilename = $resultId . '_result.png';
$resultPath     = OUTPUT_DIR . $resultFilename;

if (!rename($tmpResult, $resultPath)) {
    // rename() can fail across filesystem boundaries; fall back to copy + delete.
    file_put_contents($resultPath, $resultData);
    @unlink($tmpResult);
}

// ─── 13. Return the result identifier to the browser ─────────────────────────

echo json_encode([
    'success' => true,
    'result'  => $resultId,
]);
