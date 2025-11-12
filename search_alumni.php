<?php
include 'admin/db_connect.php';

$name = $_POST['name'] ?? '';
$course = $_POST['course'] ?? '';
$batch = $_POST['batch'] ?? '';

$where = [];
$params = [];
$types = '';

if ($name) {
    $where[] = "(a.firstname LIKE ? OR a.lastname LIKE ?)";
    $params[] = "%$name%";
    $params[] = "%$name%";
    $types .= 'ss';
}
if ($course) {
    $where[] = "a.course_id = ?";
    $params[] = $course;
    $types .= 'i';
}
if ($batch) {
    $where[] = "a.batch = ?";
    $params[] = $batch;
    $types .= 's';
}

$sql = "SELECT a.id, a.firstname, a.lastname, a.img, c.course as course_name, a.batch
        FROM alumnus_bio a
        LEFT JOIN courses c ON a.course_id = c.id";
if ($where) $sql .= " WHERE " . implode(' AND ', $where);
$sql .= " ORDER BY a.lastname, a.firstname";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows) {
    echo '<div class="alumni-grid">';
    while ($mate = $res->fetch_assoc()) {
        echo '<div class="alumni-card">';
        echo '<img src="uploads/' . htmlspecialchars($mate['img'] ?? 'default_avatar.jpg') . '" alt="Profile">';
        echo '<div class="name">' . htmlspecialchars($mate['firstname'] . ' ' . $mate['lastname']) . '</div>';
        echo '<div class="course">' . htmlspecialchars($mate['course_name']) . '</div>';
        echo '<div class="batch">Batch ' . htmlspecialchars($mate['batch']) . '</div>';
    echo '<a href="view_profile.php?id=' . $mate['id'] . '" class="view-profile">View Profile</a>';
        echo '</div>';
    }
    echo '</div>';
} else {
    echo '<div class="bio">No alumni found.</div>';
}
?>