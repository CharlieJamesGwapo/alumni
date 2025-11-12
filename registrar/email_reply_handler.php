<?php
/**
 * Email Reply Handler
 * Captures replies from Gmail and records them
 */

session_start();
require_once '../admin/db_connect.php';

header('Content-Type: application/json');

// This endpoint can be called manually or via webhook
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'record_reply') {
    // Manual reply recording
    $message_id = intval($_POST['message_id'] ?? 0);
    $recipient_email = trim($_POST['recipient_email'] ?? '');
    $reply_content = trim($_POST['reply_content'] ?? '');
    
    if ($message_id && $recipient_email && $reply_content) {
        // Find recipient
        $stmt = $conn->prepare("SELECT ab.id, ab.firstname, ab.lastname 
                               FROM alumnus_bio ab 
                               WHERE ab.email = ?");
        $stmt->bind_param("s", $recipient_email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($recipient = $result->fetch_assoc()) {
            // Record reply in database
            $insert = $conn->prepare("INSERT INTO message_replies 
                                     (message_id, recipient_id, reply_content, replied_at) 
                                     VALUES (?, ?, ?, NOW())");
            $insert->bind_param("iis", $message_id, $recipient['id'], $reply_content);
            
            if ($insert->execute()) {
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Reply recorded successfully'
                ]);
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Failed to record reply'
                ]);
            }
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Recipient not found'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Missing required fields'
        ]);
    }
    exit();
}

// Get replies for a message
if ($action === 'get_replies') {
    $message_id = intval($_POST['message_id'] ?? $_GET['message_id'] ?? 0);
    
    if ($message_id) {
        $stmt = $conn->prepare("SELECT mr.*, ab.firstname, ab.lastname, ab.email 
                               FROM message_replies mr
                               JOIN alumnus_bio ab ON mr.recipient_id = ab.id
                               WHERE mr.message_id = ?
                               ORDER BY mr.replied_at DESC");
        $stmt->bind_param("i", $message_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $replies = [];
        while ($row = $result->fetch_assoc()) {
            $replies[] = $row;
        }
        
        echo json_encode([
            'status' => 'success',
            'replies' => $replies,
            'count' => count($replies)
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Message ID required'
        ]);
    }
    exit();
}

echo json_encode([
    'status' => 'error',
    'message' => 'Invalid action'
]);
?>
