<?php
session_start();
include 'admin/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['login_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

$userId = $_SESSION['login_id'];
$response = ['success' => false, 'message' => ''];

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid request data');
    }
    
    $currentPassword = $input['current_password'] ?? '';
    $newPassword = $input['new_password'] ?? '';
    $confirmPassword = $input['confirm_password'] ?? '';
    
    // Validate input
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        throw new Exception('All fields are required');
    }
    
    // Validate new password length
    if (strlen($newPassword) < 8) {
        throw new Exception('New password must be at least 8 characters long');
    }
    
    // Validate password match
    if ($newPassword !== $confirmPassword) {
        throw new Exception('New passwords do not match');
    }
    
    // Validate password strength
    if (!preg_match('/[A-Z]/', $newPassword)) {
        throw new Exception('Password must contain at least one uppercase letter');
    }
    
    if (!preg_match('/[a-z]/', $newPassword)) {
        throw new Exception('Password must contain at least one lowercase letter');
    }
    
    if (!preg_match('/[0-9]/', $newPassword)) {
        throw new Exception('Password must contain at least one number');
    }
    
    // Check if new password is same as current
    if ($currentPassword === $newPassword) {
        throw new Exception('New password must be different from current password');
    }
    
    // Get current password from database
    $stmt = $conn->prepare("SELECT password FROM alumnus_bio WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    // Verify current password
    // Check if password is hashed or plain text
    $isPasswordCorrect = false;
    
    if (password_verify($currentPassword, $user['password'])) {
        // Password is hashed and correct
        $isPasswordCorrect = true;
    } elseif ($currentPassword === $user['password']) {
        // Password is plain text and correct (legacy)
        $isPasswordCorrect = true;
    }
    
    if (!$isPasswordCorrect) {
        throw new Exception('Current password is incorrect');
    }
    
    // Hash the new password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    // Update password in database
    $updateStmt = $conn->prepare("UPDATE alumnus_bio SET password = ? WHERE id = ?");
    $updateStmt->bind_param("si", $hashedPassword, $userId);
    
    if ($updateStmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Password changed successfully';
        
        // Log the password change (optional)
        $logStmt = $conn->prepare("INSERT INTO activity_log (user_id, activity, created_at) VALUES (?, 'Password changed', NOW())");
        if ($logStmt) {
            $logStmt->bind_param("i", $userId);
            $logStmt->execute();
        }
    } else {
        throw new Exception('Failed to update password. Please try again.');
    }
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit;
