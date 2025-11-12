<?php
session_start();
include '../admin/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['login_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$batch = $_GET['batch'] ?? '';
$userId = $_SESSION['login_id'];

try {
    if (empty($batch)) {
        throw new Exception('Batch year is required');
    }
    
    // Get batchmates (same batch year, excluding current user)
    $stmt = $conn->prepare("
        SELECT a.id, a.firstname, a.lastname, a.img, a.batch,
               c.course as course_name, s.strand as strand_name
        FROM alumnus_bio a
        LEFT JOIN courses c ON a.course_id = c.id
        LEFT JOIN strands s ON a.strand_id = s.id
        WHERE a.batch = ? AND a.id != ? AND a.status = 1
        ORDER BY a.firstname ASC, a.lastname ASC
    ");
    
    $stmt->bind_param("si", $batch, $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $batchmates = [];
    while ($row = $result->fetch_assoc()) {
        $batchmates[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $batchmates,
        'total' => count($batchmates)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
