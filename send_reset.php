<?php
require 'admin/db_connect.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
header('Content-Type: application/json');

if (!isset($_POST['email'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Email required.']);
    exit;
}

$email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$user = $stmt->get_result();
$stmt->close();

if ($user->num_rows == 0) {
    echo json_encode(['status' => 'error', 'msg' => 'No user found with that email.']);
    exit;
}

$user_row = $user->fetch_assoc();

$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));

$stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $user_row['id'], $token, $expires);
$stmt->execute();
$stmt->close();

$mail = new PHPMailer\PHPMailer\PHPMailer(true);
try {
    require_once __DIR__ . '/admin/email_config.php';
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = SMTP_ENCRYPTION;
    $mail->Port = SMTP_PORT;
    $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
    $mail->addAddress($email, $user_row['firstname'] ?? '');
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request';

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $reset_link = $protocol . $_SERVER['HTTP_HOST'] . '/alumni/reset_password.php?token=' . urlencode($token);

    $mail->Body = '<div style="font-family:sans-serif;font-size:1.1em;background:#fffbe6;padding:24px 18px;border-radius:10px;max-width:420px;margin:auto;">'
        . '<div style="text-align:center;margin-bottom:12px;"><img src="' . $protocol . htmlspecialchars($_SERVER['HTTP_HOST']) . '/alumni/assets/img/logo.png" style="height:40px;vertical-align:middle;"></div>'
        . '<div style="text-align:center;font-size:1.1em;margin-bottom:10px;">Hello <b>' . htmlspecialchars($user_row['firstname'] ?? '') . '</b>,</div>'
        . '<div style="text-align:center;margin-bottom:18px;">You requested a password reset. Click the link below to reset your password:</div>'
        . '<div style="text-align:center;margin-bottom:18px;"><a href="' . htmlspecialchars($reset_link) . '" style="background:#800000;color:#fff;padding:10px 22px;border-radius:6px;text-decoration:none;font-weight:bold;">Reset Password</a></div>'
        . '<div style="text-align:center;color:#555;">This link will expire in <b>30 minutes</b> and can only be used once.</div>'
        . '<div style="margin-top:18px;text-align:center;font-size:0.95em;color:#888;">If you did not request this, please ignore this email.</div>'
        . '</div>';

    $mail->send();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    error_log("Password reset email failed: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'msg' => 'Failed to send reset email. Please try again.']);
}
