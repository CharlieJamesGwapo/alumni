<?php
/**
 * Email Configuration Test Script
 * Tests SMTP connection and email sending capability
 */

session_start();
require_once '../admin/db_connect.php';

// Only allow Registrar (type = 4)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    die('Unauthorized access');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Configuration Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; padding: 20px; }
        .test-container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .test-result { margin: 15px 0; padding: 15px; border-radius: 5px; }
        .test-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .test-error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; }
        .test-info { background: #d1ecf1; border: 1px solid #bee5eb; color: #0c5460; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="test-container">
        <h2><i class="fas fa-envelope-circle-check me-2"></i>Email Configuration Test</h2>
        <p class="text-muted">Testing SMTP connection and email sending capability...</p>
        <hr>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_email'])) {
            $testEmail = filter_var($_POST['test_email'], FILTER_VALIDATE_EMAIL);
            
            if (!$testEmail) {
                echo '<div class="test-result test-error"><i class="fas fa-times-circle me-2"></i>Invalid email address provided</div>';
            } else {
                echo '<h4>Test Results:</h4>';
                
                // Test 1: PHPMailer Class
                echo '<div class="test-result test-info">';
                echo '<strong><i class="fas fa-check-circle me-2"></i>Test 1: PHPMailer Class</strong><br>';
                if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                    echo '✅ PHPMailer class is available';
                } else {
                    echo '❌ PHPMailer class not found';
                }
                echo '</div>';
                
                // Test 2: SMTP Connection
                echo '<div class="test-result test-info">';
                echo '<strong><i class="fas fa-network-wired me-2"></i>Test 2: SMTP Connection</strong><br>';
                try {
                    $mail = new PHPMailer(true);
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'capstonee2@gmail.com';
                    $mail->Password = 'ewushfarghitfdkt';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->Timeout = 10;
                    
                    // Try to connect
                    if ($mail->smtpConnect()) {
                        echo '✅ SMTP connection successful<br>';
                        echo '<small>Host: smtp.gmail.com:587</small>';
                        $mail->smtpClose();
                    } else {
                        echo '❌ SMTP connection failed';
                    }
                } catch (Exception $e) {
                    echo '❌ SMTP connection error: ' . htmlspecialchars($e->getMessage());
                }
                echo '</div>';
                
                // Test 3: Send Test Email
                echo '<div class="test-result test-info">';
                echo '<strong><i class="fas fa-paper-plane me-2"></i>Test 3: Send Test Email</strong><br>';
                try {
                    $mail = new PHPMailer(true);
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'capstonee2@gmail.com';
                    $mail->Password = 'ewushfarghitfdkt';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;
                    $mail->CharSet = 'UTF-8';
                    $mail->Timeout = 30;
                    
                    $mail->setFrom('capstonee2@gmail.com', 'MOIST Alumni Office');
                    $mail->addAddress($testEmail, 'Test Recipient');
                    
                    $mail->isHTML(true);
                    $mail->Subject = 'Email Configuration Test - ' . date('Y-m-d H:i:s');
                    $mail->Body = '
                        <html>
                        <body style="font-family: Arial, sans-serif; padding: 20px;">
                            <h2 style="color: #1e3a8a;">✅ Email Test Successful!</h2>
                            <p>This is a test email from the MOIST Alumni Management System.</p>
                            <p><strong>Test Time:</strong> ' . date('Y-m-d H:i:s') . '</p>
                            <p><strong>SMTP Server:</strong> smtp.gmail.com:587</p>
                            <p>If you received this email, your email configuration is working correctly.</p>
                            <hr>
                            <p style="color: #666; font-size: 12px;">MOIST Alumni Office</p>
                        </body>
                        </html>
                    ';
                    $mail->AltBody = 'Email Test Successful! Test Time: ' . date('Y-m-d H:i:s');
                    
                    if ($mail->send()) {
                        echo '<div class="test-success">';
                        echo '✅ <strong>Test email sent successfully!</strong><br>';
                        echo 'Check your inbox at: <strong>' . htmlspecialchars($testEmail) . '</strong><br>';
                        echo '<small>Note: Check spam folder if not in inbox</small>';
                        echo '</div>';
                    } else {
                        echo '<div class="test-error">';
                        echo '❌ Failed to send test email';
                        echo '</div>';
                    }
                    
                } catch (Exception $e) {
                    echo '<div class="test-error">';
                    echo '❌ <strong>Email sending failed</strong><br>';
                    echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '<br>';
                    echo '<pre>' . htmlspecialchars($mail->ErrorInfo) . '</pre>';
                    echo '</div>';
                }
                echo '</div>';
                
                // Test 4: Database Connection
                echo '<div class="test-result test-info">';
                echo '<strong><i class="fas fa-database me-2"></i>Test 4: Database Connection</strong><br>';
                if ($conn && $conn->ping()) {
                    echo '✅ Database connection is active';
                } else {
                    echo '❌ Database connection failed';
                }
                echo '</div>';
                
                // Test 5: Check speaker_rsvp table
                echo '<div class="test-result test-info">';
                echo '<strong><i class="fas fa-table me-2"></i>Test 5: Speaker RSVP Table</strong><br>';
                $tableCheck = $conn->query("SHOW TABLES LIKE 'speaker_rsvp'");
                if ($tableCheck && $tableCheck->num_rows > 0) {
                    echo '✅ speaker_rsvp table exists<br>';
                    $countResult = $conn->query("SELECT COUNT(*) as cnt FROM speaker_rsvp");
                    if ($countResult) {
                        $count = $countResult->fetch_assoc();
                        echo '<small>Records: ' . $count['cnt'] . '</small>';
                    }
                } else {
                    echo '⚠️ speaker_rsvp table does not exist (will be created on first invitation)';
                }
                echo '</div>';
            }
        }
        ?>

        <form method="POST" class="mt-4">
            <div class="mb-3">
                <label for="test_email" class="form-label">
                    <i class="fas fa-envelope me-2"></i>Test Email Address
                </label>
                <input type="email" class="form-control" id="test_email" name="test_email" 
                       placeholder="Enter email to receive test" required>
                <small class="text-muted">A test email will be sent to this address</small>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-paper-plane me-2"></i>Run Email Test
            </button>
            <a href="alumni.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Alumni
            </a>
        </form>

        <div class="mt-4 p-3 bg-light rounded">
            <h5><i class="fas fa-info-circle me-2"></i>Troubleshooting Tips</h5>
            <ul>
                <li><strong>Gmail Security:</strong> Ensure "Less secure app access" is enabled or use an App Password</li>
                <li><strong>2-Factor Authentication:</strong> If enabled, you must use an App-specific password</li>
                <li><strong>Firewall:</strong> Ensure port 587 is not blocked</li>
                <li><strong>PHP Extensions:</strong> Ensure OpenSSL extension is enabled</li>
                <li><strong>Error Logs:</strong> Check PHP error logs for detailed error messages</li>
            </ul>
        </div>
    </div>
</body>
</html>
