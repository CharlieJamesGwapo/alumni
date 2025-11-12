<?php
session_start();
require_once 'auth_functions.php';

// Check if user is not already logged in
if (!isset($_SESSION['login']) && isset($_COOKIE['remember_token'])) {
    // Validate remember token and auto-login if valid
    $token = $_COOKIE['remember_token'];
    if (!validateRememberToken($token)) {
        // Invalid or expired token, remove it
        removeRememberToken();
    }
}

// If still not logged in after auto-login attempt, redirect to login page
if (!isset($_SESSION['login']) && basename($_SERVER['PHP_SELF']) !== 'login.php') {
    header('Location: login.php');
    exit();
}
?>
