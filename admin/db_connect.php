<?php
require_once __DIR__ . '/../includes/config.php';

mysqli_report(MYSQLI_REPORT_OFF);

// Try configured socket first, then default, then fallback
$conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, DB_SOCKET);
if ($conn->connect_error) {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
}
if ($conn->connect_error) {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT, '/tmp/mysql.sock');
}

if ($conn->connect_error) {
    $allow_without_db = false;
    $page = $_GET['page'] ?? '';
    $action = $_GET['action'] ?? '';

    if (in_array($page, ['backup', 'site_settings']) || $action === 'import_database') {
        $allow_without_db = true;
    }

    if ($allow_without_db) {
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS);
        if ($conn->connect_error) {
            $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, '', DB_PORT, '/tmp/mysql.sock');
        }
        if ($conn->connect_error) {
            error_log("Database connection failed: " . $conn->connect_error);
            die("Database connection failed. Please contact the administrator.");
        }
        $GLOBALS['db_missing'] = true;
    } else {
        error_log("Database connection failed: " . $conn->connect_error);
        die("Database connection failed. Please contact the administrator.");
    }
} else {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    $conn->set_charset("utf8mb4");
    $GLOBALS['db_missing'] = false;
}
