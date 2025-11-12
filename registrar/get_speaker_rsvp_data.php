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
    // Check if speaker_rsvp table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'speaker_rsvp'");
    if ($tableCheck->num_rows == 0) {
        echo json_encode([
            'status' => 'success',
            'summary' => [
                'accept' => 0,
                'decline' => 0,
                'pending' => 0,
                'total' => 0
            ],
            'data' => []
        ]);
        exit();
    }
    
    // Get RSVP summary counts
    $summaryQuery = "
        SELECT 
            COUNT(CASE WHEN sr.response = 'accept' THEN 1 END) as accept,
            COUNT(CASE WHEN sr.response = 'decline' THEN 1 END) as decline,
            COUNT(CASE WHEN sr.response = 'pending' THEN 1 END) as pending,
            COUNT(*) as total
        FROM speaker_rsvp sr
    ";
    
    $summaryResult = $conn->query($summaryQuery);
    $summary = $summaryResult->fetch_assoc();
    
    // Get detailed RSVP data
    $dataQuery = "
        SELECT 
            sr.*,
            ab.firstname,
            ab.lastname,
            ab.email,
            ab.batch,
            c.course
        FROM speaker_rsvp sr
        JOIN alumnus_bio ab ON sr.alumni_id = ab.id
        LEFT JOIN courses c ON ab.course_id = c.id
        ORDER BY 
            CASE 
                WHEN sr.response = 'accept' THEN 1
                WHEN sr.response = 'decline' THEN 2
                ELSE 3
            END,
            sr.updated_at DESC,
            sr.created_at DESC
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
        'message' => 'Failed to fetch speaker RSVP data: ' . $e->getMessage()
    ]);
}
?>
