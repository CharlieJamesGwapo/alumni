<?php
session_start();
include '../admin/db_connect.php';

// Check if user is logged in and is registrar
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    echo "N/A";
    exit();
}

if(isset($_POST['course_id'])) {
    $course_id = $conn->real_escape_string($_POST['course_id']);
    
    $query = "SELECT course FROM courses WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($row = $result->fetch_assoc()) {
        echo $row['course'];
    } else {
        echo "N/A";
    }
    $stmt->close();
} else {
    echo "N/A";
}

$conn->close();
