<?php
session_start();
include '../admin/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['login_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$name = $_GET['name'] ?? '';
$course = $_GET['course'] ?? '';
$batch = $_GET['batch'] ?? '';
$offset = intval($_GET['offset'] ?? 0);
$limit = intval($_GET['limit'] ?? 12);
$userId = $_SESSION['login_id'];

try {
    // Build query
    $where = ["a.id != ?", "a.status = 1"];
    $params = [$userId];
    $types = "i";
    
    if (!empty($name)) {
        $where[] = "(CONCAT(a.firstname, ' ', a.lastname) LIKE ? OR CONCAT(a.lastname, ' ', a.firstname) LIKE ?)";
        $searchName = "%{$name}%";
        $params[] = $searchName;
        $params[] = $searchName;
        $types .= "ss";
    }
    
    if (!empty($course)) {
        $where[] = "a.course_id = ?";
        $params[] = $course;
        $types .= "i";
    }
    
    if (!empty($batch)) {
        $where[] = "a.batch = ?";
        $params[] = $batch;
        $types .= "s";
    }
    
    $whereClause = implode(" AND ", $where);
    
    // Get total count
    $countSql = "
        SELECT COUNT(*) as total
        FROM alumnus_bio a
        WHERE {$whereClause}
    ";
    
    $countStmt = $conn->prepare($countSql);
    $countStmt->bind_param($types, ...$params);
    $countStmt->execute();
    $totalResult = $countStmt->get_result();
    $total = $totalResult->fetch_assoc()['total'];
    
    // Get results with pagination
    $sql = "
        SELECT a.id, a.firstname, a.lastname, a.img, a.batch,
               c.course as course_name, s.strand as strand_name
        FROM alumnus_bio a
        LEFT JOIN courses c ON a.course_id = c.id
        LEFT JOIN strands s ON a.strand_id = s.id
        WHERE {$whereClause}
        ORDER BY a.firstname ASC, a.lastname ASC
        LIMIT ? OFFSET ?
    ";
    
    $stmt = $conn->prepare($sql);
    $params[] = $limit;
    $params[] = $offset;
    $types .= "ii";
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $alumni = [];
    while ($row = $result->fetch_assoc()) {
        $alumni[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $alumni,
        'total' => $total,
        'offset' => $offset,
        'limit' => $limit
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
