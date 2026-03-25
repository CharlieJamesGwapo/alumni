<?php
/**
 * Centralized Security Helpers
 */

/**
 * Escape output for HTML context (prevents XSS).
 */
function esc($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Generate or retrieve CSRF token for current session.
 */
function csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(CSRF_TOKEN_LENGTH ?? 32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate a submitted CSRF token.
 */
function csrf_validate($token) {
    if (empty($token) || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output a hidden CSRF input field.
 */
function csrf_field() {
    return '<input type="hidden" name="csrf_token" value="' . esc(csrf_token()) . '">';
}

/**
 * Sanitize integer input.
 */
function sanitize_int($val) {
    return (int)($val ?? 0);
}

/**
 * Sanitize email input.
 */
function sanitize_email($val) {
    return filter_var($val ?? '', FILTER_SANITIZE_EMAIL);
}

/**
 * Sanitize a string: trim and strip tags.
 */
function sanitize_string($val) {
    return trim(strip_tags($val ?? ''));
}

/**
 * Validate uploaded file is an allowed image.
 */
function validate_image_upload($file, $max_size = null) {
    $max_size = $max_size ?? (defined('UPLOAD_MAX_SIZE') ? UPLOAD_MAX_SIZE : 10 * 1024 * 1024);
    $allowed = defined('ALLOWED_IMAGE_TYPES') ? ALLOWED_IMAGE_TYPES : ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    if (empty($file['tmp_name'])) {
        return ['valid' => false, 'ext' => '', 'error' => 'No file uploaded'];
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed)) {
        return ['valid' => false, 'ext' => $ext, 'error' => 'Invalid image type. Allowed: ' . implode(', ', $allowed)];
    }

    if ($file['size'] > $max_size) {
        return ['valid' => false, 'ext' => $ext, 'error' => 'File too large. Max: ' . round($max_size / 1024 / 1024) . 'MB'];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $mime_map = [
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'webp' => ['image/webp'],
    ];

    if (!isset($mime_map[$ext]) || !in_array($mime, $mime_map[$ext])) {
        return ['valid' => false, 'ext' => $ext, 'error' => 'File content does not match extension'];
    }

    return ['valid' => true, 'ext' => $ext, 'error' => ''];
}

/**
 * Generate a safe filename for uploads.
 */
function safe_filename($prefix, $ext) {
    return $prefix . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
}

/**
 * Ensure upload directory exists with safe permissions.
 */
function ensure_upload_dir($path) {
    if (!is_dir($path)) {
        mkdir($path, defined('UPLOAD_DIR_PERMISSIONS') ? UPLOAD_DIR_PERMISSIONS : 0755, true);
    }
}

/**
 * Send a JSON response and exit.
 */
function json_response($status, $message, $extra = []) {
    header('Content-Type: application/json');
    echo json_encode(array_merge(['status' => $status, 'message' => $message], $extra));
    exit;
}

/**
 * Set standard security headers.
 */
function set_security_headers() {
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: DENY");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: geolocation=(), microphone=(), camera=()");
}
