<?php
include '../admin/db_connect.php';

// Initialize response array
$response = array(
    'total' => 0,
    'validated' => 0,
    'notValidated' => 0,
    'courses' => array()
);

// Get total counts
$query = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as validated,
    SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as notValidated
FROM alumnus_bio";

$result = $conn->query($query);
if ($row = $result->fetch_assoc()) {
    $response['total'] = (int)$row['total'];
    $response['validated'] = (int)$row['validated'];
    $response['notValidated'] = (int)$row['notValidated'];
}

// Get course distribution
$query = "SELECT course, COUNT(*) as count 
FROM alumnus_bio 
GROUP BY course 
ORDER BY count DESC";

$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $response['courses'][$row['course']] = (int)$row['count'];
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?>
