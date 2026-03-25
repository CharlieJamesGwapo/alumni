<?php
session_start();
require_once '../admin/db_connect.php';
header('Content-Type: application/json');

// Only allow Registrar (type = 4)
if (!isset($_SESSION['login_id']) || $_SESSION['login_type'] != 4) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

try {
    // Create homecoming_rsvp table if it doesn't exist
    $conn->query("CREATE TABLE IF NOT EXISTS homecoming_rsvp (
        id INT AUTO_INCREMENT PRIMARY KEY,
        alumni_id INT NOT NULL,
        response ENUM('attending','not_attending') DEFAULT NULL,
        message TEXT DEFAULT NULL,
        responded_at DATETIME DEFAULT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        invited_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_alumni (alumni_id),
        INDEX idx_response (response)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    // Get RSVP summary counts
    $summaryResult = $conn->query("SELECT
        COUNT(CASE WHEN response = 'attending' THEN 1 END) as attending,
        COUNT(CASE WHEN response = 'not_attending' THEN 1 END) as not_attending,
        COUNT(CASE WHEN response IS NULL THEN 1 END) as pending,
        COUNT(*) as total
        FROM homecoming_rsvp");
    $summary = $summaryResult ? $summaryResult->fetch_assoc() : ['attending'=>0,'not_attending'=>0,'pending'=>0,'total'=>0];

    // Get detailed RSVP data
    $dataResult = $conn->query("SELECT hr.*, ab.firstname, ab.lastname, ab.email, c.course
        FROM homecoming_rsvp hr
        JOIN alumnus_bio ab ON hr.alumni_id = ab.id
        LEFT JOIN courses c ON ab.course_id = c.id
        ORDER BY CASE WHEN hr.response = 'attending' THEN 1 WHEN hr.response = 'not_attending' THEN 2 ELSE 3 END,
        hr.responded_at DESC, hr.created_at DESC");

    $data = [];
    if ($dataResult) {
        while ($row = $dataResult->fetch_assoc()) $data[] = $row;
    }

    echo json_encode(['status' => 'success', 'summary' => $summary, 'data' => $data]);

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
