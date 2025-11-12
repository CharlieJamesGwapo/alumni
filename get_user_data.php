<?php
header('Content-Type: application/json');
session_start();

try {
    // Check authentication
    if (!isset($_SESSION['login_id'])) {
        throw new Exception('Not authenticated');
    }

    // Include database connection
    include 'admin/db_connect.php';

    // Check database connection
    if ($conn->connect_error) {
        throw new Exception('Database connection failed');
    }

    $userId = $_SESSION['login_id'];

    // Prepare and execute alumni query
    $stmt = $conn->prepare("SELECT a.*, c.course as course_name 
                           FROM alumni a 
                           LEFT JOIN courses c ON a.course_id = c.id 
                           WHERE a.id = ?");
    
    if (!$stmt) {
        throw new Exception('Failed to prepare alumni query');
    }

    $stmt->bind_param("i", $userId);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to execute alumni query');
    }

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if (!$user) {
        throw new Exception('User not found');
    }

    // Clean sensitive data before sending
    unset($user['password']);
    unset($user['reset_code']);
    
    // Add additional user stats
    $stats = [
        'posts' => 0,
        'followers' => 0,
        'following' => 0
    ];
    
    // Merge stats with user data
    $user = array_merge($user, ['stats' => $stats]);

    echo json_encode([
        'status' => 'success',
        'data' => $user
    ]);

} catch (Exception $e) {
    http_response_code($e->getMessage() === 'Not authenticated' ? 401 : 500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
} finally {
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conn)) {
        $conn->close();
    }
}
