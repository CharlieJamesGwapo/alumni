<?php
session_start();
include '../admin/db_connect.php';

header('Content-Type: application/json');

if(!isset($_SESSION['login_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to join events']);
    exit;
}

$event_id = intval($_POST['event_id'] ?? 0);
$user_id = intval($_SESSION['login_id']);

// Check if already joined
$check_stmt = $conn->prepare("SELECT id FROM event_commits WHERE event_id = ? AND user_id = ?");
$check_stmt->bind_param("ii", $event_id, $user_id);
$check_stmt->execute();
$check = $check_stmt->get_result();
if($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already joined this event']);
    $check_stmt->close();
    exit;
}
$check_stmt->close();

// Add new commitment
$insert_stmt = $conn->prepare("INSERT INTO event_commits (event_id, user_id) VALUES (?, ?)");
$insert_stmt->bind_param("ii", $event_id, $user_id);
$insert = $insert_stmt->execute();
$insert_stmt->close();

if($insert) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to join event']);
}