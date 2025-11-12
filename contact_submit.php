<?php
// Simple AJAX contact handler for admission inquiries
header('Content-Type: application/json; charset=utf-8');
$response = ['success' => false, 'message' => ''];

function bad($msg){ echo json_encode(['success'=>false,'message'=>$msg]); exit; }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') bad('Invalid request');

$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';

if (!$name || !$email || !$message) bad('Please complete required fields');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) bad('Please provide a valid email address');

// Log submission to a simple log file (append)
$logDir = __DIR__ . DIRECTORY_SEPARATOR . 'logs';
if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
$logFile = $logDir . DIRECTORY_SEPARATOR . 'admission_contacts.log';
$entry = sprintf("[%s] Name: %s | Email: %s | Phone: %s | Message: %s\n", date('Y-m-d H:i:s'), $name, $email, $phone, str_replace("\n", ' ', $message));
@file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);

// Attempt to send email to admissions inbox (best-effort)
$to = 'moist@moist.edu.ph';
$subject = 'Admission Contact Form: ' . substr($name,0,50);
$body = "You have a new admission inquiry:\n\nName: $name\nEmail: $email\nPhone: $phone\nMessage:\n$message\n\n--\nFrom your website.";
$headers = 'From: ' . $email . "\r\n" . 'Reply-To: ' . $email . "\r\n" . 'X-Mailer: PHP/' . phpversion();

$mailSent = false;
try{
    if (function_exists('mail')){
        @mail($to, $subject, $body, $headers);
        $mailSent = true; // optimistic; cannot inspect return reliably on some hosts
    }
}catch(Exception $e){ /* ignore mail exceptions */ }

echo json_encode(['success'=>true,'message'=>'Your message was received. We will contact you shortly.']);
exit;
