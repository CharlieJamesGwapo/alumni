<?php
date_default_timezone_set('Asia/Manila');
session_start();
require_once '../admin/db_connect.php';
header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

// Only allow Registrar (type = 4)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

// Simple send all action - no batching needed
if (!isset($_POST['action']) || $_POST['action'] === 'send_all') {
    // Get all validated alumni with emails
    $query = "SELECT email, firstname, lastname FROM alumnus_bio WHERE email IS NOT NULL AND email != '' AND status = 1";
    $result = $conn->query($query);
    
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed']);
        exit();
    }
    
    $alumni_list = [];
    while ($row = $result->fetch_assoc()) {
        if (filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            $alumni_list[] = $row;
        }
    }
    
    if (empty($alumni_list)) {
        echo json_encode(['status' => 'success', 'message' => 'No valid email addresses found', 'sent' => 0, 'failed' => 0]);
        exit();
    }
    
    // Send emails
    $sent = 0;
    $failed = 0;
    $errors = [];
    
    foreach ($alumni_list as $alumni) {
        if (sendHomecomingEmail($alumni)) {
            $sent++;
        } else {
            $failed++;
            $errors[] = $alumni['email'];
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'message' => "Homecoming invitations sent: $sent successful, $failed failed",
        'sent' => $sent,
        'failed' => $failed,
        'total' => count($alumni_list),
        'errors' => $errors
    ]);
    exit();
}

// Send test email
if ($_POST['action'] === 'test') {
    $test_email = filter_var($_POST['test_email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$test_email) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid test email address']);
        exit();
    }
    
    $test_alumni = [
        'email' => $test_email,
        'firstname' => 'Test',
        'lastname' => 'User'
    ];
    
    if (sendHomecomingEmail($test_alumni)) {
        echo json_encode(['status' => 'success', 'message' => 'Test email sent successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send test email']);
    }
    exit();
}

// Add new action for getting courses
if ($_POST['action'] === 'get_courses') {
    $query = "SELECT DISTINCT c.id, c.course, COUNT(ab.id) as alumni_count 
              FROM courses c 
              LEFT JOIN alumnus_bio ab ON c.id = ab.course_id 
              WHERE ab.status = 1 AND ab.email IS NOT NULL AND ab.email != ''
              GROUP BY c.id, c.course 
              HAVING alumni_count > 0
              ORDER BY c.course";
    
    $result = $conn->query($query);
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'courses' => $courses]);
    exit();
}

// Enhanced action for getting recipients by course with proper limits
if ($_POST['action'] === 'get_recipients') {
    $courses = isset($_POST['courses']) ? json_decode($_POST['courses'], true) : [];
    $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 10;
    $minLimit = isset($_POST['min_limit']) ? intval($_POST['min_limit']) : 5;
    
    if (empty($courses)) {
        echo json_encode(['status' => 'error', 'message' => 'No courses selected']);
        exit();
    }
    
    $course_ids = array_map('intval', $courses);
    $recipients = [];
    
    // Get recipients for each course separately to respect limits
    foreach ($course_ids as $course_id) {
        $query = "SELECT ab.id, ab.firstname, ab.lastname, ab.email, c.course 
                  FROM alumnus_bio ab 
                  JOIN courses c ON ab.course_id = c.id 
                  WHERE ab.course_id = ? 
                  AND ab.status = 1 AND ab.email IS NOT NULL AND ab.email != ''
                  ORDER BY RAND()";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $course_recipients = [];
        while ($row = $result->fetch_assoc()) {
            $course_recipients[] = $row;
        }
        
        // Apply limits: if less than minLimit, take all; if more than limit, take random selection
        $count = count($course_recipients);
        if ($count > 0) {
            if ($count <= $minLimit) {
                // Take all if below minimum
                $recipients = array_merge($recipients, $course_recipients);
            } else {
                // Take up to the limit
                $take = min($count, $limit);
                $selected = array_slice($course_recipients, 0, $take);
                $recipients = array_merge($recipients, $selected);
            }
        }
        
        $stmt->close();
    }
    
    echo json_encode(['status' => 'success', 'recipients' => $recipients, 'total' => count($recipients)]);
    exit();
}

