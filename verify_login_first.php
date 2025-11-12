<?php
date_default_timezone_set('Asia/Manila');
session_start();
require_once 'admin/db_connect.php';
header('Content-Type: application/json');

try {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        throw new Exception('Email and password are required');
    }

    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }

    $stmt = $conn->prepare("SELECT id, password, status FROM alumnus_bio WHERE email = ? LIMIT 1");
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    if (!$stmt->execute()) {
        throw new Exception("Database error: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception('Account not found');
    }

    $user = $result->fetch_assoc();

    if ($user['status'] != 1) {
        throw new Exception('Your account is pending validation');
    }

    if (!password_verify($password, $user['password']) && md5($password) !== $user['password']) {
        throw new Exception('Invalid password');
    }

    // Set session for OTP step
    $_SESSION['otp_step_verified'] = true;
    $_SESSION['otp_step_email'] = $email;

    echo json_encode([
        'status' => 'success',
        'message' => 'Credentials verified. OTP will be sent.'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
