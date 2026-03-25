<?php
session_start();
include '../admin/db_connect.php';

// Check if user is logged in and is registrar
if (!isset($_SESSION['login_id']) || $_SESSION['login_type'] != 4) {
    echo "N/A";
    exit();
}

if(isset($_POST['course_id'])) {
    $course_id = intval($_POST['course_id']);
    
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
