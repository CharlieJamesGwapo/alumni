<?php
session_start();
require_once __DIR__ . '/admin/db_connect.php';

header('Content-Type: application/json');

function log_error($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, __DIR__ . '/logs/reset_password.log');
}

try {
    $otp = trim($_POST['otp'] ?? '');
    $contact = $_SESSION['reset_contact'] ?? '';
    $alumni_id = $_SESSION['reset_alumni_id'] ?? '';

    if (empty($otp) || empty($contact) || empty($alumni_id)) {
        throw new Exception('Invalid request. Please try again.');
    }

    // Verify OTP
    $stmt = $conn->prepare("
        SELECT * FROM password_resets 
        WHERE alumni_id = ? 
        AND contact = ? 
        AND otp = ? 
        AND expires_at > NOW() 
        AND used = 0 
        ORDER BY created_at DESC 
        LIMIT 1
    ");
    
    $stmt->bind_param("sss", $alumni_id, $contact, $otp);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('Invalid or expired OTP. Please try again.');
    }

    // Mark OTP as used
    $reset_id = $result->fetch_assoc()['id'];
    $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
    $stmt->bind_param("i", $reset_id);
    $stmt->execute();

    // Set verification session
    $_SESSION['reset_verified'] = true;
    $_SESSION['reset_expires'] = time() + 300; // 5 minutes to complete reset

    echo json_encode([
        'status' => 'success',
        'message' => 'OTP verified successfully'
    ]);

} catch (Exception $e) {
    log_error("OTP Verification Error: {$e->getMessage()} for contact: {$contact}");
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
