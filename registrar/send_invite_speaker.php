<?php
// Prevent any output before JSON
ob_start();
date_default_timezone_set('Asia/Manila');
session_start();
require_once '../admin/db_connect.php';

// Clear any previous output
ob_end_clean();

// Set JSON header
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Error handling - convert PHP errors to JSON
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo json_encode([
        'status' => 'error',
        'message' => 'PHP Error: ' . $errstr,
        'file' => basename($errfile),
        'line' => $errline
    ]);
    exit();
});

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

// ==================== FUNCTION DEFINITIONS (Must be before usage) ====================

// Function to send speaker invitation email
function sendSpeakerInvitation($recipient, $subject, $content, $event_date, $event_time, $event_venue, $event_topic) {
    global $conn;
    
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
        $mail->Timeout = 30;
        $mail->SMTPKeepAlive = false;
        
        $mail->setFrom('capstonee2@gmail.com', 'MOIST Alumni Office');
        $mail->addAddress($recipient['email'], $recipient['firstname'] . ' ' . $recipient['lastname']);
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        
        // Generate unique RSVP token with event details
        $alumniId = isset($recipient['id']) ? $recipient['id'] : 0;
        $rsvpToken = generateSpeakerRSVPToken($alumniId, $event_date, $event_time, $event_venue, $event_topic);
        
        // Replace placeholders in content
        $personalizedContent = str_replace(
            ['{{name}}', '{{firstname}}', '{{lastname}}', '{{course}}', '{{event_date}}', '{{event_time}}', '{{event_venue}}', '{{event_topic}}'],
            [
                $recipient['firstname'] . ' ' . $recipient['lastname'],
                $recipient['firstname'],
                $recipient['lastname'],
                $recipient['course'] ?? 'N/A',
                $event_date,
                $event_time,
                $event_venue,
                $event_topic
            ],
            $content
        );
        
        // Create beautiful HTML email with RSVP buttons
        $baseUrl = getBaseUrl();
        $acceptUrl = $baseUrl . "/speaker_rsvp_response.php?token=" . $rsvpToken . "&response=accept";
        $declineUrl = $baseUrl . "/speaker_rsvp_response.php?token=" . $rsvpToken . "&response=decline";
        
        $mail->Body = createSpeakerEmailTemplate($personalizedContent, $acceptUrl, $declineUrl, $recipient, $event_date, $event_time, $event_venue, $event_topic);
        $mail->AltBody = strip_tags($personalizedContent) . "\n\nTo RSVP, visit: " . $baseUrl . "/speaker_rsvp_response.php?token=" . $rsvpToken;
        
        $result = $mail->send();
        
        if ($result) {
            error_log("SUCCESS: Speaker invitation sent to " . $recipient['email']);
        }
        
        return $result;
        
    } catch (Exception $e) {
        $errorMsg = "Speaker invitation email failed for " . ($recipient['email'] ?? 'unknown') . ": " . $e->getMessage();
        error_log($errorMsg);
        return false;
    }
}

function generateSpeakerRSVPToken($alumniId, $event_date = '', $event_time = '', $event_venue = '', $event_topic = '') {
    global $conn;
    
    $token = bin2hex(random_bytes(32));
    
    $tableCheck = $conn->query("SHOW TABLES LIKE 'speaker_rsvp'");
    if ($tableCheck->num_rows == 0) {
        $conn->query("CREATE TABLE speaker_rsvp (
            id INT AUTO_INCREMENT PRIMARY KEY,
            alumni_id INT NOT NULL,
            token VARCHAR(64) UNIQUE NOT NULL,
            response ENUM('pending', 'accept', 'decline') DEFAULT 'pending',
            event_date VARCHAR(100) DEFAULT NULL,
            event_time VARCHAR(100) DEFAULT NULL,
            event_venue VARCHAR(255) DEFAULT NULL,
            event_topic VARCHAR(255) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_alumni (alumni_id)
        )");
    }
    
    $stmt = $conn->prepare("INSERT INTO speaker_rsvp (alumni_id, token, event_date, event_time, event_venue, event_topic, created_at) 
                           VALUES (?, ?, ?, ?, ?, ?, NOW()) 
                           ON DUPLICATE KEY UPDATE token = ?, event_date = ?, event_time = ?, event_venue = ?, event_topic = ?, updated_at = NOW()");
    $stmt->bind_param("issssssssss", $alumniId, $token, $event_date, $event_time, $event_venue, $event_topic, 
                      $token, $event_date, $event_time, $event_venue, $event_topic);
    $stmt->execute();
    $stmt->close();
    
    return $token;
}

function logSpeakerInvitation($alumniId, $event_date, $event_time, $event_venue, $event_topic) {
    global $conn;
    
    $tableCheck = $conn->query("SHOW TABLES LIKE 'speaker_invitations'");
    if ($tableCheck->num_rows == 0) {
        $conn->query("CREATE TABLE speaker_invitations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            alumni_id INT NOT NULL,
            event_date VARCHAR(100),
            event_time VARCHAR(100),
            event_venue VARCHAR(255),
            event_topic VARCHAR(255),
            invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_alumni (alumni_id)
        )");
    }
    
    $stmt = $conn->prepare("INSERT INTO speaker_invitations (alumni_id, event_date, event_time, event_venue, event_topic) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $alumniId, $event_date, $event_time, $event_venue, $event_topic);
    $stmt->execute();
    $stmt->close();
}

function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['REQUEST_URI']);
    return $protocol . '://' . $host . $path;
}