// Add new action for sending invites to selected recipients
if ($_POST['action'] === 'send_selected') {
    $recipients = isset($_POST['recipients']) ? json_decode($_POST['recipients'], true) : [];
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    
    if (empty($recipients)) {
        echo json_encode(['status' => 'error', 'message' => 'No recipients selected']);
        exit();
    }
    
    $sent = 0;
    $failed = 0;
    
    foreach ($recipients as $recipient) {
        if (sendCustomHomecomingEmail($recipient, $subject, $content)) {
            $sent++;
        } else {
            $failed++;
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'sent' => $sent,
        'failed' => $failed,
        'total' => count($recipients)
    ]);
    exit();
}

// Function to send homecoming invitation email
function sendHomecomingEmail($alumni) {
    try {
        $mail = new PHPMailer(true);
        
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'capstonee2@gmail.com';
        $mail->Password = 'ewushfarghitfdkt';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        // Email settings
        $mail->setFrom('capstonee2@gmail.com', 'MOIST Alumni Office');
        $mail->addAddress($alumni['email'], $alumni['firstname'] . ' ' . $alumni['lastname']);
        
        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'MOIST Alumni Homecoming 2025 - You\'re Invited!';
        
        $fullName = trim($alumni['firstname'] . ' ' . $alumni['lastname']);
        
        $mail->Body = '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; background: #f9f9f9; padding: 20px;">
            <div style="background: linear-gradient(135deg, #800000 0%, #600000 100%); color: white; padding: 30px; text-align: center; border-radius: 10px;">
                <h1 style="margin: 0; color: #ffd700; font-size: 28px;">🎓 MOIST Alumni Homecoming 2025</h1>
                <p style="margin: 10px 0 0 0; font-size: 16px;">Misamis Oriental Institute of Science and Technology</p>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 10px; margin-top: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <h2 style="color: #800000; margin-top: 0;">Dear ' . htmlspecialchars($fullName) . ',</h2>
                
                <p style="font-size: 16px; line-height: 1.6; color: #333;">
                    We are excited to invite you to the <strong>MOIST Alumni Homecoming 2025</strong>! 
                    Join us for a memorable reunion with your fellow alumni, faculty, and friends.
                </p>
                
                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 20px 0;">
                    <h3 style="color: #856404; margin-top: 0;">📅 Event Details</h3>
                    <p style="margin: 5px 0; color: #856404;"><strong>Date:</strong> To be announced</p>
                    <p style="margin: 5px 0; color: #856404;"><strong>Time:</strong> To be announced</p>
                    <p style="margin: 5px 0; color: #856404;"><strong>Venue:</strong> MOIST Campus</p>
                </div>
                
                <h3 style="color: #800000;">What to Expect:</h3>
                <ul style="font-size: 16px; line-height: 1.6; color: #333;">
                    <li>🤝 Reconnect with classmates and faculty</li>
                    <li>🍽️ Delicious food and refreshments</li>
                    <li>🎵 Entertainment and activities</li>
                    <li>📸 Photo opportunities and memories</li>
                    <li>🏆 Recognition of outstanding alumni</li>
                </ul>
                
                <p style="font-size: 16px; line-height: 1.6; color: #333;">
                    More details including the exact date, time, and registration information will be sent soon. 
                    Mark your calendars and start planning to join us for this special celebration!
                </p>
                
                <div style="text-align: center; margin: 30px 0;">
                    <div style="background: linear-gradient(135deg, #800000 0%, #600000 100%); color: white; padding: 15px; border-radius: 8px; display: inline-block;">
                        <strong>Save the Date - More Details Coming Soon!</strong>
                    </div>
                </div>
                
                <p style="font-size: 16px; line-height: 1.6; color: #333;">
                    We look forward to seeing you at the homecoming celebration!
                </p>
                
                <p style="margin-top: 30px; color: #666;">
                    Warm regards,<br>
                    <strong style="color: #800000;">MOIST Alumni Office</strong><br>
                    Misamis Oriental Institute of Science and Technology
                </p>
            </div>
            
            <div style="text-align: center; margin-top: 20px; color: #666; font-size: 12px;">
                <p>This is an automated message from MOIST Alumni Office.</p>
                <p>&copy; ' . date('Y') . ' MOIST Alumni Portal. All rights reserved.</p>
            </div>
        </div>';
        
        $mail->AltBody = "Dear $fullName,\n\nWe are excited to invite you to the MOIST Alumni Homecoming 2025!\n\nMore details will be announced soon. Mark your calendars for this special reunion with fellow alumni, faculty, and friends.\n\nWarm regards,\nMOIST Alumni Office";
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("Homecoming email failed for " . $alumni['email'] . ": " . $e->getMessage());
        return false;
    }
}

