<?php
session_start();
include '../admin/db_connect.php';
include '../admin/log_activity.php';
require_once '../PHPMailer/src/PHPMailer.php';
require_once '../PHPMailer/src/SMTP.php';
require_once '../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Only allow access to Registrar (user_type = 4)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if (isset($_POST['id']) && isset($_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'] === 'true' ? 1 : 0;
    
    // Get alumni information for email notification
    $alumni_query = $conn->prepare("SELECT firstname, lastname, email, alumni_id FROM alumnus_bio WHERE id = ?");
    $alumni_query->bind_param("i", $id);
    $alumni_query->execute();
    $alumni_result = $alumni_query->get_result();
    
    if ($alumni_result->num_rows === 0) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Alumni not found']);
        exit();
    }
    
    $alumni = $alumni_result->fetch_assoc();
    $alumni_query->close();

    // Update ONLY the registration status (status field) - NOT employment_status or id_release_count
    $stmt = $conn->prepare("UPDATE alumnus_bio SET status = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $id);

    if ($stmt->execute()) {
        // Log the activity
        $registrar_name = 'Registrar';
        if (isset($_SESSION['firstname']) && isset($_SESSION['lastname'])) {
            $registrar_name = $_SESSION['firstname'] . ' ' . $_SESSION['lastname'];
        } elseif (isset($_SESSION['username'])) {
            $registrar_name = $_SESSION['username'];
        }
        
        $action = $status ? 'validated' : 'invalidated';
        log_activity($_SESSION['user_id'], "Alumni {$action}", "Alumni {$alumni['firstname']} {$alumni['lastname']} (ID: {$alumni['alumni_id']}) was {$action} by registrar {$registrar_name}");
        
        // Send email notification if validating (status = 1) and email exists
        $email_sent = false;
        if ($status == 1 && !empty($alumni['email'])) {
            $email_sent = sendValidationEmail($alumni);
        }
        
        $message = $status ? 'Alumni validated successfully' : 'Alumni status updated successfully';
        if ($status == 1 && $email_sent) {
            $message .= ' and verification email sent';
        } elseif ($status == 1 && !$email_sent) {
            $message .= ' but email notification failed';
        }
        
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success', 
            'message' => $message,
            'email_sent' => $email_sent
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Database update failed']);
    }

    $stmt->close();
    $conn->close();
    exit();
}

header('Content-Type: application/json');
echo json_encode(['status' => 'error', 'message' => 'Invalid request parameters']);
exit();

function sendValidationEmail($alumni) {
    try {
        $mail = new PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Set the SMTP server to send through
        $mail->SMTPAuth   = true;
        $mail->Username   = 'capstonee2@gmail.com'; // SMTP username (change this)
        $mail->Password   = 'ewushfarghitfdkt';    // SMTP password (change this)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('noreply@moist.edu.ph', 'MOIST Alumni System');
        $mail->addAddress($alumni['email'], $alumni['firstname'] . ' ' . $alumni['lastname']);
        $mail->addReplyTo('registrar@moist.edu.ph', 'MOIST Registrar Office');
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Alumni Verification Confirmed - MOIST Alumni System';
        
        $mail->Body = "
        <html>
        <head>
            <title>Alumni Verification Confirmed</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background: linear-gradient(135deg, #800000 0%, #600000 100%); color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; background: #f9f9f9; }
                .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
                .highlight { background: #fff3cd; padding: 10px; border-left: 4px solid #ffc107; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>🎓 MOIST Alumni System</h1>
                <p>Misamis Oriental Institute of Science and Technology</p>
            </div>
            
            <div class='content'>
                <h2>Dear {$alumni['firstname']} {$alumni['lastname']},</h2>
                
                <p>Congratulations! Your alumni registration has been <strong>successfully verified</strong> by our registrar office.</p>
                
                <div class='highlight'>
                    <h3>✅ Verification Complete</h3>
                    <p><strong>Alumni ID:</strong> {$alumni['alumni_id']}</p>
                    <p><strong>Status:</strong> Verified Alumni</p>
                    <p><strong>Verified Date:</strong> " . date('F j, Y g:i A') . "</p>
                </div>
                
                <p>As a verified alumni, you now have access to:</p>
                <ul>
                    <li>🆔 Official Alumni ID Card generation</li>
                    <li>📧 Alumni newsletter and updates</li>
                    <li>🎉 Invitations to alumni events and reunions</li>
                    <li>🤝 Alumni networking opportunities</li>
                    <li>📋 Job posting and career services</li>
                </ul>
                
                <p>You can access your alumni portal anytime to update your information, connect with batchmates, and stay updated with MOIST news.</p>
                
                <p>Thank you for being part of the MOIST Alumni community!</p>
                
                <p>Best regards,<br>
                <strong>MOIST Registrar Office</strong><br>
                Alumni Management System</p>
            </div>
            
            <div class='footer'>
                <p>Misamis Oriental Institute of Science and Technology<br>
                Sta. Cruz, Cogon, Balingasag, Misamis Oriental<br>
                This is an automated message. Please do not reply to this email.</p>
            </div>
        </body>
        </html>";
        
        // Alternative plain text version
        $mail->AltBody = "Dear {$alumni['firstname']} {$alumni['lastname']},\n\n" .
                        "Congratulations! Your alumni registration has been successfully verified by our registrar office.\n\n" .
                        "Alumni ID: {$alumni['alumni_id']}\n" .
                        "Status: Verified Alumni\n" .
                        "Verified Date: " . date('F j, Y g:i A') . "\n\n" .
                        "As a verified alumni, you now have access to official Alumni ID Card generation, alumni newsletter, event invitations, networking opportunities, and job posting services.\n\n" .
                        "Thank you for being part of the MOIST Alumni community!\n\n" .
                        "Best regards,\nMOIST Registrar Office\nAlumni Management System";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
?>
