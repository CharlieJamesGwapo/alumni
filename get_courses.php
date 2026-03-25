<?php
session_start();
include 'admin/db_connect.php';

header('Content-Type: application/json');

$query = "SELECT id, course FROM courses ORDER BY course";
$result = $conn->query($query);

$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row;
}

echo json_encode($courses);

$conn->close();
