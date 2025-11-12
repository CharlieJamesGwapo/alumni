<?php
// send_birthday_greetings.php
// Endpoint to send birthday greetings to alumni whose birthdate matches today.

session_start();
include '../admin/db_connect.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Only Registrar (type 4) allowed
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

header('Content-Type: application/json');

$action = $_POST['action'] ?? 'send_all';
$test_email = filter_var($_POST['test_email'] ?? '', FILTER_VALIDATE_EMAIL);
$custom_message = $_POST['custom_message'] ?? '';

// Get today's month-day
$today_md = date('m-d');

// Support 'get_template' action to return the default birthday message template
if ($action === 'get_template') {
    $sample = ['firstname' => 'John', 'lastname' => 'Doe', 'birthdate' => date('Y-m-d')];
    $template = buildBirthdayMessage($sample, '');
    echo json_encode(['status' => 'ok', 'template' => $template]);
    exit;
}

// Support 'count' action to only return how many alumni have birthday today
if ($action === 'count') {
    $count_q = "SELECT COUNT(*) as cnt FROM alumnus_bio WHERE birthdate IS NOT NULL AND birthdate != '' AND DATE_FORMAT(birthdate, '%m-%d') = ?";
    $cstmt = $conn->prepare($count_q);
    $cstmt->bind_param('s', $today_md);
    $cstmt->execute();
    $cres = $cstmt->get_result();
    $crow = $cres->fetch_assoc();
    echo json_encode(['status' => 'ok', 'total' => intval($crow['cnt'] ?? 0)]);
    exit;
}

// Support 'test' action to send a single test email to $test_email
if ($action === 'test') {
    if (!$test_email) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid test email']);
        exit;
    }
    // build a sample payload using first name fallback
    $sample = ['firstname' => 'Alumni', 'lastname' => '', 'email' => $test_email, 'birthdate' => date('Y-m-d')];
    $sent = sendBirthdayEmail($sample, $custom_message);
    if ($sent) {
        echo json_encode(['status' => 'ok', 'message' => 'Test email sent']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send test email']);
    }
    exit;
}

// Support 'list' action to return all birthdays (optionally for a given month)
if ($action === 'list') {
    // optional month param (1-12). If not provided, return all.
    $month = isset($_POST['month']) ? intval($_POST['month']) : 0;
    if ($month >= 1 && $month <= 12) {
        $q = "SELECT id, firstname, lastname, email, DATE_FORMAT(birthdate, '%Y-%m-%d') as birthdate, DATE_FORMAT(birthdate, '%m-%d') as md FROM alumnus_bio WHERE birthdate IS NOT NULL AND birthdate != '' AND MONTH(birthdate)=? ORDER BY DAY(birthdate), firstname";
        $s = $conn->prepare($q);
        $s->bind_param('i', $month);
    } else {
        $q = "SELECT id, firstname, lastname, email, DATE_FORMAT(birthdate, '%Y-%m-%d') as birthdate, DATE_FORMAT(birthdate, '%m-%d') as md FROM alumnus_bio WHERE birthdate IS NOT NULL AND birthdate != '' ORDER BY MONTH(birthdate), DAY(birthdate), firstname";
        $s = $conn->prepare($q);
    }
    $s->execute();
    $res = $s->get_result();
    $out = [];
    while ($r = $res->fetch_assoc()) {
        $out[] = $r;
    }
    echo json_encode(['status' => 'ok', 'data' => $out]);
    exit;
}

// Support 'send_date' action to send greetings for a specified date (Y-m-d or mm-dd). If absent, uses today.
if ($action === 'send_date') {
    $date = trim($_POST['date'] ?? '');
    $md = '';
    if ($date === '') {
        $md = date('m-d');
    } else {
        // accept YYYY-MM-DD or MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $md = date('m-d', strtotime($date));
        } elseif (preg_match('/^\d{2}-\d{2}$/', $date)) {
            $md = $date;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid date']);
            exit;
        }
    }

    $sql = "SELECT id, firstname, lastname, email, birthdate FROM alumnus_bio WHERE birthdate IS NOT NULL AND birthdate != '' AND DATE_FORMAT(birthdate, '%m-%d') = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $md);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($r = $res->fetch_assoc()) {
        if (!empty($r['email']) && filter_var($r['email'], FILTER_VALIDATE_EMAIL)) $rows[] = $r;
    }

    if (count($rows) === 0) {
        echo json_encode(['status' => 'ok', 'message' => 'No alumni have birthday on that date', 'total' => 0]);
        exit;
    }

    $sent = 0; $failed = 0; $errors = [];
    foreach ($rows as $r) {
        $ok = sendBirthdayEmail($r, $custom_message);
        if ($ok) $sent++; else { $failed++; $errors[] = $r['email']; }
    }
    echo json_encode(['status' => 'ok', 'message' => 'Processed', 'total' => count($rows), 'sent' => $sent, 'failed' => $failed, 'errors' => $errors]);
    exit;
}

