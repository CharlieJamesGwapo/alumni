<?php
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');
require_once 'admin/db_connect.php';
require_once 'auth_functions.php';
require_once 'includes/security.php';

try {
    if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['otp'])) {
        throw new Exception('Missing required fields');
    }

    $email = sanitize_email($_POST['email']);
    $password = $_POST['password'];
    $otp = trim($_POST['otp']);

    // Rate limit: count recent failed OTP attempts for this email
    $lockout_minutes = OTP_LOCKOUT_MINUTES;
    $stmt = $conn->prepare("SELECT COUNT(*) as fails FROM otp_verifications WHERE email = ? AND used = 0 AND expires_at < NOW() AND created_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)");
    $stmt->bind_param("si", $email, $lockout_minutes);
    $stmt->execute();
    $failed_otps = $stmt->get_result()->fetch_assoc()['fails'] ?? 0;
    $stmt->close();

    if ($failed_otps >= OTP_MAX_ATTEMPTS) {
        throw new Exception('Too many failed attempts. Please wait and try again.');
    }

    // Verify OTP
    $stmt = $conn->prepare("SELECT id FROM otp_verifications WHERE email = ? AND otp = ? AND used = 0 AND expires_at > NOW() ORDER BY expires_at DESC LIMIT 1");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $otp_result = $stmt->get_result();
    $stmt->close();

    if ($otp_result->num_rows === 0) {
        throw new Exception('Invalid or expired OTP');
    }

    // Verify user account
    $stmt = $conn->prepare("SELECT * FROM alumnus_bio WHERE email = ? AND status = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows === 0) {
        throw new Exception('Account not found or not activated');
    }

    $user = $result->fetch_assoc();

    // Verify password with auto-upgrade from MD5
    $password_valid = false;
    if (password_verify($password, $user['password'])) {
        $password_valid = true;
    } elseif (md5($password) === $user['password']) {
        $new_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE alumnus_bio SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $new_hash, $user['id']);
        $stmt->execute();
        $stmt->close();
        $password_valid = true;
    }

    if (!$password_valid) {
        throw new Exception('Invalid credentials');
    }

    // Mark OTP as used
    $stmt = $conn->prepare("UPDATE otp_verifications SET used = 1 WHERE email = ? AND otp = ?");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $stmt->close();

    // Set up secure session
    session_regenerate_id(true);
    $_SESSION = [];

    $_SESSION['login'] = true;
    $_SESSION['login_id'] = $user['id'];
    $_SESSION['login_type'] = 'alumni';
    $_SESSION['bio'] = $user;
    $_SESSION['name'] = $user['firstname'] . ' ' . $user['lastname'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['verified'] = true;

    // Handle Remember Me
    if (isset($_POST['remember']) && $_POST['remember'] === 'true') {
        setRememberMeCookie($user['id']);
    } else {
        clearRememberToken($user['id']);
    }

    $_SESSION['show_edit_on_login'] = true;

    echo json_encode([
        'status' => 'success',
        'message' => 'Login successful',
        'redirect' => 'home.php'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
