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
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, __DIR__ . '/logs/reset_password.log');
}

try {
    $contact = trim($_POST['contact'] ?? '');
    
    if (empty($contact)) {
        throw new Exception('Please enter email or phone number.');
    }

    // Check if email or phone
    $isEmail = filter_var($contact, FILTER_VALIDATE_EMAIL);
    $sql = "SELECT * FROM alumni_ids WHERE ";
    $sql .= $isEmail ? "email = ?" : "contact_no = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $contact);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        throw new Exception('No account found with this ' . ($isEmail ? 'email' : 'phone number'));
    }

    $user = $result->fetch_assoc();

    // Generate OTP
    $otp = sprintf("%06d", mt_rand(100000, 999999));
    
    // Store OTP in database
    $stmt = $conn->prepare("INSERT INTO password_resets (alumni_id, contact, otp, expires_at) VALUES (?, ?, ?, DATE_ADD(NOW(), INTERVAL 5 MINUTE))");
    $stmt->bind_param("sss", $user['alumni_id'], $contact, $otp);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to generate OTP. Please try again.');
    }

    // Send OTP
    if ($isEmail) {
        // Send via PHPMailer
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'capstonee2@gmail.com';
            $mail->Password = 'ewus hfar ghit fdkt';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('capstonee2@gmail.com', 'MOIST Alumni Portal');
            $mail->addAddress($contact);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset OTP';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; padding: 20px; max-width: 600px; margin: 0 auto;'>
                    <h2 style='color: #800000;'>Password Reset Request</h2>
                    <p>Hello {$user['firstname']},</p>
                    <p>Your OTP for password reset is: <strong style='font-size: 24px; color: #800000;'>{$otp}</strong></p>
                    <p>This code will expire in 5 minutes.</p>
                    <p>If you didn't request this reset, please ignore this email.</p>
                </div>
            ";

            $mail->send();
        } catch (Exception $e) {
            throw new Exception('Failed to send OTP email: ' . $mail->ErrorInfo);
        }
    } else {
        // Send via TextBlaster
        $message = urlencode("Your MOIST Alumni password reset OTP is: {$otp}. Valid for 5 minutes.");
        $phone = preg_replace('/[^0-9]/', '', $contact);
        
        // Fix TextBlaster URL format
        $textblaster_base = 'http://192.168.100.190:8080/send';
        $url = "{$textblaster_base}?phoneNumber={$phone}&message={$message}";
        
        // Log SMS attempt
        log_error("Attempting SMS to: {$phone} with URL: {$url}");
        
        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ));

        $response = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        // Log response
        log_error("SMS Response: " . ($err ?: $response) . " HTTP Code: {$httpcode}");

        if ($err || $httpcode >= 400) {
            throw new Exception('Failed to send OTP via SMS. Error: ' . ($err ?: "HTTP {$httpcode}"));
        }
    }

    // Store in session for verification
    $_SESSION['reset_contact'] = $contact;
    $_SESSION['reset_alumni_id'] = $user['alumni_id'];

    echo json_encode([
        'status' => 'success',
        'message' => 'OTP has been sent successfully to your ' . ($isEmail ? 'email' : 'phone')
    ]);

} catch (Exception $e) {
    log_error($e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
