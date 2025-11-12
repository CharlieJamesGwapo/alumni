<?php
/**
 * Gmail-like Messaging System Handler
 * Handles sending, receiving, and managing messages
 */

ob_start();
date_default_timezone_set('Asia/Manila');
session_start();
require_once '../admin/db_connect.php';
ob_end_clean();

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';
require '../PHPMailer/src/Exception.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type']; // 4 = registrar, 3 = alumni

// ==================== HELPER FUNCTIONS ====================

function sendEmailNotification($recipient, $subject, $message_body, $sender_name = 'MOIST Alumni Office', $message_id = 0, $event_data = null) {
    try {
        $mail = new PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'capstonee2@gmail.com';
        $mail->Password = 'ewushfarghitfdkt';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->CharSet = 'UTF-8';
        
        $mail->setFrom('capstonee2@gmail.com', $sender_name);
        $mail->addAddress($recipient['email'], $recipient['name']);
        $mail->addReplyTo('capstonee2@gmail.com', 'MOIST Alumni Office');
        
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = createEmailTemplate($message_body, $recipient, $subject, $message_id, $event_data);
        $mail->AltBody = strip_tags($message_body);
        
        return $mail->send();
        
    } catch (Exception $e) {
        error_log("Email failed: " . $e->getMessage());
        return false;
    }
}

