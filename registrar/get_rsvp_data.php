<?php
session_start();
require_once '../admin/db_connect.php';
header('Content-Type: application/json');

// Only allow Registrar (type = 4)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

try {
    // Get RSVP summary counts
    $summaryQuery = "
        SELECT 
            COUNT(CASE WHEN hr.response = 'attending' THEN 1 END) as attending,
            COUNT(CASE WHEN hr.response = 'not_attending' THEN 1 END) as not_attending,
            COUNT(CASE WHEN hr.response IS NULL THEN 1 END) as pending,
            COUNT(*) as total
        FROM homecoming_rsvp hr
    ";
    
    $summaryResult = $conn->query($summaryQuery);
    $summary = $summaryResult->fetch_assoc();
    
    // Get detailed RSVP data
    $dataQuery = "
        SELECT 
            hr.*,
            ab.firstname,
            ab.lastname,
            ab.email,
            c.course
        FROM homecoming_rsvp hr
        JOIN alumnus_bio ab ON hr.alumni_id = ab.id
        LEFT JOIN courses c ON ab.course_id = c.id
        ORDER BY 
            CASE 
                WHEN hr.response = 'attending' THEN 1
                WHEN hr.response = 'not_attending' THEN 2
                ELSE 3
            END,
            hr.responded_at DESC,
            hr.created_at DESC
    ";
    
    $dataResult = $conn->query($dataQuery);
    $data = [];
    
    while ($row = $dataResult->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode([
        'status' => 'success',
        'summary' => $summary,
        'data' => $data
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Failed to fetch RSVP data: ' . $e->getMessage()
    ]);
}
?>
