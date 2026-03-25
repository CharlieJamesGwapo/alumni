<?php
/**
 * Real-time Activity Notifications System
 * Provides live updates for critical system activities
 */

include 'db_connect.php';
include 'log_activity.php';

header('Content-Type: application/json');

if(!isset($_SESSION['login_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$last_check = $_GET['last_check'] ?? date('Y-m-d H:i:s', strtotime('-1 minute'));

// Get recent critical activities
$critical_actions = [
    'LOGIN_FAILED', 'DELETE_ALUMNI', 'ARCHIVE_ALUMNI', 
    'VALIDATE_ALUMNI', 'RESTORE_ALUMNI'
];

$action_filter = "'" . implode("','", $critical_actions) . "'";

$query = "SELECT * FROM activity_log 
          WHERE timestamp > ? 
          AND action_type IN ($action_filter)
          ORDER BY timestamp DESC 
          LIMIT 10";

$stmt = $conn->prepare($query);
$stmt->bind_param('s', $last_check);
$stmt->execute();
$result = $stmt->get_result();

$notifications = [];
while ($row = $result->fetch_assoc()) {
    $notifications[] = [
        'id' => $row['id'],
        'timestamp' => $row['timestamp'],
        'user_name' => $row['user_name'],
        'user_type' => $row['user_type'],
        'action_type' => $row['action_type'],
        'details' => $row['details'],
        'severity' => getSeverity($row['action_type']),
        'icon' => getIcon($row['action_type'])
    ];
}

echo json_encode([
    'notifications' => $notifications,
    'count' => count($notifications),
    'last_update' => date('Y-m-d H:i:s')
]);

function getSeverity($action) {
    $severity_map = [
        'LOGIN_FAILED' => 'warning',
        'DELETE_ALUMNI' => 'danger',
        'ARCHIVE_ALUMNI' => 'info',
        'VALIDATE_ALUMNI' => 'success',
        'RESTORE_ALUMNI' => 'primary'
    ];
    return $severity_map[$action] ?? 'secondary';
}

function getIcon($action) {
    $icon_map = [
        'LOGIN_FAILED' => 'fas fa-exclamation-triangle',
        'DELETE_ALUMNI' => 'fas fa-trash',
        'ARCHIVE_ALUMNI' => 'fas fa-archive',
        'VALIDATE_ALUMNI' => 'fas fa-check-circle',
        'RESTORE_ALUMNI' => 'fas fa-undo'
    ];
    return $icon_map[$action] ?? 'fas fa-info-circle';
}
?>