function sendCustomHomecomingEmail($recipient, $subject, $content) {
    try {
        $mail = new PHPMailer(true);
        
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'capstonee2@gmail.com';
        $mail->Password = 'ewushfarghitfdkt';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        $mail->setFrom('capstonee2@gmail.com', 'MOIST Alumni Office');
        $mail->addAddress($recipient['email'], $recipient['firstname'] . ' ' . $recipient['lastname']);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        
        // Generate unique RSVP token
        $alumniId = isset($recipient['id']) ? $recipient['id'] : $recipient['alumni_id'];
        $rsvpToken = generateRSVPToken($alumniId);
        
        // Replace placeholders in content
        $personalizedContent = str_replace(
            ['{{name}}', '{{firstname}}', '{{lastname}}', '{{course}}'],
            [
                $recipient['firstname'] . ' ' . $recipient['lastname'],
                $recipient['firstname'],
                $recipient['lastname'],
                $recipient['course']
            ],
            $content
        );
        
        // Create beautiful HTML email with RSVP buttons
        $baseUrl = getBaseUrl();
        $acceptUrl = $baseUrl . "/rsvp_response.php?token=" . $rsvpToken . "&response=accept";
        $declineUrl = $baseUrl . "/rsvp_response.php?token=" . $rsvpToken . "&response=decline";
        
        $mail->Body = createBeautifulEmailTemplate($personalizedContent, $acceptUrl, $declineUrl, $recipient);
        $mail->AltBody = strip_tags($personalizedContent) . "\n\nTo RSVP, visit: " . $baseUrl . "/rsvp_response.php?token=" . $rsvpToken;
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("Custom homecoming email failed for " . $recipient['email'] . ": " . $e->getMessage());
        return false;
    }
}

function generateRSVPToken($alumniId) {
    global $conn;
    
    // Generate unique token
    $token = bin2hex(random_bytes(32));
    
    // Store in database
    $stmt = $conn->prepare("INSERT INTO homecoming_rsvp (alumni_id, token, created_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE token = ?, updated_at = NOW()");
    $stmt->bind_param("iss", $alumniId, $token, $token);
    $stmt->execute();
    $stmt->close();
    
    return $token;
}

function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI']);
    return $protocol . '://' . $host . $path;
}