// Default: send to all alumni with birthday today
// We'll query alumnus_bio for rows where DATE_FORMAT(birthdate, '%m-%d') = $today_md
$sql = "SELECT id, firstname, lastname, email, birthdate FROM alumnus_bio WHERE birthdate IS NOT NULL AND birthdate != '' AND DATE_FORMAT(birthdate, '%m-%d') = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $today_md);
$stmt->execute();
$res = $stmt->get_result();
$rows = [];
while ($r = $res->fetch_assoc()) {
    // only include rows with valid email
    if (!empty($r['email']) && filter_var($r['email'], FILTER_VALIDATE_EMAIL)) {
        $rows[] = $r;
    }
}

if (count($rows) === 0) {
    echo json_encode(['status' => 'ok', 'message' => 'No alumni have birthday today', 'total' => 0]);
    exit;
}

$sent = 0; $failed = 0; $errors = [];
foreach ($rows as $r) {
    $ok = sendBirthdayEmail($r, $custom_message);
    if ($ok) $sent++; else { $failed++; $errors[] = $r['email']; }
}

echo json_encode(['status' => 'ok', 'message' => 'Processed', 'total' => count($rows), 'sent' => $sent, 'failed' => $failed, 'errors' => $errors]);

// Function to compose and send an email using PHPMailer. Returns true on success.
function sendBirthdayEmail($row, $customMessage = '') {
    // Customize these from-address values to match your environment
    $fromEmail = 'no-reply@moist.edu.ph';
    $fromName = 'MOIST Alumni Office';

    $mail = new PHPMailer(true);
    try {
        // SMTP configuration - adjust to your mail server
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // TODO: change to real SMTP host
        $mail->SMTPAuth = true;
        $mail->Username = 'capstonee2@gmail.com'; // TODO
        $mail->Password = 'ewushfarghitfdkt'; // TODO
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom($fromEmail, $fromName);
        $mail->addAddress($row['email'], trim($row['firstname'] . ' ' . $row['lastname']));
        $mail->isHTML(true);

        $subject = 'Happy Birthday from MOIST Alumni Office!';
        $body = buildBirthdayMessage($row, $customMessage);

        $mail->Subject = $subject;
        $mail->Body = $body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $body));

        return $mail->send();
    } catch (Exception $e) {
        error_log('Birthday email failed for ' . ($row['email'] ?? 'unknown') . ': ' . $mail->ErrorInfo);
        return false;
    }
}

function buildBirthdayMessage($row, $customMessage = '') {
    $name = trim($row['firstname'] . ' ' . $row['lastname']);
    $firstname = htmlspecialchars($row['firstname']);
    $lastname = htmlspecialchars($row['lastname']);
    $birthdate = !empty($row['birthdate']) ? date('F j', strtotime($row['birthdate'])) : '';
    
    // Use custom message if provided, otherwise use default
    if (!empty($customMessage)) {
        // Replace placeholders in custom message
        $message = str_replace(
            ['{{firstname}}', '{{lastname}}', '{{name}}', '{{birthdate}}'],
            [$firstname, $lastname, htmlspecialchars($name), htmlspecialchars($birthdate)],
            $customMessage
        );
    } else {
        // Default message
        $message = "<p>Dear " . htmlspecialchars($name) . ",</p>";
        $message .= "<p>Warmest wishes from the MOIST Alumni Office on your special day (" . htmlspecialchars($birthdate) . "). May your day be filled with joy, good health, and wonderful memories.</p>";
        $message .= "<p>We appreciate being part of your alumni journey. We hope to see you at our next alumni event!</p>";
    }
    
    // Professional HTML template
    $body = '
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Happy Birthday!</title>
    </head>
    <body style="margin:0;padding:0;font-family:Arial,Helvetica,sans-serif;background:#f4f4f4">
        <div style="max-width:600px;margin:20px auto;background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.1)">
            <div style="background:linear-gradient(135deg, #800000 0%, #600000 100%);padding:40px 30px;text-align:center">
                <div style="font-size:60px;margin-bottom:10px">🎂</div>
                <h1 style="color:#fbbf24;margin:0;font-size:32px;text-shadow:2px 2px 4px rgba(0,0,0,0.3)">Happy Birthday!</h1>
            </div>
            <div style="padding:40px 30px;color:#333">
                ' . $message . '
                <div style="margin-top:30px;padding:20px;background:linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);border-radius:8px;border-left:4px solid #f59e0b">
                    <p style="margin:0;font-size:16px;color:#78350f">🎉 <strong>Wishing you a wonderful year ahead!</strong></p>
                </div>
            </div>
            <div style="background:#f8f9fa;padding:30px;text-align:center;border-top:3px solid #800000">
                <p style="margin:0;color:#666;font-size:14px"><strong>MOIST Alumni Office</strong></p>
                <p style="margin:5px 0 0 0;color:#999;font-size:12px">Misamis Oriental Institute of Science and Technology</p>
            </div>
        </div>
    </body>
    </html>';
    
    return $body;
}

?>
