<?php
session_start();
require_once 'admin/db_connect.php';
header('Content-Type: application/json');

if (!isset($_POST['otp']) || !isset($_POST['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Missing OTP or email.']);
    exit;
}

$otp = trim($_POST['otp']);
$email = trim($_POST['email']);

// Validate OTP format
if (!preg_match('/^\d{6}$/', $otp)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid OTP format. Please enter 6 digits.']);
    exit;
}

try {
    // Check if there's a valid OTP in the database
    $stmt = $conn->prepare("SELECT *, TIMESTAMPDIFF(MINUTE, NOW(), expires_at) as minutes_left 
                           FROM otp_verifications 
                           WHERE email = ? 
                           AND otp = ? 
                           AND used = 0 
                           AND expires_at > NOW() 
                           ORDER BY id DESC LIMIT 1");
    $stmt->bind_param("ss", $email, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Check if OTP exists but is expired or used
        $stmt = $conn->prepare("SELECT expires_at, used, TIMESTAMPDIFF(MINUTE, NOW(), expires_at) as minutes_left 
                              FROM otp_verifications 
                              WHERE email = ? AND otp = ? 
                              ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("ss", $email, $otp);
        $stmt->execute();
        $expiry_check = $stmt->get_result();
        
        if ($expiry_check->num_rows > 0) {
            $row = $expiry_check->fetch_assoc();
            if ($row['minutes_left'] < 0) {
                echo json_encode(['status' => 'error', 'message' => 'This OTP has expired. Please request a new one.']);
                exit;
            } else if ($row['used'] == 1) {
                echo json_encode(['status' => 'error', 'message' => 'This OTP has already been used. Please request a new one.']);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid OTP. Please check and try again.']);
            exit;
        }
    }

    // Valid OTP found, proceed with verification

// Mark OTP as used
$stmt = $conn->prepare("UPDATE otp_verifications SET used = 1 WHERE email = ? AND otp = ?");
$stmt->bind_param("ss", $email, $otp);
$stmt->execute();

// Set verification status in session
$_SESSION['otp_verified'] = true;
$_SESSION['verified_email'] = $email;
$_SESSION['verification_time'] = time();

// Get user data
$stmt = $conn->prepare("SELECT * FROM alumnus_bio WHERE email = ? AND status = 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    $_SESSION['temp_user'] = $user;
    
    // Set all required session variables
    $_SESSION['login'] = true;
    $_SESSION['login_type'] = 'alumni';
    $_SESSION['bio'] = $user;
    $_SESSION['id'] = $user['id'];
    $_SESSION['name'] = $user['firstname'] . ' ' . $user['lastname'];
    $_SESSION['email'] = $user['email'];
    
    // Try to update last login timestamp if the column exists
    try {
        $stmt = $conn->prepare("UPDATE alumnus_bio SET last_login = NOW() WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
        }
    } catch (Exception $e) {
        // Ignore the error if the column doesn't exist
        error_log("Notice: last_login column might not exist - " . $e->getMessage());
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => 'OTP verified successfully',
        'redirect' => 'home.php'
    ]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
}
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>