<?php
session_start();
include 'admin/db_connect.php';

header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_POST['alumni_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Alumni ID is required']);
    exit;
}

try {
    $alumni_id = trim($_POST['alumni_id']);
    
    // First check if the ID exists in alumni_ids
    $check_exists = "SELECT * FROM alumni_ids WHERE alumni_id = ? LIMIT 1";
    $exists_stmt = $conn->prepare($check_exists);
    $exists_stmt->bind_param("s", $alumni_id);
    $exists_stmt->execute();
    $exists_result = $exists_stmt->get_result();
    
    if ($exists_result->num_rows == 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid Alumni ID. Please check your ID and try again.'
        ]);
        exit;
    }
    
    // Then check if this ID is already registered
    $check_registered = "SELECT id FROM alumnus_bio WHERE alumni_id = ? LIMIT 1";
    $reg_stmt = $conn->prepare($check_registered);
    $reg_stmt->bind_param("s", $alumni_id);
    $reg_stmt->execute();
    $reg_result = $reg_stmt->get_result();
    
    if ($reg_result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'This Alumni ID is already registered in the system'
        ]);
        exit;
    }
    
    // Get the alumni data from alumni_ids
    $alumni_data = $exists_result->fetch_assoc();
        
    // Format the birthdate to Y-m-d format for the date input
    $birthdate = date('Y-m-d', strtotime($alumni_data['birthdate']));

    // Fetch course name for the returned course_id (if any)
    $course_name = '';
    if (!empty($alumni_data['course_id'])) {
        $cstmt = $conn->prepare("SELECT course FROM courses WHERE id = ? LIMIT 1");
        if ($cstmt) {
            $cstmt->bind_param('i', $alumni_data['course_id']);
            $cstmt->execute();
            $cres = $cstmt->get_result();
            if ($cres && $cres->num_rows > 0) {
                $crow = $cres->fetch_assoc();
                $course_name = $crow['course'];
            }
            $cstmt->close();
        }
    }

    // Fetch majors for the course (if any) and return them as an array
    $majors = [];
    if (!empty($alumni_data['course_id'])) {
        $mstmt = $conn->prepare("SELECT id, major, about FROM majors WHERE course_id = ? ORDER BY major ASC");
        if ($mstmt) {
            $mstmt->bind_param('i', $alumni_data['course_id']);
            $mstmt->execute();
            $mres = $mstmt->get_result();
            if ($mres) {
                while ($mrow = $mres->fetch_assoc()) {
                    $majors[] = [
                        'id' => $mrow['id'],
                        'major' => $mrow['major'],
                        'about' => $mrow['about']
                    ];
                }
            }
            $mstmt->close();
        }
    }

    // If alumni_ids contains a major_id, fetch that specific major
    $selected_major = null;
    if (!empty($alumni_data['major_id'])) {
        $smstmt = $conn->prepare("SELECT id, major, about FROM majors WHERE id = ? LIMIT 1");
        if ($smstmt) {
            $smstmt->bind_param('i', $alumni_data['major_id']);
            $smstmt->execute();
            $smres = $smstmt->get_result();
            if ($smres && $smres->num_rows > 0) {
                $smrow = $smres->fetch_assoc();
                $selected_major = [
                    'id' => $smrow['id'],
                    'major' => $smrow['major'],
                    'about' => $smrow['about']
                ];
            }
            $smstmt->close();
        }
    }
        
    // Return success with alumni data
    echo json_encode([
        'status' => 'success',
        'message' => 'Alumni ID verified successfully',
        'data' => [
            'lastname' => $alumni_data['lastname'],
            'firstname' => $alumni_data['firstname'],
            'middlename' => $alumni_data['middlename'] ?? '',
            'suffixname' => $alumni_data['suffixname'] ?? '',
            'birthdate' => $birthdate,
            'gender' => $alumni_data['gender'],
            'batch' => $alumni_data['batch'],
            'course_id' => $alumni_data['course_id'],
            'course_name' => $course_name,
            'majors' => $majors,
            'major_id' => $alumni_data['major_id'] ?? null,
            'selected_major' => $selected_major,
            'program_type' => $alumni_data['program_type'] ?? '',
            'strand_id' => isset($alumni_data['strand_id']) ? $alumni_data['strand_id'] : null
        ]
    ]);

} catch (Exception $e) {
    error_log("Alumni verification error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    echo json_encode([
        'status' => 'error',
        'message' => 'An error occurred during verification. Please try again later.'
    ]);
}
    if (isset($exists_stmt)) $exists_stmt->close();
    if (isset($reg_stmt)) $reg_stmt->close();
?>
