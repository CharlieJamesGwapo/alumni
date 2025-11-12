<?php
session_start();
include 'db_connect.php';
include 'log_activity.php';

if(!isset($_SESSION['login_id'])) {
    header('location:login.php');
    exit;
}

// Get export parameters
$format = $_GET['format'] ?? 'csv';
$user_type = $_GET['user_type'] ?? null;
$action_type = $_GET['action_type'] ?? null;
$start_date = $_GET['start_date'] ?? null;
$end_date = $_GET['end_date'] ?? null;
$limit = $_GET['limit'] ?? 1000;

// Get filtered logs
$logs = get_activity_logs(null, $start_date, $end_date, $user_type, $action_type, $limit);

// Log the export action
log_activity($_SESSION['login_id'], 'EXPORT_LOGS', "Exported $format format with " . count($logs) . " records");

if ($format === 'csv') {
    // CSV Export
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="activity_logs_' . date('Y-m-d_H-i-s') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // CSV Headers
    fputcsv($output, [
        'ID', 'Timestamp', 'User ID', 'User Name', 'User Type', 
        'Action Type', 'Details', 'Target ID', 'Target Type', 'IP Address'
    ]);
    
    // CSV Data
    foreach ($logs as $log) {
        fputcsv($output, [
            $log['id'],
            $log['timestamp'],
            $log['user_id'],
            $log['user_name'],
            $log['user_type'],
            $log['action_type'],
            $log['details'],
            $log['target_id'] ?? '',
            $log['target_type'] ?? '',
            $log['ip_address']
        ]);
    }
    
    fclose($output);
    
} elseif ($format === 'json') {
    // JSON Export
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="activity_logs_' . date('Y-m-d_H-i-s') . '.json"');
    
    echo json_encode([
        'export_info' => [
            'exported_at' => date('Y-m-d H:i:s'),
            'exported_by' => $_SESSION['login_name'],
            'total_records' => count($logs),
            'filters' => [
                'user_type' => $user_type,
                'action_type' => $action_type,
                'start_date' => $start_date,
                'end_date' => $end_date
            ]
        ],
        'logs' => $logs
    ], JSON_PRETTY_PRINT);
    
} else {
    // Invalid format
    header('HTTP/1.1 400 Bad Request');
    echo 'Invalid export format';
}
?>
