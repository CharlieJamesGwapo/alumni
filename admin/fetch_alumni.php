<?php
include_once __DIR__ . '/db_connect.php';
header('Content-Type: application/json; charset=utf-8');
if (!isset($conn) || !$conn) {
    echo json_encode([]);
    exit;
}

// choose source: ?source=archive will fetch archived rows
$source = (isset($_GET['source']) && $_GET['source'] === 'archive') ? 'alumnus_bio_archive' : 'alumnus_bio';

// compute upload URL robustly
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$admin_dir = dirname($_SERVER['SCRIPT_NAME']); // e.g. /alumni/admin
$project_dir = dirname($admin_dir);            // e.g. /alumni
$upload_url = $scheme . '://' . $host . rtrim($project_dir, '/') . '/uploads/';

// filesystem uploads dir - check multiple possible locations
$upload_dir = realpath(__DIR__ . '/../uploads');
if ($upload_dir === false) {
    $upload_dir = realpath(__DIR__ . '/../assets/uploads');
}
if ($upload_dir === false) {
    $upload_dir = __DIR__ . '/../uploads';
}
$upload_dir = rtrim($upload_dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

$sql = "SELECT a.*, c.*, s.*, m.*,
           CONCAT_WS(' ', a.firstname, a.middlename, a.lastname) as fullname
        FROM {$source} a
        LEFT JOIN courses c ON c.id = a.course_id
        LEFT JOIN strands s ON s.id = a.strand_id
        LEFT JOIN majors m ON m.id = a.major_id
        ORDER BY a.lastname ASC, a.firstname ASC";

$res = $conn->query($sql);
$data = [];

// Check for default image in multiple locations
$default_img_candidates = [
    $upload_url . '1602813060_no-image-available.png',
    $scheme . '://' . $host . rtrim($project_dir, '/') . '/assets/uploads/1602813060_no-image-available.png',
    $scheme . '://' . $host . rtrim($project_dir, '/') . '/admin/assets/uploads/1602813060_no-image-available.png'
];

$default_img = $default_img_candidates[0]; // Use first as default
foreach ($default_img_candidates as $candidate) {
    $local_path = str_replace($scheme . '://' . $host, $_SERVER['DOCUMENT_ROOT'], $candidate);
    if (file_exists($local_path)) {
        $default_img = $candidate;
        break;
    }
}

if ($res) {
    while ($row = $res->fetch_assoc()) {
        // resolve image filename (img then avatar), ensure only basename used
        $img_url = $default_img;
        if (!empty($row['img'])) {
            $bn = basename($row['img']);
            $candidate = $upload_dir . $bn;
            if (file_exists($candidate) && is_file($candidate)) {
                $img_url = $upload_url . rawurlencode($bn);
            }
        }
        if ($img_url === $default_img && !empty($row['avatar'])) {
            $bn = basename($row['avatar']);
            $candidate = $upload_dir . $bn;
            if (file_exists($candidate) && is_file($candidate)) {
                $img_url = $upload_url . rawurlencode($bn);
            }
        }

        // resolve course/strand/major
        $course_val = '';
        foreach (['course','course_name','title','name'] as $k) { if (!empty($row[$k])) { $course_val = trim($row[$k]); break; } }
        $strand_val = '';
        foreach (['strand','strand_name'] as $k) { if (!empty($row[$k])) { $strand_val = trim($row[$k]); break; } }
        $major_val = '';
        foreach (['major','major_name'] as $k) { if (!empty($row[$k])) { $major_val = trim($row[$k]); break; } }
        $course_strand = '';
        if ($course_val !== '') {
            $course_strand = $course_val;
            if ($strand_val !== '') $course_strand .= ' / ' . $strand_val;
            elseif ($major_val !== '') $course_strand .= ' / ' . $major_val;
        } else {
            if ($strand_val !== '') $course_strand = $strand_val;
            elseif ($major_val !== '') $course_strand = $major_val;
            else $course_strand = 'N/A';
        }

        $data[] = [
            'id' => (int)$row['id'],
            'fullname' => trim($row['fullname'] ?: ($row['firstname'].' '.$row['lastname'])),
            'alumni_id' => $row['alumni_id'] ?? '',
            'course_strand' => $course_strand,
            'batch' => $row['batch'] ?? '',
            'email' => $row['email'] ?? $row['company_email'] ?? '',
            'status' => isset($row['status']) ? (int)$row['status'] : 0,
            'img' => $img_url
        ];
    }
}

echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
exit;