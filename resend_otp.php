<?php
session_start();
require 'admin/db_connect.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid session. Please start over.']);
    exit;
}

try {
    $email = $_SESSION['reset_email'];
    $user_id = $_SESSION['reset_user_id'];

    // Get user details
    $stmt = $conn->prepare("SELECT CONCAT(firstname, ' ', lastname) as fullname FROM alumni_ids WHERE id = ? AND email = ?");
    $stmt->bind_param("is", $user_id, $email);
    $stmt->execute();
    $stmt->bind_result($user_name);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
        exit;
    }
    $stmt->close();

    // Generate new OTP
    $otp = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);
    $expires_at = date("Y-m-d H:i:s", time() + 300); // expires in 5 minutes

    // Store OTP in database
    $stmt = $conn->prepare("INSERT INTO password_reset_otps (user_id, email, otp, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $user_id, $email, $otp, $expires_at);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to save OTP");
    }

    // Send new OTP via email
    $mail = new PHPMailer(true);
    require_once __DIR__ . '/admin/email_config.php';
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = SMTP_PORT;

    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress($email, $user_name);
    $mail->isHTML(true);

    $mail->Subject = 'New Password Reset OTP';
    $mail->Body = "
        <h2>Password Reset Request</h2>
        <p>Dear $user_name,</p>
        <p>Your new OTP for password reset is: <strong>$otp</strong></p>
        <p>This OTP will expire in 5 minutes.</p>
        <p>If you did not request this password reset, please ignore this email.</p>
    ";

    $mail->send();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Failed to send new OTP. Please try again.']);
}
?>
