<?php
session_start();
include 'admin/db_connect.php';

if (!isset($_SESSION['login_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$query = "SELECT id, course FROM courses ORDER BY course";
$result = $conn->query($query);

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode($courses);

$conn->close();
