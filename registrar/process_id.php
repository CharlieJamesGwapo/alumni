<?php
session_start();
include '../admin/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 4) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'get_alumni':
            $alumni_id = $conn->real_escape_string($_GET['alumni_id']);
            
            $query = "SELECT ab.*, c.course FROM alumnus_bio ab 
                     LEFT JOIN courses c ON ab.course_id = c.id 
                     WHERE ab.alumni_id = ?";
            
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
                exit();
            }
            
            $stmt->bind_param("s", $alumni_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
                
                // Format the name properly
                $fullname = trim($data['firstname'] . ' ' . 
                            ($data['middlename'] ? $data['middlename'] . ' ' : '') . 
                            $data['lastname'] . 
                            ($data['suffix'] ? ' ' . $data['suffix'] : ''));
                
                $response = [
                    'success' => true,
                    'data' => [
                        'name' => $fullname,
                        'alumni_id' => $data['alumni_id'],
                        'course' => $data['course'],
                        'batch' => $data['batch'],
                        'email' => $data['email'],
                        'contact' => $data['contact'],
                        'address' => $data['address'],
                        'avatar' => $data['avatar'],
                        'birthdate' => date('F j, Y', strtotime($data['birthdate'])),
                        'status' => $data['status']
                    ]
                ];
            } else {
                $response = ['success' => false, 'message' => 'Alumni not found'];
            }
            
            echo json_encode($response);
            break;
            
        case 'save_id':
            // Save the generated ID to database
            $alumni_id = $conn->real_escape_string($_POST['alumni_id']);
            $valid_until = $conn->real_escape_string($_POST['valid_until']);
            
            $query = "INSERT INTO alumni_ids (alumni_id, date_generated, valid_until, generated_by) 
                      VALUES (?, NOW(), ?, ?)";
            
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssi", $alumni_id, $valid_until, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'ID generated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error saving ID details']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
}
?>
