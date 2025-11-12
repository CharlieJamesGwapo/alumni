<?php
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');
require_once 'admin/db_connect.php';
require_once 'auth_functions.php';

try {
    if (!isset($_POST['email']) || !isset($_POST['password']) || !isset($_POST['otp'])) {
        throw new Exception('Missing required fields');
    }

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $otp = trim($_POST['otp']);

    // Debug log
    error_log("OTP verify attempt: $email, $otp", 3, __DIR__ . '/otp_error.log');

    // Handle remember me preference
    $remember = isset($_POST['remember']) && $_POST['remember'] === 'true';

    $stmt = $conn->prepare("
        SELECT * FROM otp_verifications 
        WHERE email = ? 
        AND otp = ? 
        AND used = 0 
        AND expires_at > NOW()
        ORDER BY expires_at DESC 
        LIMIT 1
    ");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $otp_result = $stmt->get_result();

    if ($otp_result->num_rows === 0) {
        error_log("OTP verify failed for $email, $otp", 3, __DIR__ . '/otp_error.log');
        throw new Exception('Invalid or expired OTP');
    }

    $stmt = $conn->prepare("SELECT * FROM alumnus_bio WHERE email = ? AND status = 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Account not found or not activated');
    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password']) && md5($password) !== $user['password']) {
        throw new Exception('Invalid credentials');
    }

    $update_stmt = $conn->prepare("UPDATE otp_verifications SET used = 1 WHERE email = ? AND otp = ?");
    $update_stmt->bind_param("ss", $email, $otp);
    $update_stmt->execute();

    session_regenerate_id(true);
    $_SESSION = array();

    $_SESSION['login'] = true;
    $_SESSION['login_id'] = $user['id'];
    $_SESSION['login_type'] = 'alumni';
    $_SESSION['bio'] = $user;
    $_SESSION['name'] = $user['firstname'] . ' ' . $user['lastname'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['verified'] = true;

    // Handle Remember Me functionality
    if (isset($_POST['remember']) && $_POST['remember'] === 'true') {
        $credentials = [
            'email' => $email,
            'password' => $password  // Store the hashed password
        ];
        setRememberMeCookie($user['id'], json_encode($credentials));
    } else {
        clearRememberToken($user['id']);
    }

    // Automatically open edit profile after login for all users who log in
    $_SESSION['show_edit_on_login'] = true;

    // If you do NOT have a last_login column, comment out these lines:
    // $stmt = $conn->prepare("UPDATE alumnus_bio SET last_login = NOW() WHERE id = ?");
    // $stmt->bind_param("i", $user['id']);
    // $stmt->execute();

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
?>
