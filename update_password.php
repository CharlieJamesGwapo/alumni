<?php
session_start();
require_once 'admin/db_connect.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

function log_error($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, __DIR__ . '/logs/password_reset.log');
}

try {
    if (!isset($_SESSION['reset_verified']) || !isset($_SESSION['reset_alumni_id'])) {
        throw new Exception('Invalid reset session');
    }

    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $alumni_id = $_SESSION['reset_alumni_id'];

    // Validate passwords
    if (empty($password) || empty($confirm_password)) {
        throw new Exception('Both passwords are required');
    }

    if ($password !== $confirm_password) {
        throw new Exception('Passwords do not match');
    }

    if (strlen($password) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }

    // Get user details from alumni_ids
    $stmt = $conn->prepare("SELECT * FROM alumni_ids WHERE alumni_id = ?");
    $stmt->bind_param("s", $alumni_id);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user) {
        throw new Exception('User not found');
    }

    // Hash new password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Update password in alumni_ids table
        $update_alumni = $conn->prepare("UPDATE alumni_ids SET password = ? WHERE alumni_id = ?");
        $update_alumni->bind_param("ss", $hashed_password, $alumni_id);
        
        if (!$update_alumni->execute()) {
            throw new Exception('Failed to update password in alumni_ids');
        }

        // Update password in alumnus_bio table
        $update_bio = $conn->prepare("UPDATE alumnus_bio SET password = ? WHERE alumni_id = ?");
        $update_bio->bind_param("ss", $hashed_password, $alumni_id);
        
        if (!$update_bio->execute()) {
            throw new Exception('Failed to update password in alumnus_bio');
        }

        // Send confirmation notification
        $isEmail = filter_var($user['email'], FILTER_VALIDATE_EMAIL);
        
        if ($isEmail) {
            // Send email confirmation
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'capstonee2@gmail.com';
            $mail->Password = 'ewus hfar ghit fdkt';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('capstonee2@gmail.com', 'MOIST Alumni Portal');
            $mail->addAddress($user['email']);
            $mail->isHTML(true);
            $mail->Subject = 'Password Changed Successfully';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px;'>
                    <h2>Password Changed Successfully</h2>
                    <p>Hello {$user['firstname']},</p>
                    <p>Your password has been successfully updated.</p>
                    <p>If you did not make this change, please contact support immediately.</p>
                    <p>Time: " . date('Y-m-d H:i:s') . "</p>
                </div>
            ";

            $mail->send();
        } else {
            // Send SMS confirmation
            $phone = preg_replace('/[^0-9]/', '', $user['contact_no']);
            $message = urlencode("MOIST Alumni: Your password has been changed successfully. Time: " . date('Y-m-d H:i:s'));
            
            $textblaster_base = 'http://192.168.8.34:8080/send';
            $url = "{$textblaster_base}?phoneNumber={$phone}&message={$message}";
            
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET'
            ));

            curl_exec($ch);
            curl_close($ch);
        }

        // Commit transaction
        $conn->commit();

        // Clear reset sessions
        unset($_SESSION['reset_verified']);
        unset($_SESSION['reset_alumni_id']);
        unset($_SESSION['reset_expires']);
        unset($_SESSION['reset_contact']);

        echo json_encode([
            'status' => 'success',
            'message' => 'Password updated successfully'
        ]);

    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }

} catch (Exception $e) {
    log_error("Password Update Error: {$e->getMessage()}");
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
