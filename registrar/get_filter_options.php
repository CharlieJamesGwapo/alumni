<?php
include '../admin/db_connect.php';

$type = $_POST['type'] ?? 'college';
$options = [];

if($type == 'college') {
    $sql = "SELECT DISTINCT c.course as name 
            FROM courses c 
            INNER JOIN alumnus_bio ab ON c.id = ab.course_id 
            WHERE ab.strand_id IS NULL 
            ORDER BY c.course";
} else {
    $sql = "SELECT DISTINCT s.name 
            FROM strands s 
            INNER JOIN alumnus_bio ab ON s.id = ab.strand_id 
            ORDER BY s.name";
}

$result = $conn->query($sql);
while($row = $result->fetch_assoc()) {
    $options[] = $row;
}

echo json_encode($options);