<?php
session_start();
include '../admin/db_connect.php';

header('Content-Type: application/json');

if(!isset($_SESSION['login_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login to join events']);
    exit;
}

$event_id = $_POST['event_id'] ?? 0;
$user_id = $_SESSION['login_id'];

// Check if already joined
$check = $conn->query("SELECT id FROM event_commits WHERE event_id = $event_id AND user_id = $user_id");
if($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You have already joined this event']);
    exit;
}

// Add new commitment
$insert = $conn->query("INSERT INTO event_commits (event_id, user_id) VALUES ($event_id, $user_id)");

if($insert) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to join event']);
}