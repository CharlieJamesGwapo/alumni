<?php
session_start();
require_once 'auth_functions.php';

// Clear remember me token if it exists
if (isset($_SESSION['login_id'])) {
    clearRememberToken($_SESSION['login_id']);
}

// Clear all session data
session_unset();
session_destroy();

// Clear session cookie
if (ini_get("session.use_cookies")) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Redirect to index page
header("Location: index.php");
exit;
