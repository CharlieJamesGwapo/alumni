<?php
session_start();
include '../admin/db_connect.php';
header('Content-Type: application/json');

// Restrict access to only Registrar (type = 4)
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$rows = [];
$query = "SELECT a.*, c.course AS course_name, u.username AS archived_by_name
          FROM archive_alumni a
          LEFT JOIN courses c ON a.course_id = c.id
          LEFT JOIN users u ON a.archived_by = u.id
          ORDER BY a.archived_date DESC";
$res = $conn->query($query);
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $rows[] = $r;
    }
}

echo json_encode(['status' => 'success', 'data' => $rows]);
exit();
?>