function createSpeakerEmailTemplate($content, $acceptUrl, $declineUrl, $recipient, $event_date, $event_time, $event_venue, $event_topic) {
    $fullName = htmlspecialchars($recipient['firstname'] . ' ' . $recipient['lastname']);
    $course = htmlspecialchars($recipient['course'] ?? 'N/A');
    
    return '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>MOIST Guest Speaker Invitation</title><style>* { margin: 0; padding: 0; box-sizing: border-box; }body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; }.container { max-width: 600px; margin: 0 auto; background: #ffffff; }.header { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 40px 30px; text-align: center; }.logo { width: 80px; height: 80px; background: white; border-radius: 50%; margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; }.header h1 { font-size: 28px; margin-bottom: 10px; color: #fbbf24; text-shadow: 2px 2px 4px rgba(0,0,0,0.3); }.header p { font-size: 16px; opacity: 0.9; }.content { padding: 40px 30px; }.greeting { font-size: 18px; color: #1e3a8a; margin-bottom: 20px; font-weight: 600; }.message { font-size: 16px; line-height: 1.8; margin-bottom: 30px; color: #444; }.event-details { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-left: 5px solid #3b82f6; padding: 25px; margin: 30px 0; border-radius: 8px; }.event-details h3 { color: #1e3a8a; margin-bottom: 15px; font-size: 20px; }.detail-item { margin-bottom: 10px; display: flex; align-items: flex-start; }.detail-item strong { color: #1e40af; min-width: 100px; }.rsvp-section { background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%); border-radius: 15px; padding: 30px; text-align: center; margin: 30px 0; }.rsvp-section h3 { color: #065f46; margin-bottom: 15px; font-size: 22px; }.rsvp-section p { color: #065f46; margin-bottom: 25px; font-size: 16px; }.rsvp-buttons { display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; }.btn { display: inline-block; padding: 15px 30px; text-decoration: none; border-radius: 50px; font-weight: 600; font-size: 16px; transition: all 0.3s ease; text-align: center; min-width: 140px; }.btn-accept { background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); }.btn-decline { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3); }.footer { background: #f8f9fa; padding: 30px; text-align: center; border-top: 3px solid #1e3a8a; }.footer p { color: #666; font-size: 14px; margin-bottom: 10px; }</style></head><body><div class="container"><div class="header"><div class="logo"><span style="color: #1e3a8a; font-weight: bold; font-size: 24px;">M</span></div><h1>🎤 Guest Speaker Invitation</h1><p>Misamis Oriental Institute of Science and Technology</p></div><div class="content"><div class="greeting">Dear ' . $fullName . ',</div><div class="message">' . nl2br(htmlspecialchars($content)) . '</div><div class="event-details"><h3>📅 Event Information</h3><div class="detail-item"><strong>Date:</strong> <span>' . htmlspecialchars($event_date) . '</span></div><div class="detail-item"><strong>Time:</strong> <span>' . htmlspecialchars($event_time) . '</span></div><div class="detail-item"><strong>Venue:</strong> <span>' . htmlspecialchars($event_venue) . '</span></div><div class="detail-item"><strong>Topic:</strong> <span>' . htmlspecialchars($event_topic) . '</span></div><div class="detail-item"><strong>Your Course:</strong> <span>' . $course . '</span></div></div><div class="rsvp-section"><h3>🎉 Will You Join Us?</h3><p>Please let us know if you can accept this invitation.</p><div class="rsvp-buttons"><a href="' . $acceptUrl . '" class="btn btn-accept">✅ Yes, I\'ll Speak!</a><a href="' . $declineUrl . '" class="btn btn-decline">❌ Can\'t Make It</a></div></div></div><div class="footer"><p><strong>MOIST Alumni Office</strong></p><p>Guest Speaker Program</p></div></div></body></html>';
}

// ==================== END FUNCTION DEFINITIONS ====================

// Check if action is set
if (!isset($_POST['action'])) {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
    exit();
}

try {
    // Get all validated alumni for selection
    if ($_POST['action'] === 'get_alumni') {
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';
    $course_filter = isset($_POST['course_filter']) ? intval($_POST['course_filter']) : 0;
    
    $query = "SELECT ab.id, ab.firstname, ab.lastname, ab.email, c.course, 
              ab.batch as batch_year,
              CONCAT(ab.firstname, ' ', ab.lastname) as fullname
              FROM alumnus_bio ab 
              LEFT JOIN courses c ON ab.course_id = c.id 
              WHERE ab.status = 1 AND ab.email IS NOT NULL AND ab.email != ''";
    
    if ($search) {
        $search_term = '%' . $conn->real_escape_string($search) . '%';
        $query .= " AND (ab.firstname LIKE '$search_term' OR ab.lastname LIKE '$search_term' 
                    OR ab.email LIKE '$search_term' OR c.course LIKE '$search_term')";
    }
    
    if ($course_filter > 0) {
        $query .= " AND ab.course_id = " . $course_filter;
    }
    
    $query .= " ORDER BY ab.lastname, ab.firstname LIMIT 100";
    
    $result = $conn->query($query);
    if (!$result) {
        echo json_encode(['status' => 'error', 'message' => 'Database query failed: ' . $conn->error]);
        exit();
    }
    
    $alumni = [];
    while ($row = $result->fetch_assoc()) {
        $alumni[] = $row;
    }
    
    echo json_encode(['status' => 'success', 'alumni' => $alumni, 'count' => count($alumni)]);
    exit();
}

