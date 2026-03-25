<?php
session_start();
// Dashboard redirects to the main home page
if (!isset($_SESSION['login_id'])) {
    header("Location: login.php");
    exit;
}
header("Location: home.php");
exit;
?>