function createBeautifulEmailTemplate($content, $acceptUrl, $declineUrl, $recipient) {
    $fullName = htmlspecialchars($recipient['firstname'] . ' ' . $recipient['lastname']);
    $course = htmlspecialchars($recipient['course']);
    
    return '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>MOIST Alumni Homecoming 2026</title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
            .header { background: linear-gradient(135deg, #800000 0%, #600000 100%); color: white; padding: 40px 30px; text-align: center; }
            .logo { width: 80px; height: 80px; background: white; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; }
            .logo img { width: 60px; height: 60px; object-fit: contain; }
            .header h1 { font-size: 28px; margin-bottom: 10px; color: #ffd700; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }
            .header p { font-size: 16px; opacity: 0.9; }
            .content { padding: 40px 30px; }
            .greeting { font-size: 18px; color: #800000; margin-bottom: 20px; font-weight: 600; }
            .message { font-size: 16px; line-height: 1.8; margin-bottom: 30px; color: #444; }
            .event-details { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border-left: 5px solid #ffc107; padding: 25px; margin: 30px 0; border-radius: 8px; }
            .event-details h3 { color: #856404; margin-bottom: 15px; font-size: 20px; }
            .detail-item { margin-bottom: 10px; display: flex; align-items: center; }
            .detail-item strong { color: #856404; min-width: 80px; }
            .highlights { background: #f8f9fa; border-radius: 10px; padding: 25px; margin: 30px 0; }
            .highlights h3 { color: #800000; margin-bottom: 15px; font-size: 18px; }
            .highlights ul { list-style: none; }
            .highlights li { margin-bottom: 8px; padding-left: 25px; position: relative; }
            .highlights li:before { content: "✨"; position: absolute; left: 0; }
            .rsvp-section { background: linear-gradient(135deg, #e8f5e8 0%, #d4edda 100%); border-radius: 15px; padding: 30px; text-align: center; margin: 30px 0; }
            .rsvp-section h3 { color: #155724; margin-bottom: 15px; font-size: 22px; }
            .rsvp-section p { color: #155724; margin-bottom: 25px; font-size: 16px; }
            .rsvp-buttons { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }
            .btn { display: inline-block; padding: 15px 30px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; text-align: center; min-width: 140px; }
            .btn-accept { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3); }
            .btn-accept:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4); }
            .btn-decline { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3); }
            .btn-decline:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4); }
            .footer { background: #f8f9fa; padding: 30px; text-align: center; border-top: 3px solid #800000; }
            .footer p { color: #666; font-size: 14px; margin-bottom: 10px; }
            .contact-info { background: white; border-radius: 10px; padding: 20px; margin: 20px 0; }
            .contact-info h4 { color: #800000; margin-bottom: 10px; }
            @media (max-width: 600px) {
                .container { margin: 0; }
                .header, .content { padding: 20px; }
                .rsvp-buttons { flex-direction: column; align-items: center; }
                .btn { width: 100%; max-width: 250px; }
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="logo">
                    <span style="color: #800000; font-weight: bold; font-size: 24px;">M</span>
                </div>
                <h1>🎓 Alumni Homecoming 2026</h1>
                <p>Misamis Oriental Institute of Science and Technology</p>
            </div>
            
            <div class="content">
                <div class="greeting">Dear ' . $fullName . ',</div>
                
                <div class="message">
                    ' . nl2br(htmlspecialchars($content)) . '
                </div>
                
                <div class="event-details">
                    <h3>📅 Event Information</h3>
                    <div class="detail-item">
                        <strong>Date:</strong> February 14-15, 2026
                    </div>
                    <div class="detail-item">
                        <strong>Time:</strong> 6:00 PM - 11:00 PM
                    </div>
                    <div class="detail-item">
                        <strong>Venue:</strong> MOIST Grand Auditorium & Grounds
                    </div>
                    <div class="detail-item">
                        <strong>Theme:</strong> "Reconnect, Reminisce, Rejoice"
                    </div>
                    <div class="detail-item">
                        <strong>Your Course:</strong> ' . $course . '
                    </div>
                </div>
                
                <div class="highlights">
                    <h3>✨ What Awaits You</h3>
                    <ul>
                        <li>Welcome reception and networking session</li>
                        <li>Alumni recognition and awards ceremony</li>
                        <li>Cultural presentations and entertainment</li>
                        <li>Delicious dinner and refreshments</li>
                        <li>Photo opportunities and memory lane exhibit</li>
                        <li>Special surprises and exclusive giveaways</li>
                        <li>Reconnect with classmates and professors</li>
                    </ul>
                </div>
                
                <div class="rsvp-section">
                    <h3>🎉 Will You Join Us?</h3>
                    <p>Please let us know if you can attend this special celebration. Your response helps us prepare better for everyone!</p>
                    
                    <div class="rsvp-buttons">
                        <a href="' . $acceptUrl . '" class="btn btn-accept">
                            ✅ Yes, I\'ll Attend!
                        </a>
                        <a href="' . $declineUrl . '" class="btn btn-decline">
                            ❌ Can\'t Make It
                        </a>
                    </div>
                </div>
                
                <div class="contact-info">
                    <h4>📞 Need Help or Have Questions?</h4>
                    <p><strong>MOIST Alumni Office</strong></p>
                    <p>📧 Email: alumni@moist.edu.ph</p>
                    <p>📱 Phone: (088) 123-4567</p>
                    <p>🌐 Website: www.moist.edu.ph</p>
                </div>
            </div>
            
            <div class="footer">
                <p><strong>MOIST Alumni Office</strong></p>
                <p>Homecoming 2026 Organizing Committee</p>
                <p>Misamis Oriental Institute of Science and Technology</p>
                <p style="margin-top: 20px; font-size: 12px; color: #999;">
                    This invitation was sent to ' . htmlspecialchars($recipient['email']) . '
                </p>
            </div>
        </div>
    </body>
    </html>';
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action specified']);
?>