// Get courses for filtering
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

// Send test email
if ($_POST['action'] === 'test') {
    $test_email = filter_var($_POST['test_email'] ?? '', FILTER_VALIDATE_EMAIL);
    if (!$test_email) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid test email address']);
        exit();
    }
    
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : 'Guest Speaker Invitation';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $event_date = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $event_time = isset($_POST['event_time']) ? trim($_POST['event_time']) : '';
    $event_venue = isset($_POST['event_venue']) ? trim($_POST['event_venue']) : '';
    $event_topic = isset($_POST['event_topic']) ? trim($_POST['event_topic']) : '';
    
    $test_recipient = [
        'id' => 0,
        'email' => $test_email,
        'firstname' => 'Test',
        'lastname' => 'User',
        'course' => 'Sample Course'
    ];
    
    if (sendSpeakerInvitation($test_recipient, $subject, $content, $event_date, $event_time, $event_venue, $event_topic)) {
        echo json_encode(['status' => 'success', 'message' => 'Test email sent successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send test email']);
    }
    exit();
}

// Send invitations to selected alumni (batch mode)
if ($_POST['action'] === 'send_invitations') {
    $recipients = isset($_POST['recipients']) ? json_decode($_POST['recipients'], true) : [];
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $event_date = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $event_time = isset($_POST['event_time']) ? trim($_POST['event_time']) : '';
    $event_venue = isset($_POST['event_venue']) ? trim($_POST['event_venue']) : '';
    $event_topic = isset($_POST['event_topic']) ? trim($_POST['event_topic']) : '';
    
    if (empty($recipients)) {
        echo json_encode(['status' => 'error', 'message' => 'No recipients selected']);
        exit();
    }
    
    $sent = 0;
    $failed = 0;
    $errors = [];
    
    foreach ($recipients as $recipient) {
        if (sendSpeakerInvitation($recipient, $subject, $content, $event_date, $event_time, $event_venue, $event_topic)) {
            $sent++;
            // Log the invitation
            logSpeakerInvitation($recipient['id'], $event_date, $event_time, $event_venue, $event_topic);
        } else {
            $failed++;
            $errors[] = $recipient['email'];
        }
    }
    
    echo json_encode([
        'status' => 'success',
        'sent' => intval($sent),
        'failed' => intval($failed),
        'total' => intval(count($recipients)),
        'errors' => $errors
    ]);
    exit();
}

// Send single invitation (for real-time progress)
if ($_POST['action'] === 'send_single') {
    $recipient = isset($_POST['recipient']) ? json_decode($_POST['recipient'], true) : null;
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $event_date = isset($_POST['event_date']) ? trim($_POST['event_date']) : '';
    $event_time = isset($_POST['event_time']) ? trim($_POST['event_time']) : '';
    $event_venue = isset($_POST['event_venue']) ? trim($_POST['event_venue']) : '';
    $event_topic = isset($_POST['event_topic']) ? trim($_POST['event_topic']) : '';
    
    if (empty($recipient)) {
        echo json_encode(['status' => 'error', 'message' => 'No recipient provided']);
        exit();
    }
    
    // Validate email address
    if (empty($recipient['email']) || !filter_var($recipient['email'], FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid email address: ' . ($recipient['email'] ?? 'empty'),
            'recipient' => $recipient['email'] ?? 'N/A'
        ]);
        exit();
    }
    
    // Validate required fields
    if (empty($subject)) {
        echo json_encode(['status' => 'error', 'message' => 'Subject is required']);
        exit();
    }
    
    if (empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Email content is required']);
        exit();
    }
    
    $sendResult = sendSpeakerInvitation($recipient, $subject, $content, $event_date, $event_time, $event_venue, $event_topic);
    
    if ($sendResult === true) {
        logSpeakerInvitation($recipient['id'], $event_date, $event_time, $event_venue, $event_topic);
        echo json_encode([
            'status' => 'success',
            'message' => 'Invitation sent successfully',
            'recipient' => $recipient['email']
        ]);
    } else {
        // Get the last error from error log
        $errorDetails = is_string($sendResult) ? $sendResult : 'Failed to send invitation. Please check SMTP settings.';
        echo json_encode([
            'status' => 'error',
            'message' => $errorDetails,
            'recipient' => $recipient['email']
        ]);
    }
    exit();
}

    echo json_encode(['status' => 'error', 'message' => 'Invalid action specified']);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Server error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
} catch (Error $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Fatal error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}
?>
