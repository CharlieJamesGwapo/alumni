<?php
// track_id_release.php - AJAX endpoint to track ID releases
session_start();
include '../admin/db_connect.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$alumni_id = intval($_POST['alumni_id'] ?? 0);
$release_method = $_POST['release_method'] ?? 'print_button';

if ($alumni_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid alumni ID']);
    exit;
}

// Validate release method
$valid_methods = ['print_button', 'ctrl_p', 'manual'];
if (!in_array($release_method, $valid_methods)) {
    $release_method = 'print_button';
}

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Update the release count in alumnus_bio
    $update_stmt = $conn->prepare("UPDATE alumnus_bio SET id_release_count = COALESCE(id_release_count, 0) + 1 WHERE id = ?");
    $update_stmt->bind_param('i', $alumni_id);
    
    if (!$update_stmt->execute()) {
        throw new Exception('Failed to update release count');
    }
    
    // Log the release in id_release_log table
    $released_by = $_SESSION['username'] ?? 'Unknown';
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    
    $log_stmt = $conn->prepare("INSERT INTO id_release_log (alumni_id, released_by, release_method, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
    $log_stmt->bind_param('issss', $alumni_id, $released_by, $release_method, $ip_address, $user_agent);
    
    if (!$log_stmt->execute()) {
        throw new Exception('Failed to log release');
    }
    
    // Get updated count
    $count_stmt = $conn->prepare("SELECT id_release_count FROM alumnus_bio WHERE id = ?");
    $count_stmt->bind_param('i', $alumni_id);
    $count_stmt->execute();
    $result = $count_stmt->get_result();
    $new_count = $result->fetch_assoc()['id_release_count'] ?? 0;
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'ID release tracked successfully',
        'new_count' => $new_count,
        'release_method' => $release_method
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    error_log("ID Release tracking error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Failed to track ID release']);
}
?>
