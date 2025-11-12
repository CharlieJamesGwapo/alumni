<?php
date_default_timezone_set('Asia/Manila');
session_start();
header('Content-Type: application/json');
try{
    require_once 'admin/db_connect.php';
} catch (Throwable $e) {
    error_log("DB connect error in verify_signup_otp: " . $e->getMessage(), 3, __DIR__.'/logs/otp_debug.log');
    echo json_encode(['status'=>'error','message'=>'Database connection failed. Please start the database server.']);
    exit;
}

function log_debug($msg){
    error_log("[".date('Y-m-d H:i:s')."] RECV_VERIFY_SIGNUP: $msg\n", 3, __DIR__.'/logs/otp_debug.log');
}

$contact = trim($_POST['contact'] ?? '');
$otp = trim($_POST['otp'] ?? '');

if(!$contact || !$otp){
    echo json_encode(['status'=>'error','message'=>'Missing contact or OTP']);
    exit;
}

// Normalize contact
$is_email = filter_var($contact, FILTER_VALIDATE_EMAIL) !== false;
$normalized = $contact;

// Validate OTP format
if(!preg_match('/^\d{6}$/', $otp)){
    echo json_encode(['status'=>'error','message'=>'Invalid OTP format']);
    exit;
}

// Find OTP row
$stmt = $conn->prepare("SELECT id, email, phone, otp, expires_at, used FROM otp_verifications WHERE (email = ? OR phone = ?) AND otp = ? ORDER BY id DESC LIMIT 1");
$stmt->bind_param('sss', $normalized, $normalized, $otp);
$stmt->execute();
$res = $stmt->get_result();
if($res->num_rows === 0){
    echo json_encode(['status'=>'error','message'=>'OTP not found']);
    exit;
}
$row = $res->fetch_assoc();

// Check expiry and used
if($row['used'] == 1){
    echo json_encode(['status'=>'error','message'=>'This OTP has already been used.']);
    exit;
}
if(strtotime($row['expires_at']) < time()){
    echo json_encode(['status'=>'error','message'=>'This OTP has expired.']);
    exit;
}

// Mark used
$up = $conn->prepare("UPDATE otp_verifications SET used = 1 WHERE id = ?");
$up->bind_param('i', $row['id']);
$up->execute();

// Lookup alumni by email or contact
if($is_email){
    $q = $conn->prepare("SELECT * FROM alumni_ids WHERE email = ? LIMIT 1");
    $q->bind_param('s', $normalized);
} else {
    $q = $conn->prepare("SELECT * FROM alumni_ids WHERE contact_no = ? LIMIT 1");
    $q->bind_param('s', $normalized);
}
$q->execute();
$alres = $q->get_result();
if($alres->num_rows === 0){
    // OTP verified but no alumni record - still return success so user can continue but no prefill
    echo json_encode(['status'=>'success','message'=>'OTP verified','data'=>null]);
    exit;
}
$adata = $alres->fetch_assoc();

// fetch course name
$course_name = '';
if(!empty($adata['course_id'])){
    $cs = $conn->prepare('SELECT course FROM courses WHERE id = ? LIMIT 1');
    $cs->bind_param('i', $adata['course_id']);
    $cs->execute();
    $cres = $cs->get_result();
    if($cres && $cres->num_rows) $course_name = $cres->fetch_assoc()['course'];
}

// fetch majors
$majors = [];
if(!empty($adata['course_id'])){
    $ms = $conn->prepare('SELECT id, major, about FROM majors WHERE course_id = ? ORDER BY major ASC');
    $ms->bind_param('i', $adata['course_id']);
    $ms->execute();
    $mres = $ms->get_result();
    while($mrow = $mres->fetch_assoc()) $majors[] = $mrow;
}

// prepare return data matching signup.js expectations
$ret = [
    'alumni_id' => $adata['alumni_id'],
    'lastname' => $adata['lastname'],
    'firstname' => $adata['firstname'],
    'middlename' => $adata['middlename'] ?? '',
    'suffixname' => $adata['suffixname'] ?? '',
    'birthdate' => date('Y-m-d', strtotime($adata['birthdate'])),
    'gender' => $adata['gender'] ?? '',
    'batch' => $adata['batch'] ?? '',
    'course_id' => $adata['course_id'] ?? null,
    'course_name' => $course_name,
    'majors' => $majors,
    'major_id' => $adata['major_id'] ?? null,
    'program_type' => $adata['program_type'] ?? '',
    'strand_id' => $adata['strand_id'] ?? null
];

// set session markers
$_SESSION['signup_verified_contact'] = $normalized;
$_SESSION['signup_verified_alumni_id'] = $adata['alumni_id'];

log_debug("contact=$normalized otp=$otp alumni_id=".$adata['alumni_id']);

echo json_encode(['status'=>'success','message'=>'OTP verified','data'=>$ret]);
exit;

?>
