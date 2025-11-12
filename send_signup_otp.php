<?php
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');
// include DB with protection against connection errors so we can return JSON
try{
    require_once 'admin/db_connect.php';
} catch (Throwable $e) {
    // log and return JSON error for frontend
    error_log("DB connect error in send_signup_otp: " . $e->getMessage(), 3, __DIR__.'/logs/otp_debug.log');
    echo json_encode(['status'=>'error','message'=>'Database connection failed. Please start the database server.']);
    exit;
}

// Simple endpoint to send OTP for signup verification (accepts email or phone)
// Expected POST: contact, method ('email' or 'sms')
// Returns JSON {status: 'success'|'error', message: '', cooldown: seconds}

function log_debug($msg){
    error_log("[".date('Y-m-d H:i:s')."] SEND_SIGNUP: $msg\n", 3, __DIR__.'/logs/otp_debug.log');
}

$contact = trim($_POST['contact'] ?? '');
$method = trim($_POST['method'] ?? 'email');

if(!$contact){
    echo json_encode(['status'=>'error','message'=>'Contact is required','cooldown'=>0]);
    exit;
}

// Normalize
if($method === 'email') $email = $contact; else $email = filter_var($contact, FILTER_VALIDATE_EMAIL) ? $contact : null;
$phone = null;
if($method === 'sms') $phone = preg_replace('/[^0-9+]/','',$contact);

// Look up in alumni_ids by email or contact_no
if($email){
    $stmt = $conn->prepare("SELECT alumni_id FROM alumni_ids WHERE email = ? LIMIT 1");
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $found_contact = $email;
} else {
    $stmt = $conn->prepare("SELECT alumni_id FROM alumni_ids WHERE contact_no = ? LIMIT 1");
    $stmt->bind_param('s',$phone);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();
    $found_contact = $phone;
}

if(!$row){
    echo json_encode(['status'=>'error','message'=>'Contact not found in alumni records. Please check and try again.','cooldown'=>0]);
    exit;
}

$alumni_id = $row['alumni_id'];

// Rate limiting: don't send more than once per 60s
$cooldownSeconds = 60;
$chk = $conn->prepare("SELECT created_at FROM otp_verifications WHERE (email = ? OR phone = ?) AND used = 0 ORDER BY created_at DESC LIMIT 1");
$chk->bind_param('ss',$found_contact,$found_contact);
$chk->execute();
$cres = $chk->get_result();
if($cres && $cres->num_rows){
    $r = $cres->fetch_assoc();
    $created = strtotime($r['created_at']);
    $elapsed = time() - $created;
    if($elapsed < $cooldownSeconds){
        $remain = $cooldownSeconds - $elapsed;
        echo json_encode(['status'=>'error','message'=>'An OTP was recently sent. Please wait.','cooldown'=>$remain]);
        exit;
    }
}

$otp = sprintf('%06d', mt_rand(100000,999999));
$expiry = date('Y-m-d H:i:s', strtotime('+5 minutes'));

// Insert OTP
$ins = $conn->prepare("INSERT INTO otp_verifications (email, phone, otp, expires_at, used, created_at) VALUES (?, ?, ?, ?, 0, NOW())");
$ins->bind_param('ssss', $found_contact, $found_contact, $otp, $expiry);
$ins->execute();

log_debug("contact=$found_contact otp=$otp alumni_id=$alumni_id");

// Send email if method email
if($method === 'email'){
    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    require 'PHPMailer/src/Exception.php';
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try{
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'capstonee2@gmail.com';
        $mail->Password = 'ewus hfar ghit fdkt';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        $mail->setFrom('capstonee2@gmail.com','MOIST Alumni Portal');
        $mail->addAddress($found_contact);
        $mail->isHTML(true);
        $mail->Subject = 'Your MOIST Signup OTP';
        $mail->Body = 'Your OTP is <strong>'.$otp.'</strong>. It will expire in 5 minutes.';
        $mail->AltBody = 'Your OTP is '.$otp;
        $mail->send();
    }catch(Exception $e){
        log_debug('mailerr:'.$e->getMessage());
        // don't fail; still allow verifying via other means
    }
}

// Send SMS if method sms using local TextBlaster server (per user request)
if($method === 'sms'){
    // small helper to normalize Philippine local numbers -> E.164 (used for parsing)
    function normalize_e164($num){
        $n = preg_replace('/[^0-9+]/','',$num);
        if(!$n) return '';
        if(strpos($n, '+') === 0) return $n;
        if(preg_match('/^0[0-9]{9,10}$/', $n)){
            return '+63'.substr($n,1);
        }
        if(preg_match('/^[0-9]{10}$/', $n)){
            return '+63'.$n;
        }
        return $n;
    }

    $to_number = normalize_e164($found_contact);

    // TextBlaster server settings (HTTP GET, expects 11-digit local phone like 09856122843)
    $textblaster_base = 'http://192.168.100.190:8080/';

    // Convert +63XXXXXXXXX to local 0XXXXXXXXXX format expected by TextBlaster
    $phone_for_tb = preg_replace('/^\+63/', '0', $to_number);
    $phone_for_tb = preg_replace('/^[+]/', '', $phone_for_tb);
    $phone_for_tb = preg_replace('/[^0-9]/','',$phone_for_tb);

    // Prepare message (max 255 chars per your server spec)
    $msg = "Your MOIST OTP is: $otp (valid 5 minutes)";
    if(strlen($msg) > 255) $msg = substr($msg,0,252).'...';

    $query = http_build_query([
        'phoneNumber' => $phone_for_tb,
        'message' => $msg
    ]);

    $url = $textblaster_base . '?' . $query;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlerr = curl_error($ch);
    curl_close($ch);

    if($response === false || ($httpcode < 200 || $httpcode >= 400)){
        log_debug("textblaster_failed to=$phone_for_tb http=$httpcode err=$curlerr resp=".substr(($response??''),0,500));
        echo json_encode(['status'=>'error','message'=>'Failed to send SMS via TextBlaster. Check server accessibility and logs. OTP is stored in DB for debugging.','cooldown'=>0]);
        exit;
    }

    log_debug("sms_sent_textblaster to=$phone_for_tb otp=$otp alumni_id=$alumni_id http=$httpcode resp=".substr($response,0,500));
}

// Put a simple session marker to remember last contact used
$_SESSION['signup_otp_contact'] = $found_contact;

echo json_encode(['status'=>'success','message'=>'OTP sent successfully','cooldown'=>$cooldownSeconds]);
exit;

?>
