<?php
session_start();
include 'admin/db_connect.php';

header('Content-Type: application/json');

if (!isset($_SESSION['login_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$user_id = intval($_POST['user_id'] ?? 0);

if ($user_id !== intval($_SESSION['login_id']) && $_SESSION['login_type'] !== 1) {
    echo json_encode(['success' => false, 'message' => 'Permission denied']);
    exit;
}

try {
    switch ($action) {
        case 'update_personal_info':
            $full_name = trim($_POST['full_name'] ?? '');
            $gender = $_POST['gender'] ?? '';
            $academic_program = trim($_POST['academic_program'] ?? '');
            $academic_honor = trim($_POST['academic_honor'] ?? '');

            // Parse full name
            $name_parts = explode(' ', $full_name);
            $firstname = $name_parts[0] ?? '';
            $middlename = $name_parts[1] ?? '';
            $lastname = $name_parts[2] ?? '';
            $suffixname = $name_parts[3] ?? '';

            // Update alumnus_bio table
            $stmt = $conn->prepare("UPDATE alumnus_bio SET firstname = ?, middlename = ?, lastname = ?, suffixname = ?, gender = ?, academic_honor = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $firstname, $middlename, $lastname, $suffixname, $gender, $academic_honor, $user_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Personal information updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        case 'update_work_experience':
            $employment_status = $_POST['employment_status'] ?? '';
            $company_name = trim($_POST['company_name'] ?? '');
            $company_address = trim($_POST['company_address'] ?? '');
            $company_email = trim($_POST['company_email'] ?? '');

            $stmt = $conn->prepare("UPDATE alumnus_bio SET employment_status = ?, connected_to = ?, company_address = ?, company_email = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $employment_status, $company_name, $company_address, $company_email, $user_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Work experience updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        case 'update_education':
            $degree = trim($_POST['degree'] ?? '');
            $institution = trim($_POST['institution'] ?? '');
            $graduation_year = trim($_POST['graduation_year'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // Note: Since education information is primarily stored in the courses table and linked,
            // we might need to store additional education details in a separate table
            // For now, we'll update the batch and academic_honor fields
            $stmt = $conn->prepare("UPDATE alumnus_bio SET batch = ?, academic_honor = ? WHERE id = ?");
            $stmt->bind_param("ssi", $graduation_year, $degree, $user_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Education updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        case 'update_skills':
            $skills_json = $_POST['skills'] ?? '[]';
            $skills = json_decode($skills_json, true);

            // Store skills as JSON in a custom field or create a skills table
            // For now, we'll store it in the employment_history field as JSON
            $stmt = $conn->prepare("UPDATE alumnus_bio SET employment_history = ? WHERE id = ?");
            $stmt->bind_param("si", $skills_json, $user_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Skills updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        case 'add_work_experience':
            $company_name = trim($_POST['company_name'] ?? '');
            $position = trim($_POST['position'] ?? '');
            $start_date = $_POST['start_date'] ?? '';
            $end_date = $_POST['end_date'] ?? '';
            $description = trim($_POST['description'] ?? '');

            // Insert into employment_history table
            $stmt = $conn->prepare("INSERT INTO employment_history (user_id, company_name, position, date_start, date_end, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $user_id, $company_name, $position, $start_date, $end_date, $description);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Work experience added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        case 'add_education':
            $degree = trim($_POST['degree'] ?? '');
            $institution = trim($_POST['institution'] ?? '');
            $graduation_year = trim($_POST['graduation_year'] ?? '');
            $description = trim($_POST['description'] ?? '');

            // For now, we'll store additional education in employment_history as JSON
            $education_data = json_encode([
                'type' => 'education',
                'degree' => $degree,
                'institution' => $institution,
                'graduation_year' => $graduation_year,
                'description' => $description
            ]);

            $stmt = $conn->prepare("INSERT INTO employment_history (user_id, company_name, position, date_start, description) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("issss", $user_id, $institution, $degree, $graduation_year, $education_data);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Education added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        case 'add_certification':
            $certification_name = trim($_POST['certification_name'] ?? '');
            $issuer = trim($_POST['issuer'] ?? '');
            $issue_date = $_POST['issue_date'] ?? '';
            $expiry_date = $_POST['expiry_date'] ?? '';
            $description = trim($_POST['description'] ?? '');

            // Store certification in employment_history as JSON
            $cert_data = json_encode([
                'type' => 'certification',
                'name' => $certification_name,
                'issuer' => $issuer,
                'issue_date' => $issue_date,
                'expiry_date' => $expiry_date,
                'description' => $description
            ]);

            $stmt = $conn->prepare("INSERT INTO employment_history (user_id, company_name, position, date_start, date_end, description) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssss", $user_id, $issuer, $certification_name, $issue_date, $expiry_date, $cert_data);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Certification added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        case 'remove_work_experience':
            $item_id = intval($_POST['id'] ?? 0);

            $stmt = $conn->prepare("DELETE FROM employment_history WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $item_id, $user_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Work experience removed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        case 'remove_certification':
            $item_id = intval($_POST['id'] ?? 0);

            $stmt = $conn->prepare("DELETE FROM employment_history WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $item_id, $user_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Certification removed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        case 'remove_project':
            $item_id = intval($_POST['id'] ?? 0);

            $stmt = $conn->prepare("DELETE FROM employment_history WHERE id = ? AND user_id = ?");
            $stmt->bind_param("ii", $item_id, $user_id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Project removed successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        case 'update_skills':
            $skills_json = $_POST['skills'] ?? '[]';
            $skills = json_decode($skills_json, true);

            // First, try to update existing skills record
            $stmt = $conn->prepare("UPDATE employment_history SET description = ? WHERE user_id = ? AND description LIKE '%\"type\":\"skills\"%' LIMIT 1");
            $stmt->bind_param("si", $skills_json, $user_id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Skills updated successfully']);
            } else {
                // If no existing record, insert new one
                $stmt = $conn->prepare("INSERT INTO employment_history (user_id, company_name, position, date_start, description) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("issss", $user_id, 'Skills', 'Skills', date('Y-m-d'), $skills_json);

                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'Skills added successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
                }
            }
            break;

        case 'add_reference':
            $reference_json = $_POST['reference_data'] ?? '{}';
            $reference_data = json_decode($reference_json, true);

            // Validate reference data
            if (empty($reference_data['name']) || empty($reference_data['email']) || empty($reference_data['phone'])) {
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                break;
            }

            // Insert reference into employment_history table
            $stmt = $conn->prepare("INSERT INTO employment_history (user_id, company_name, position, date_start, description) VALUES (?, ?, ?, ?, ?)");
            $company = $reference_data['company'] ?? 'Reference';
            $position = $reference_data['position'];
            $current_date = date('Y-m-d');
            
            $stmt->bind_param("issss", $user_id, $company, $position, $current_date, $reference_json);

            if ($stmt->execute()) {
                echo json_encode(['success' => true, 'message' => 'Reference added successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        case 'remove_reference':
            $reference_id = intval($_POST['id'] ?? 0);

            // Verify the reference belongs to the user
            $stmt = $conn->prepare("DELETE FROM employment_history WHERE id = ? AND user_id = ? AND description LIKE '%\"type\":\"reference\"%'");
            $stmt->bind_param("ii", $reference_id, $user_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Reference removed successfully']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Reference not found or access denied']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
