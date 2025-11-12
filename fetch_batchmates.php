<?php
include 'admin/db_connect.php';
$batch = $_POST['batch'] ?? '';
$exclude = $_POST['exclude'] ?? '';

$stmt = $conn->prepare("
    SELECT a.id, a.firstname, a.lastname, a.img, c.course as course_name, a.batch
    FROM alumnus_bio a
    LEFT JOIN courses c ON a.course_id = c.id
    WHERE a.batch = ? AND a.id != ?
    ORDER BY a.lastname, a.firstname
");
$stmt->bind_param("si", $batch, $exclude);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows) {
    while ($mate = $res->fetch_assoc()) {
        echo '<div class="alumni-card">';
        echo '<img src="uploads/' . htmlspecialchars($mate['img'] ?? 'default_avatar.jpg') . '" alt="Profile">';
        echo '<div class="name">' . htmlspecialchars($mate['firstname'] . ' ' . $mate['lastname']) . '</div>';
        echo '<div class="course">' . htmlspecialchars($mate['course_name']) . '</div>';
        echo '<div class="batch">Batch ' . htmlspecialchars($mate['batch']) . '</div>';
    echo '<a href="view_profile.php?id=' . $mate['id'] . '" class="view-profile">View Profile</a>';
        echo '</div>';
    }
} else {
    echo '<div class="bio">No batchmates found.</div>';
}
?>