function createEmailTemplate($content, $recipient, $subject, $message_id = 0, $event_data = null) {
    $name = htmlspecialchars($recipient['name']);
    $recipient_id = $recipient['id'] ?? 0;
    
    // Generate RSVP token
    $token = md5($message_id . $recipient_id . 'moist_rsvp_secret');
    $base_url = 'http://' . $_SERVER['HTTP_HOST'] . '/alumni/registrar/rsvp_handler.php';
    
    // RSVP URLs
    $accept_url = $base_url . '?mid=' . $message_id . '&rid=' . $recipient_id . '&response=accept&token=' . $token;
    $decline_url = $base_url . '?mid=' . $message_id . '&rid=' . $recipient_id . '&response=decline&token=' . $token;
    $maybe_url = $base_url . '?mid=' . $message_id . '&rid=' . $recipient_id . '&response=maybe&token=' . $token;
    
    // Check if subject contains invitation/event keywords
    $has_rsvp = (stripos($subject, 'invite') !== false || 
                  stripos($subject, 'event') !== false || 
                  stripos($subject, 'homecoming') !== false ||
                  stripos($content, 'RSVP') !== false ||
                  stripos($content, 'confirm your attendance') !== false);
    
    // Event details section
    $event_details_html = '';
    if ($event_data && ($event_data['date'] || $event_data['start_time'] || $event_data['end_time'])) {
        $event_details_html = '
        <div style="background: linear-gradient(135deg, #fff8e1 0%, #ffe0b2 100%); padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #ff9800;">
            <h3 style="color: #e65100; margin: 0 0 15px 0; font-size: 18px;">
                📅 Event Details
            </h3>
            <table style="width: 100%; border-collapse: collapse;">
                ' . ($event_data['date'] ? '
                <tr>
                    <td style="padding: 8px 0; color: #666; font-weight: 600; width: 100px;">
                        <i style="color: #ff9800;">📆</i> Date:
                    </td>
                    <td style="padding: 8px 0; color: #333; font-size: 16px; font-weight: 600;">
                        ' . htmlspecialchars($event_data['date']) . '
                    </td>
                </tr>' : '') . '
                ' . ($event_data['start_time'] ? '
                <tr>
                    <td style="padding: 8px 0; color: #666; font-weight: 600;">
                        <i style="color: #ff9800;">🕐</i> Start:
                    </td>
                    <td style="padding: 8px 0; color: #333; font-size: 16px; font-weight: 600;">
                        ' . htmlspecialchars($event_data['start_time']) . '
                    </td>
                </tr>' : '') . '
                ' . ($event_data['end_time'] ? '
                <tr>
                    <td style="padding: 8px 0; color: #666; font-weight: 600;">
                        <i style="color: #ff9800;">🕐</i> End:
                    </td>
                    <td style="padding: 8px 0; color: #333; font-size: 16px; font-weight: 600;">
                        ' . htmlspecialchars($event_data['end_time']) . '
                    </td>
                </tr>' : '') . '
            </table>
        </div>';
    }
    
    $rsvp_buttons = '';
    if ($has_rsvp && $message_id > 0) {
        $rsvp_buttons = '
        <div style="background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%); padding: 30px; margin: 30px 0; border-radius: 12px; text-align: center; border: 3px solid #800000; box-shadow: 0 4px 15px rgba(128,0,0,0.1);">
            <h2 style="color: #800000; margin: 0 0 10px 0; font-size: 24px; font-weight: 800;">
                📅 RSVP Required
            </h2>
            <p style="color: #666; margin-bottom: 25px; font-size: 16px; line-height: 1.6;">
                <strong>Please confirm your attendance by clicking one of the buttons below:</strong><br>
                <small style="color: #999;">Your response helps us plan better for the event</small>
            </p>
            <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin: 20px auto;">
                <tr>
                    <td style="padding: 8px;">
                        <a href="' . $accept_url . '" style="display: inline-block; padding: 18px 40px; background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 800; font-size: 16px; box-shadow: 0 4px 12px rgba(40,167,69,0.4); border: 2px solid #1e7e34; transition: all 0.3s;">
                            ✓ YES, I\'LL ATTEND
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px;">
                        <a href="' . $maybe_url . '" style="display: inline-block; padding: 18px 40px; background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%); color: #333; text-decoration: none; border-radius: 8px; font-weight: 800; font-size: 16px; box-shadow: 0 4px 12px rgba(255,193,7,0.4); border: 2px solid #e0a800; transition: all 0.3s;">
                            ? MAYBE / NOT SURE
                        </a>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px;">
                        <a href="' . $decline_url . '" style="display: inline-block; padding: 18px 40px; background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 800; font-size: 16px; box-shadow: 0 4px 12px rgba(220,53,69,0.4); border: 2px solid #bd2130; transition: all 0.3s;">
                            ✗ NO, CAN\'T ATTEND
                        </a>
                    </td>
                </tr>
            </table>
            <p style="color: #800000; margin-top: 20px; font-size: 13px; font-weight: 600;">
                ⚡ Click any button above - your response will be recorded instantly!
            </p>
        </div>';
    }
    
    return '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($subject) . '</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; }
        .header { background: linear-gradient(135deg, #800000 0%, #a00000 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { font-size: 24px; margin-bottom: 5px; }
        .content { padding: 30px; }
        .message-body { font-size: 15px; line-height: 1.8; color: #444; white-space: pre-wrap; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 13px; color: #666; border-top: 1px solid #e0e0e0; }
        .btn { display: inline-block; padding: 12px 24px; background: #800000; color: white; text-decoration: none; border-radius: 5px; margin: 15px 5px; font-weight: 600; }
        .btn:hover { background: #a00000; }
        .reply-info { background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #2196f3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>MOIST Alumni Office</h1>
            <p>Misamis Oriental Institute of Science and Technology</p>
        </div>
        <div class="content">
            <div class="message-body">
                ' . nl2br($content) . '
            </div>
            
            ' . $event_details_html . '
            
            ' . $rsvp_buttons . '
            
            <div class="reply-info" style="background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%); padding: 25px; margin: 25px 0; border-radius: 12px; border: 3px solid #1976d2; box-shadow: 0 4px 15px rgba(25,118,210,0.1);">
                <h3 style="color: #1976d2; margin: 0 0 15px 0; font-size: 20px; font-weight: 800;">
                    💬 Have Questions or Need to Reply?
                </h3>
                <p style="margin: 0 0 20px 0; color: #333; font-size: 15px; line-height: 1.6; text-align: center;">
                    <strong>Click the button below to send us a message!</strong>
                </p>
                
                <table cellpadding="0" cellspacing="0" border="0" align="center" style="margin: 20px auto;">
                    <tr>
                        <td style="padding: 5px;">
                            <a href="http://' . $_SERVER['HTTP_HOST'] . '/alumni/registrar/alumni_reply.php?mid=' . $message_id . '&rid=' . $recipient_id . '&token=' . $token . '" style="display: inline-block; padding: 18px 40px; background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%); color: white; text-decoration: none; border-radius: 8px; font-weight: 800; font-size: 16px; box-shadow: 0 4px 12px rgba(25,118,210,0.4); border: 2px solid #0d47a1;">
                                💬 SEND A MESSAGE
                            </a>
                        </td>
                    </tr>
                </table>
                
                <p style="margin: 15px 0 0 0; color: #666; font-size: 13px; text-align: center;">
                    Or reply directly to: <strong style="color: #800000;">capstonee2@gmail.com</strong><br>
                    ⚡ We typically respond within 24 hours
                </p>
            </div>
        </div>
        <div class="footer">
            <p><strong>MOIST Alumni Office</strong></p>
            <p>Misamis Oriental Institute of Science and Technology</p>
            <p style="margin-top: 10px;">
                📧 Email: capstonee2@gmail.com<br>
                🌐 Visit the <a href="http://' . $_SERVER['HTTP_HOST'] . '/alumni/home.php" style="color: #800000;">Alumni Portal</a>
            </p>
        </div>
    </div>
    
    <!-- Email Open Tracking Pixel -->
    <img src="http://' . $_SERVER['HTTP_HOST'] . '/alumni/registrar/track_email_open.php?mid=' . $message_id . '&rid=' . $recipient_id . '" width="1" height="1" style="display:none;" alt="">
    
</body>
</html>';
}

function replacePlaceholders($content, $recipient) {
    $replacements = [
        '{{name}}' => $recipient['firstname'] . ' ' . $recipient['lastname'],
        '{{firstname}}' => $recipient['firstname'],
        '{{lastname}}' => $recipient['lastname'],
        '{{email}}' => $recipient['email'],
        '{{course}}' => $recipient['course'] ?? 'N/A',
        '{{batch}}' => $recipient['batch'] ?? 'N/A'
    ];
    
    return str_replace(array_keys($replacements), array_values($replacements), $content);
}

// ==================== ACTION HANDLERS ====================

// Check if action is set
$action = $_POST['action'] ?? '';

// Log the action for debugging
error_log("Message action received: " . $action);

if (empty($action)) {
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
    exit();
}

try {
    
    // Get all alumni for recipient selection
    if ($action === 'get_alumni') {
        $search = isset($_POST['search']) ? trim($_POST['search']) : '';
        $course_filter = isset($_POST['course_filter']) ? intval($_POST['course_filter']) : 0;
        
        $query = "SELECT ab.id, ab.firstname, ab.lastname, ab.email, c.course, ab.batch,
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
        
        $query .= " ORDER BY ab.lastname, ab.firstname LIMIT 200";
        
        $result = $conn->query($query);
        $alumni = [];
        while ($row = $result->fetch_assoc()) {
            $alumni[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'alumni' => $alumni, 'count' => count($alumni)]);
        exit();
    }
    
    // Get courses for filtering
    if ($action === 'get_courses') {
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
    
    // Get email templates
    if ($action === 'get_templates') {
        try {
            $query = "SELECT * FROM email_templates WHERE is_active = 1 ORDER BY category, template_name";
            $result = $conn->query($query);
            
            if (!$result) {
                error_log("Template query failed: " . $conn->error);
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'Database error: ' . $conn->error,
                    'templates' => []
                ]);
                exit();
            }
            
            $templates = [];
            while ($row = $result->fetch_assoc()) {
                $templates[] = $row;
            }
            
            error_log("Loaded " . count($templates) . " templates");
            
            if (count($templates) === 0) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No templates found in database. Please run setup_messaging_db.php',
                    'templates' => []
                ]);
            } else {
                echo json_encode(['status' => 'success', 'templates' => $templates]);
            }
            exit();
        } catch (Exception $e) {
            error_log("Template error: " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => 'Error loading templates: ' . $e->getMessage(),
                'templates' => []
            ]);
            exit();
        }
    }
    
    // Save new template
    if ($action === 'save_template') {
        if ($user_type != 4) {
            echo json_encode(['status' => 'error', 'message' => 'Only registrar can save templates']);
            exit();
        }
        
        $template_name = trim($_POST['template_name'] ?? '');
        $template_subject = trim($_POST['template_subject'] ?? '');
        $template_body = trim($_POST['template_body'] ?? '');
        $category = trim($_POST['category'] ?? 'general');
        
        if (empty($template_name) || empty($template_subject) || empty($template_body)) {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
            exit();
        }
        
        $stmt = $conn->prepare("INSERT INTO email_templates (template_name, template_subject, template_body, category, created_by) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $template_name, $template_subject, $template_body, $category, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Template saved successfully', 'template_id' => $stmt->insert_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to save template: ' . $conn->error]);
        }
        exit();
    }
    
    // Send message to single or multiple recipients
    if ($action === 'send_message') {
        $recipients = isset($_POST['recipients']) ? json_decode($_POST['recipients'], true) : [];
        $subject = trim($_POST['subject'] ?? '');
        $message_body = trim($_POST['message_body'] ?? '');
        $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : null;
        $send_email = isset($_POST['send_email']) && $_POST['send_email'] === 'true';
        
        if (empty($recipients)) {
            echo json_encode(['status' => 'error', 'message' => 'No recipients selected']);
            exit();
        }
        
        if (empty($subject) || empty($message_body)) {
            echo json_encode(['status' => 'error', 'message' => 'Subject and message are required']);
            exit();
        }
        
        $sender_type = ($user_type == 4) ? 'registrar' : 'alumni';
        $sent_count = 0;
        $failed_count = 0;
        $message_ids = [];
        
        foreach ($recipients as $recipient) {
            // Replace placeholders
            $personalized_subject = replacePlaceholders($subject, $recipient);
            $personalized_body = replacePlaceholders($message_body, $recipient);
            
            // Save to database
            $stmt = $conn->prepare("INSERT INTO messages (sender_id, sender_type, recipient_id, recipient_type, subject, message_body, template_id, sent_at) 
                                   VALUES (?, ?, ?, 'alumni', ?, ?, ?, NOW())");
            $stmt->bind_param("isissi", $user_id, $sender_type, $recipient['id'], $personalized_subject, $personalized_body, $template_id);
            
            if ($stmt->execute()) {
                $message_id = $stmt->insert_id;
                $message_ids[] = $message_id;
                
                // Add to message_recipients table
                $rec_stmt = $conn->prepare("INSERT INTO message_recipients (message_id, recipient_id, recipient_type, recipient_email) 
                                           VALUES (?, ?, 'alumni', ?)");
                $rec_stmt->bind_param("iis", $message_id, $recipient['id'], $recipient['email']);
                $rec_stmt->execute();
                
                // Send email notification if requested
                if ($send_email) {
                    $email_recipient = [
                        'email' => $recipient['email'],
                        'name' => $recipient['firstname'] . ' ' . $recipient['lastname']
                    ];
                    sendEmailNotification($email_recipient, $personalized_subject, $personalized_body);
                }
                
                $sent_count++;
            } else {
                $failed_count++;
            }
        }
        
        echo json_encode([
            'status' => 'success',
            'sent' => $sent_count,
            'failed' => $failed_count,
            'total' => count($recipients),
            'message_ids' => $message_ids
        ]);
        exit();
    }
    
    // Send single message (for real-time progress)
    if ($action === 'send_single') {
        $recipient = isset($_POST['recipient']) ? json_decode($_POST['recipient'], true) : null;
        $subject = trim($_POST['subject'] ?? '');
        $message_body = trim($_POST['message_body'] ?? '');
        $template_id = isset($_POST['template_id']) ? intval($_POST['template_id']) : null;
        $send_email = isset($_POST['send_email']) && $_POST['send_email'] === 'true';
        
        // Get event data if provided
        $event_date = trim($_POST['event_date'] ?? '');
        $event_start_time = trim($_POST['event_start_time'] ?? '');
        $event_end_time = trim($_POST['event_end_time'] ?? '');
        
        $event_data = null;
        if ($event_date || $event_start_time || $event_end_time) {
            $event_data = [
                'date' => $event_date,
                'start_time' => $event_start_time,
                'end_time' => $event_end_time
            ];
        }
        
        if (empty($recipient)) {
            echo json_encode(['status' => 'error', 'message' => 'No recipient provided']);
            exit();
        }
        
        // Replace placeholders
        $personalized_subject = replacePlaceholders($subject, $recipient);
        $personalized_body = replacePlaceholders($message_body, $recipient);
        
        $sender_type = ($user_type == 4) ? 'registrar' : 'alumni';
        
        // Save to database
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, sender_type, recipient_id, recipient_type, subject, message_body, template_id, sent_at) 
                               VALUES (?, ?, ?, 'alumni', ?, ?, ?, NOW())");
        $stmt->bind_param("isissi", $user_id, $sender_type, $recipient['id'], $personalized_subject, $personalized_body, $template_id);
        
        if ($stmt->execute()) {
            $message_id = $stmt->insert_id;
            
            // Add to message_recipients table
            $rec_stmt = $conn->prepare("INSERT INTO message_recipients (message_id, recipient_id, recipient_type, recipient_email) 
                                       VALUES (?, ?, 'alumni', ?)");
            $rec_stmt->bind_param("iis", $message_id, $recipient['id'], $recipient['email']);
            $rec_stmt->execute();
            
            // Send email notification if requested
            if ($send_email) {
                $email_recipient = [
                    'id' => $recipient['id'],
                    'email' => $recipient['email'],
                    'name' => $recipient['firstname'] . ' ' . $recipient['lastname'],
                    'firstname' => $recipient['firstname'],
                    'lastname' => $recipient['lastname']
                ];
                $email_sent = sendEmailNotification($email_recipient, $personalized_subject, $personalized_body, 'MOIST Alumni Office', $message_id, $event_data);
            }
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Message sent successfully',
                'message_id' => $message_id,
                'recipient' => $recipient['email'],
                'email_sent' => $send_email && $email_sent
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send message: ' . $conn->error]);
        }
        exit();
    }
    
    // Get sent messages (for registrar)
    if ($action === 'get_sent_messages') {
        if ($user_type != 4) {
            echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
            exit();
        }
        
        $query = "SELECT m.*, COUNT(DISTINCT mr.id) as recipient_count,
                  SUM(CASE WHEN mr.is_read = 1 THEN 1 ELSE 0 END) as read_count,
                  SUM(CASE WHEN mr.rsvp_status = 'accept' THEN 1 ELSE 0 END) as accept_count,
                  SUM(CASE WHEN mr.rsvp_status = 'decline' THEN 1 ELSE 0 END) as decline_count,
                  SUM(CASE WHEN mr.rsvp_status = 'maybe' THEN 1 ELSE 0 END) as maybe_count,
                  SUM(CASE WHEN mr.rsvp_status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                  COUNT(DISTINCT rep.id) as reply_count
                  FROM messages m
                  LEFT JOIN message_recipients mr ON m.id = mr.message_id
                  LEFT JOIN message_replies rep ON m.id = rep.message_id
                  WHERE m.sender_id = ? AND m.sender_type = 'registrar' AND m.is_deleted = 0
                  GROUP BY m.id
                  ORDER BY m.sent_at DESC
                  LIMIT 100";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'messages' => $messages]);
        exit();
    }
    
    // Get received messages (for alumni)
    if ($action === 'get_received_messages') {
        $query = "SELECT m.*, ab.firstname as sender_firstname, ab.lastname as sender_lastname,
                  mr.is_read, mr.read_at
                  FROM messages m
                  INNER JOIN message_recipients mr ON m.id = mr.message_id
                  LEFT JOIN alumnus_bio ab ON m.sender_id = ab.id
                  WHERE mr.recipient_id = ? AND m.is_deleted = 0
                  ORDER BY m.sent_at DESC
                  LIMIT 100";
        
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
        
        echo json_encode(['status' => 'success', 'messages' => $messages]);
        exit();
    }
    
    // Mark message as read
    if ($action === 'mark_read') {
        $message_id = intval($_POST['message_id'] ?? 0);
        
        if ($message_id <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid message ID']);
            exit();
        }
        
        $stmt = $conn->prepare("UPDATE message_recipients SET is_read = 1, read_at = NOW() 
                               WHERE message_id = ? AND recipient_id = ?");
        $stmt->bind_param("ii", $message_id, $user_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Message marked as read']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to update message']);
        }
        exit();
    }
    
    // Get message details with recipients
    if ($action === 'get_message_details') {
        $message_id = intval($_POST['message_id'] ?? 0);
        
        $stmt = $conn->prepare("SELECT m.*, 
                               GROUP_CONCAT(CONCAT(ab.firstname, ' ', ab.lastname) SEPARATOR ', ') as recipients_names
                               FROM messages m
                               LEFT JOIN message_recipients mr ON m.id = mr.message_id
                               LEFT JOIN alumnus_bio ab ON mr.recipient_id = ab.id
                               WHERE m.id = ?
                               GROUP BY m.id");
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            // Get detailed recipients
            $rec_stmt = $conn->prepare("SELECT mr.*, ab.firstname, ab.lastname, ab.email 
                                       FROM message_recipients mr
                                       JOIN alumnus_bio ab ON mr.recipient_id = ab.id
                                       WHERE mr.message_id = ?");
            $rec_stmt->bind_param("i", $message_id);
            $rec_stmt->execute();
            $rec_result = $rec_stmt->get_result();
            
            $recipients = [];
            while ($rec_row = $rec_result->fetch_assoc()) {
                $recipients[] = $rec_row;
            }
            
            $row['recipients'] = $recipients;
            echo json_encode(['status' => 'success', 'message' => $row]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Message not found']);
        }
        exit();
    }
    
    // Send reply to registrar (from alumni)
    if ($action === 'send_reply') {
        $parent_message_id = intval($_POST['parent_message_id'] ?? 0);
        $subject = trim($_POST['subject'] ?? '');
        $message_body = trim($_POST['message_body'] ?? '');
        
        if (empty($subject) || empty($message_body)) {
            echo json_encode(['status' => 'error', 'message' => 'Subject and message are required']);
            exit();
        }
        
        $sender_type = ($user_type == 4) ? 'registrar' : 'alumni';
        $recipient_type = ($user_type == 4) ? 'alumni' : 'registrar';
        
        // Get original message to determine recipient
        $stmt = $conn->prepare("SELECT sender_id, sender_type FROM messages WHERE id = ?");
        $stmt->bind_param("i", $parent_message_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $original = $result->fetch_assoc();
        
        if (!$original) {
            echo json_encode(['status' => 'error', 'message' => 'Original message not found']);
            exit();
        }
        
        // Insert reply
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, sender_type, recipient_id, recipient_type, subject, message_body, parent_message_id, sent_at) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("isissi", $user_id, $sender_type, $original['sender_id'], $recipient_type, $subject, $message_body, $parent_message_id);
        
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Reply sent successfully', 'message_id' => $stmt->insert_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send reply']);
        }
        exit();
    }
    
    // Mark as unread
    if ($action === 'mark_unread') {
        $message_id = intval($_POST['message_id'] ?? 0);
        
        if ($message_id) {
            $stmt = $conn->prepare("UPDATE message_recipients 
                                   SET is_read = 0, read_at = NULL 
                                   WHERE message_id = ?");
            $stmt->bind_param("i", $message_id);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Message marked as unread']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to mark as unread']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid message ID']);
        }
        exit();
    }
    
    // Archive message
    if ($action === 'archive_message') {
        $message_id = intval($_POST['message_id'] ?? 0);
        
        if ($message_id) {
            $stmt = $conn->prepare("UPDATE messages SET is_archived = 1 WHERE id = ? AND sender_id = ?");
            $stmt->bind_param("ii", $message_id, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Message archived successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to archive message']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid message ID']);
        }
        exit();
    }
    
    // Unarchive message
    if ($action === 'unarchive_message') {
        $message_id = intval($_POST['message_id'] ?? 0);
        
        if ($message_id) {
            $stmt = $conn->prepare("UPDATE messages SET is_archived = 0 WHERE id = ? AND sender_id = ?");
            $stmt->bind_param("ii", $message_id, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Message unarchived successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to unarchive message']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid message ID']);
        }
        exit();
    }
    
    // Delete message
    if ($action === 'delete_message') {
        $message_id = intval($_POST['message_id'] ?? 0);
        
        if ($message_id) {
            // Delete from message_recipients first (foreign key)
            $stmt = $conn->prepare("DELETE FROM message_recipients WHERE message_id = ?");
            $stmt->bind_param("i", $message_id);
            $stmt->execute();
            
            // Delete from message_replies
            $stmt = $conn->prepare("DELETE FROM message_replies WHERE message_id = ?");
            $stmt->bind_param("i", $message_id);
            $stmt->execute();
            
            // Delete the message
            $stmt = $conn->prepare("DELETE FROM messages WHERE id = ? AND sender_id = ?");
            $stmt->bind_param("ii", $message_id, $user_id);
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Message deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete message']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid message ID']);
        }
        exit();
    }
    
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
