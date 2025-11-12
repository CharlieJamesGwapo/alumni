<?php
session_start();
include 'db_connect.php';
include 'log_activity.php';

// Log logout activity before destroying session
if(isset($_SESSION['login_id'])) {
    log_logout($_SESSION['login_id']);
}

// Destroy session and redirect
session_destroy();
header('location:login.php');
?>
