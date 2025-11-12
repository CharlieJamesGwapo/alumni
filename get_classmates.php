<?php
session_start();
include 'admin/db_connect.php';

if (!isset($_SESSION['login_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$courseId = $_GET['course_id'] ?? '';
$batch = $_GET['batch'] ?? '';

if (!$courseId || !$batch) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$userId = $_SESSION['login_id'];

$stmt = $conn->prepare("
    SELECT id, alumni_id, firstname, lastname, batch, course_id, 
           img, company_address, email, contact_no 
    FROM alumni 
    WHERE course_id = ? AND batch = ? AND id != ?
    ORDER BY lastname, firstname
");

$stmt->bind_param("isi", $courseId, $batch, $userId);
$stmt->execute();
$result = $stmt->get_result();

$classmates = [];
while ($row = $result->fetch_assoc()) {
    // Get course name for each classmate
    $courseStmt = $conn->prepare("SELECT course FROM courses WHERE id = ?");
    $courseStmt->bind_param("i", $row['course_id']);
    $courseStmt->execute();
    $courseResult = $courseStmt->get_result();
    $course = $courseResult->fetch_assoc();
    
    $row['course_name'] = $course ? $course['course'] : 'Unknown Course';
    $classmates[] = $row;
}

echo json_encode($classmates);

$stmt->close();
$conn->close();
