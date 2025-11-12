<?php
// send_reset.php: Sends password reset link via PHPMailer
require 'admin/db_connect.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';
header('Content-Type: application/json');

if (!isset($_POST['email'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Email required.']);
    exit;
}
$email = $_POST['email'];
$user = $conn->query("SELECT * FROM users WHERE email = '".$conn->real_escape_string($email)."'");
if ($user->num_rows == 0) {
    echo json_encode(['status' => 'error', 'msg' => 'No user found with that email.']);
    exit;
}
$user_row = $user->fetch_assoc();
// Generate a reset token
$token = bin2hex(random_bytes(32));
$expires = date('Y-m-d H:i:s', strtotime('+30 minutes'));
// Store token in DB (create table if needed)
$conn->query("CREATE TABLE IF NOT EXISTS password_resets (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, token VARCHAR(64), expires_at DATETIME, used TINYINT DEFAULT 0)");
$conn->query("INSERT INTO password_resets (user_id, token, expires_at) VALUES ('{$user_row['id']}', '{$token}', '{$expires}')");
// Send email
$mail = new PHPMailer\PHPMailer\PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'capstonee2@gmail.com'; // CHANGE THIS
    $mail->Password = 'ewus hfar ghit fdkt'; // CHANGE THIS
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom('capstonee2@gmail.com', 'MOIST Alumni');
    $mail->addAddress($email, $user_row['firstname']);
    $mail->isHTML(true);
    $mail->Subject = 'Password Reset Request';
    $reset_link = 'http://' . $_SERVER['HTTP_HOST'] . '/alumni/reset_password.php?token=' . $token;
    $mail->Body = '<div style="font-family:sans-serif;font-size:1.1em;background:#fffbe6;padding:24px 18px;border-radius:10px;max-width:420px;margin:auto;">'
        .'<div style="text-align:center;margin-bottom:12px;"><img src="https://'.$_SERVER['HTTP_HOST'].'/alumni/assets/img/logo.png" style="height:40px;vertical-align:middle;"></div>'
        .'<div style="text-align:center;font-size:1.1em;margin-bottom:10px;">Hello <b>'.htmlspecialchars($user_row['firstname']).'</b>,</div>'
        .'<div style="text-align:center;margin-bottom:18px;">You requested a password reset. Click the link below to reset your password:</div>'
        .'<div style="text-align:center;margin-bottom:18px;"><a href="'.$reset_link.'" style="background:#800000;color:#fff;padding:10px 22px;border-radius:6px;text-decoration:none;font-weight:bold;">Reset Password</a></div>'
        .'<div style="text-align:center;color:#555;">This link will expire in <b>30 minutes</b> and can only be used once.</div>'
        .'<div style="margin-top:18px;text-align:center;font-size:0.95em;color:#888;">If you did not request this, please ignore this email.</div>'
        .'</div>';
    $mail->send();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'msg' => $mail->ErrorInfo . ' | Exception: ' . $e->getMessage()]);
}
