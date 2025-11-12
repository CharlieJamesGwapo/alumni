<?php
date_default_timezone_set('Asia/Manila');
session_start();
require_once 'admin/db_connect.php';
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Cooldown window (seconds)
$cooldownSeconds = 60; // 1 minute cooldown between sends

// helper: compute remaining cooldown for an email
function get_remaining_cooldown($conn, $email, $cooldownSeconds) {
    $sql = "SELECT created_at FROM otp_verifications WHERE email = ? AND used = 0 ORDER BY created_at DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $created = strtotime($row['created_at']);
        $elapsed = time() - $created;
        if ($elapsed < $cooldownSeconds) {
            return $cooldownSeconds - $elapsed;
        }
    }
    return 0;
}

// If client only wants to check remaining cooldown without sending
if (isset($_GET['check'])) {
    $email_check = $_SESSION['otp_step_email'] ?? null;
    if (!$email_check) {
        echo json_encode(['status' => 'error', 'message' => 'No session email found.', 'cooldown' => 0]);
        exit;
    }
    $remaining = get_remaining_cooldown($conn, $email_check, $cooldownSeconds);
    echo json_encode(['status' => 'ok', 'message' => 'cooldown', 'cooldown' => $remaining]);
    exit;
}

// Only allow sending OTP if credentials were verified
if (!isset($_SESSION['otp_step_verified']) || !$_SESSION['otp_step_verified']) {
    echo json_encode(['status' => 'error', 'message' => 'Please verify your credentials first.', 'cooldown' => 0]);
    exit;
}

$email = $_SESSION['otp_step_email'] ?? null;
if (!$email) {
    echo json_encode(['status' => 'error', 'message' => 'No email found for OTP.', 'cooldown' => 0]);
    exit;
}

// First check if email exists in alumnus_bio table
$stmt = $conn->prepare("SELECT id, firstname, lastname FROM alumnus_bio WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'Email not found. Please check your email or register first.', 'cooldown' => 0]);
    exit;
}

$alumni = $result->fetch_assoc();
// Server-side cooldown: don't send another OTP if an unused one exists within the cooldown window
$cooldownSeconds = 60; // 1 minute cooldown between sends
$recentChk = $conn->prepare("SELECT id, created_at FROM otp_verifications WHERE email = ? AND used = 0 ORDER BY created_at DESC LIMIT 1");
$recentChk->bind_param("s", $email);
$recentChk->execute();
$recentRes = $recentChk->get_result();
if ($recentRes && $recentRes->num_rows > 0) {
    $row = $recentRes->fetch_assoc();
    $created = strtotime($row['created_at']);
    $elapsed = time() - $created;
    if ($elapsed < $cooldownSeconds) {
        $remaining = $cooldownSeconds - $elapsed;
        echo json_encode(['status' => 'error', 'message' => 'An OTP was recently sent. Please check your email or wait before requesting another.', 'cooldown' => $remaining]);
        exit;
    }
}

$otp = sprintf('%06d', mt_rand(100000, 999999));

// Store OTP in database with expiry
$expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

// Invalidate any existing OTPs for this email
// Check for recent OTP attempts
$stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM otp_verifications WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 5 MINUTE)");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$attempts = $result->fetch_assoc()['attempts'];

if ($attempts >= 3) {
    echo json_encode(['status' => 'error', 'message' => 'Too many OTP attempts. Please wait 5 minutes before trying again.', 'cooldown' => 0]);
    exit;
}

// Invalidate existing OTPs
$stmt = $conn->prepare("UPDATE otp_verifications SET used = 1 WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

// Insert new OTP with created_at timestamp
$stmt = $conn->prepare("INSERT INTO otp_verifications (email, otp, expires_at, used, created_at) VALUES (?, ?, ?, 0, NOW())");
$stmt->bind_param("sss", $email, $otp, $expiry);
$stmt->execute();

// For debugging
error_log("New OTP generated for $email: $otp, expires at $expiry", 3, __DIR__ . '/otp_error.log');

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'capstonee2@gmail.com';
    $mail->Password = 'ewus hfar ghit fdkt';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->setFrom('capstonee2@gmail.com', 'MOIST Alumni Portal');
    $mail->addAddress($email, $alumni['firstname'] . ' ' . $alumni['lastname']);

    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $logo_url = $protocol . $_SERVER['HTTP_HOST'] . '/alumni/assets/img/logo.png';

    $mailContent = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9;">
        <div style="text-align: center; padding: 20px; background: #800000; border-radius: 10px;">
            <img src="'.$logo_url.'" alt="MOIST Logo" style="height: 80px;">
            <h1 style="color: #ffd700; margin-top: 10px;">MOIST Alumni Portal</h1>
        </div>
        <div style="background: white; padding: 20px; border-radius: 10px; margin-top: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            <h2 style="color: #800000; text-align: center;">Your Login OTP</h2>
            <p style="font-size: 16px;">Dear '.$alumni['firstname'].' '.$alumni['lastname'].',</p>
            <p>Your one-time password (OTP) for logging into the MOIST Alumni Portal is:</p>
            <div style="text-align: center; padding: 20px; background: #f5f5f5; border-radius: 10px; margin: 20px 0; border: 2px dashed #800000;">
                <h1 style="color: #800000; letter-spacing: 5px; font-size: 32px; margin: 0;">'.$otp.'</h1>
            </div>
            <p style="color: #666; font-size: 14px; font-weight: bold;">This OTP will expire in 5 minutes.</p>
            <p style="color: #666; font-size: 14px;">If you did not request this OTP, please ignore this email.</p>
        </div>
        <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
            <p>This is an automated message, please do not reply.</p>
            <p>&copy; '.date('Y').' MOIST Alumni Portal. All rights reserved.</p>
        </div>
    </div>';

    $mail->isHTML(true);
    $mail->Subject = 'Your MOIST Alumni Login OTP';
    $mail->Body = $mailContent;
    $mail->AltBody = "Your MOIST Alumni Portal OTP is: $otp\nValid for 5 minutes.";

    if (!$mail->send()) {
        error_log("Mail Error: " . $mail->ErrorInfo, 3, __DIR__ . '/otp_error.log');
        $stmt = $conn->prepare("DELETE FROM otp_verifications WHERE email = ? AND otp = ?");
        $stmt->bind_param("ss", $email, $otp);
        $stmt->execute();
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to send OTP email. Please try again.',
            'cooldown' => 0
        ]);
        exit;
    }

    $_SESSION['otp_verified'] = false;
    $_SESSION['verified_email'] = $email;
    $_SESSION['verification_time'] = time();

    error_log("OTP sent successfully to: " . $email, 3, __DIR__ . '/otp_error.log');

    echo json_encode([
        'status' => 'success',
        'message' => 'OTP sent successfully! Please check your email.',
        'cooldown' => $cooldownSeconds
    ]);

} catch (Exception $e) {
    error_log("Mail Error: " . $e->getMessage(), 3, __DIR__ . '/otp_error.log');
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to send OTP. Please try again later.',
        'cooldown' => 0
    ]);
}
?>
