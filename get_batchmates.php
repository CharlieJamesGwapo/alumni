<?php
session_start();
include 'admin/db_connect.php';

if (!isset($_SESSION['login_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$batch = $_GET['batch'] ?? '';

if (!$batch) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing batch parameter']);
    exit;
}

$userId = $_SESSION['login_id'];

$stmt = $conn->prepare("
    SELECT a.id, a.alumni_id, a.firstname, a.lastname, 
           a.batch, a.course_id, a.img, a.company_address, 
           a.email, a.contact_no, c.course as course_name
    FROM alumni a
    LEFT JOIN courses c ON a.course_id = c.id
    WHERE a.batch = ? AND a.id != ?
    ORDER BY a.course_id, a.lastname, a.firstname
");

$stmt->bind_param("si", $batch, $userId);
$stmt->execute();
$result = $stmt->get_result();

$batchmates = [];
while ($row = $result->fetch_assoc()) {
    $batchmates[] = $row;
}

echo json_encode($batchmates);

$stmt->close();
$conn->close();
