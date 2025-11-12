<?php
session_start();
include '../admin/db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in and is a registrar
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if(isset($_GET['alumni_id']) || isset($_GET['id'])) {
    // allow either the human-facing alumni_id string or the numeric internal id
    $use_numeric = false;
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $use_numeric = true;
        $param = intval($_GET['id']);
        $query = "SELECT ab.*, c.course, s.name AS strand_name, ai.program_type FROM alumnus_bio ab LEFT JOIN courses c ON ab.course_id = c.id LEFT JOIN strands s ON ab.strand_id = s.id LEFT JOIN alumni_ids ai ON ab.id = ai.alumni_id WHERE ab.id = ? LIMIT 1";
    } else {
        $param = $conn->real_escape_string($_GET['alumni_id']);
        $query = "SELECT ab.*, c.course, s.name AS strand_name, ai.program_type FROM alumnus_bio ab LEFT JOIN courses c ON ab.course_id = c.id LEFT JOIN strands s ON ab.strand_id = s.id LEFT JOIN alumni_ids ai ON ab.id = ai.alumni_id WHERE ab.alumni_id = ? LIMIT 1";
    }

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
        exit();
    }
    if ($use_numeric) $stmt->bind_param("i", $param); else $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result && $result->num_rows > 0) {
        $data = $result->fetch_assoc();

        // Format the name
        $fullname = trim(
            ($data['firstname'] ?? '') . ' ' . 
            (!empty($data['middlename']) ? ($data['middlename'] . ' ') : '') . 
            ($data['lastname'] ?? '') . 
            (!empty($data['suffixname']) ? (' ' . $data['suffixname']) : '')
        );

        // Resolve image URL if file exists, else provide an SVG data-URL placeholder
        $imgUrl = '';
        // prefer avatar, then img
        $fileCandidate = $data['avatar'] ?? $data['img'] ?? '';
        if (!empty($fileCandidate)) {
            $candidates = [
                __DIR__ . '/../admin/assets/uploads/' . $fileCandidate,
                __DIR__ . '/../assets/uploads/' . $fileCandidate,
                __DIR__ . '/uploads/' . $fileCandidate,
                __DIR__ . '/../assets/img/' . $fileCandidate,
                __DIR__ . '/../uploads/' . $fileCandidate
            ];
            foreach ($candidates as $p) {
                if (is_file($p) && file_exists($p)) {
                    // create a web-friendly relative path starting with ../
                    $rel = str_replace('\\', '/', str_replace(__DIR__, '', $p));
                    // remove any leading slashes
                    $web = preg_replace('#^/+#', '', $rel);
                    // ensure it starts with ../ so it's relative to registrar folder
                    if (strpos($web, '../') !== 0) {
                        $web = ltrim($web, '/');
                        $web = '../' . $web;
                    }
                    $imgUrl = $web;
                    break;
                }
            }
            if (empty($imgUrl) && (stripos($fileCandidate, 'http://') === 0 || stripos($fileCandidate, 'https://') === 0)) {
                $imgUrl = $fileCandidate;
            }
        }

        if (empty($imgUrl)) {
            // SVG placeholder (simple gray square with person silhouette text)
            $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200"><rect width="100%" height="100%" fill="#e9e9e9"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#888" font-family="Arial, Helvetica, sans-serif" font-size="20">No Image</text></svg>';
            $imgUrl = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
        }

        $response = [
            'success' => true,
            'data' => [
                'name' => $fullname,
                'alumni_id' => $data['alumni_id'] ?? '',
                'course' => $data['course'] ?? '',
                'strand' => $data['strand_name'] ?? '',
                'batch' => $data['batch'] ?? '',
                'birthdate' => $data['birthdate'] ?? '',
                'gender' => $data['gender'] ?? '',
                'contact' => $data['contact_no'] ?? '',
                'email' => $data['email'] ?? '',
                'address' => $data['address'] ?? '',
                'company_name' => $data['company_name'] ?? '',
                'company_address' => $data['company_address'] ?? '',
                'connected_to' => $data['connected_to'] ?? '',
                'industry' => $data['connected_to'] ?? '',
                'img' => $imgUrl,
                'status' => $data['status'] ?? 0
            ]
        ];
    } else {
        $response = ['success' => false, 'message' => 'Alumni not found'];
    }
    
    echo json_encode($response);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Alumni ID is required']);
}
?>
